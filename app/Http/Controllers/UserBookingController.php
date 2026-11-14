<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PackageSchedule;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class UserBookingController extends Controller
{
    /**
     * Menampilkan daftar paket wisata aktif untuk publik.
     */
    public function index(Request $request)
    {
        // Tampilkan semua paket wisata (aktif maupun belum aktif) beserta gambarnya
        $query = Package::query()
            ->with(['destinations', 'agent'])
            ->withCount(['schedules' => function ($q) {
                $q->where('quota', '>', 0)
                  ->whereDate('start_date', '>=', now()->addDays(3)->toDateString());
            }]);

        // Filter region (filter kategori sudah dihapus dari katalog publik)
        // Hanya terima region resmi (utara, selatan, timur, barat); nilai lain diabaikan
        if ($request->filled('region') && in_array($request->region, Package::regionKeys(), true)) {
            $query->where('region', $request->region);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $preference = auth()->check() ? auth()->user()->userPreference : null;

        if ($preference) {
            // Content-based scoring: hitung match_score lalu urutkan & paginate manual
            $regions    = $preference->preferred_regions ?? [];
            $categories = $preference->preferred_categories ?? [];
            $budget     = $preference->budget_max;

            $scored = $query->latest()->get()->map(function ($pkg) use ($regions, $categories, $budget) {
                $score = 0;
                if (in_array($pkg->region, $regions, true)) {
                    $score += 2;
                }
                if (in_array($pkg->category, $categories, true)) {
                    $score += 2;
                }
                if ($budget !== null && (float) $pkg->price_per_pax <= (float) $budget) {
                    $score += 1;
                }
                $pkg->match_score = $score;
                return $pkg;
            })->sortByDesc('match_score')->values();

            $perPage = 9;
            $page    = \Illuminate\Pagination\Paginator::resolveCurrentPage();
            $packages = new \Illuminate\Pagination\LengthAwarePaginator(
                $scored->forPage($page, $perPage)->values(),
                $scored->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $packages = $query->latest()->paginate(9)->withQueryString();
        }

        return view('packages.index', compact('packages', 'preference'));
    }

    /**
     * Menampilkan detail paket wisata dan jadwal perjalanan yang memenuhi syarat H-3.
     * Paket yang belum aktif tetap dapat dilihat gambar dan informasinya.
     */
    public function show(string $slug)
    {
        $package = Package::where('slug', $slug)
            ->with(['destinations', 'agent'])
            ->firstOrFail();

        // Validasi H-3: ambil jadwal dengan start_date >= 3 hari dari sekarang dan kuota > 0
        $minDateString = now()->addDays(3)->toDateString();

        $availableSchedules = $package->schedules()
            ->where('quota', '>', 0)
            ->whereDate('start_date', '>=', $minDateString)
            ->orderBy('start_date', 'asc')
            ->get();

        return view('packages.show', compact('package', 'availableSchedules'));
    }

    /**
     * Memproses pemesanan (booking) paket wisata oleh user dengan proteksi race-condition.
     */
    public function store(Request $request, string $slug)
    {
        $maxConfig = (int) config('trihita.max_guest_per_booking', 20);

        $request->validate([
            'package_schedule_id' => 'required|exists:package_schedules,id',
            'guest_count'         => "required|integer|min:1|max:{$maxConfig}",
        ], [
            'package_schedule_id.required' => 'Please select an available departure schedule.',
            'package_schedule_id.exists'   => 'Invalid travel schedule selected.',
            'guest_count.required'         => 'Guest count is required.',
            'guest_count.integer'          => 'Guest count must be an integer.',
            'guest_count.min'              => 'Guest count must be at least 1 person.',
            'guest_count.max'              => "Maximum guest count is {$maxConfig} guests.",
        ]);

        $guestCount = (int) $request->guest_count;

        // Ambil paket tanpa lock (katalog paket bersifat referensi baca)
        $package = Package::where('slug', $slug)
            ->with(['agent.user'])
            ->firstOrFail();

        // Validasi: Paket harus berstatus aktif
        if (!$package->is_active) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'package_schedule_id' => 'Booking cannot be processed because this tour package is currently inactive.',
            ]);
        }

        // Validasi: Agen pemilik paket harus berstatus 'active'
        if (!$package->agent || !$package->agent->user || $package->agent->user->status !== 'active') {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'package_schedule_id' => 'Booking cannot be processed because the tour agent is currently inactive.',
            ]);
        }

        // Validasi Kapasitas Maksimal Paket
        if ($guestCount > $package->max_guests) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'guest_count' => "Maximum capacity per booking for this package is {$package->max_guests} guests.",
            ]);
        }

        // Seluruh proses transaksi dibungkus dengan urutan lock konsisten: Schedule -> Booking -> Payment
        $booking = DB::transaction(function () use ($request, $package, $guestCount) {
            // 1. Lock schedule pertama kali (schedule -> booking)
            $schedule = PackageSchedule::where('id', $request->package_schedule_id)
                ->where('package_id', $package->id)
                ->lockForUpdate()
                ->firstOrFail();

            // 2. Validasi Jadwal Aktif & H-3 Murni (booking tepat H-3 masih diizinkan: lessThan ditolak)
            $h3Limit = now()->addDays(3)->startOfDay();
            $scheduleStartDate = Carbon::parse($schedule->start_date)->startOfDay();

            if ($scheduleStartDate->lessThan($h3Limit)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'package_schedule_id' => 'Booking failed: Bookings must be made at least 3 days before departure.',
                ]);
            }

            // 3. Validasi Ketersediaan Kuota
            if ($schedule->quota < $guestCount) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'guest_count' => "Sorry, remaining quota for this schedule is only {$schedule->quota} seats. Please adjust your guest count.",
                ]);
            }

            // 4. Lock booking pending yang sudah ada jika ada (mencegah double submit)
            $existingPending = Booking::where('user_id', auth()->id())
                ->where('package_schedule_id', $schedule->id)
                ->where('status', 'menunggu_pembayaran')
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->lockForUpdate()
                ->first();

            if ($existingPending) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'package_schedule_id' => 'You already have an active booking awaiting payment for this schedule (Code: ' . $existingPending->booking_code . '). Please complete that payment.',
                ]);
            }

            // 5. Hitung Total & Komisi di Server (Abaikan angka client)
            $pricePerPax = (int) $package->price_per_pax;
            $total = $guestCount * $pricePerPax;

            $commissionRate   = (float) config('trihita.commission_rate', 10);
            $commissionAmount = (int) round(($total * $commissionRate) / 100);
            $agentEarning     = $total - $commissionAmount;

            $expiryMinutes = (int) config('trihita.booking_expiry_minutes', 60);
            $expiresAt     = now()->addMinutes($expiryMinutes);

            // 6. Generate Unique Booking Code
            do {
                $bookingCode = 'TRIP-' . strtoupper(Str::random(4)) . '-' . date('Ymd') . rand(10, 99);
            } while (Booking::where('booking_code', $bookingCode)->exists());

            // 7. Kurangi Kuota Jadwal
            $schedule->decrement('quota', $guestCount);

            // 8. Simpan Booking (Paket Reguler: kolom kustom diisi default 0/null)
            return Booking::create([
                'booking_code'        => $bookingCode,
                'user_id'             => auth()->id(),
                'package_id'          => $package->id,
                'package_schedule_id' => $schedule->id,
                'travel_date'         => $schedule->start_date,
                'guest_count'         => $guestCount,
                'total_price'         => $total,
                'is_custom'           => false,
                'total_hours'         => null,
                'overtime_hours'      => 0,
                'overtime_charge'     => 0,
                'custom_destinations' => null,
                'special_requests'    => null,
                'commission_rate'     => $commissionRate,
                'commission_amount'   => $commissionAmount,
                'agent_earning'       => $agentEarning,
                'expires_at'          => $expiresAt,
                'status'              => 'menunggu_pembayaran',
            ]);
        }, 3);

        return redirect()->route('payments.show', $booking->booking_code)
            ->with('success', 'Booking successfully created! Please complete payment before the expiration time.');
    }
}
