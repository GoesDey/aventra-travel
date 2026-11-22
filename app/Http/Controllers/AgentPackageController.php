<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Destination;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AgentPackageController extends Controller
{
    /**
     * Dapatkan ID Agen milik user yang sedang login.
     */
    protected function getAgentId(): int
    {
        $agent = auth()->user()?->agent;

        if (!$agent) {
            abort(403, __('Access denied. Agent profile not found.'));
        }

        return $agent->id;
    }

    /**
     * Menampilkan daftar paket wisata milik agen.
     */
    public function index()
    {
        $agentId = $this->getAgentId();

        $packages = Package::where('agent_id', $agentId)
            ->with(['schedules', 'destinations'])
            ->withCount(['schedules', 'bookings'])
            ->latest()
            ->paginate(10);

        return view('agent.packages.index', compact('packages'));
    }

    /**
     * Menampilkan form pembuatan paket wisata baru.
     */
    public function create()
    {
        $agentId = $this->getAgentId();

        // Hanya destinasi aktif milik agen atau destinasi umum aktif
        $destinations = Destination::where('is_active', true)
            ->where(function ($query) use ($agentId) {
                $query->where('agent_id', $agentId)->orWhereNull('agent_id');
            })
            ->orderBy('name')
            ->get();

        return view('agent.packages.create', compact('destinations'));
    }

    /**
     * Menyimpan paket wisata baru beserta jadwal (package_schedules).
     */
    public function store(Request $request)
    {
        $agentId = $this->getAgentId();
        $tz = config('app.timezone', 'Asia/Makassar');
        $minStartDate = now($tz)->addDays(3)->toDateString();

        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'description'            => 'required|string',
            'price_per_pax'          => 'required|numeric|min:10000|max:99999999',
            'duration_days'          => 'required|integer|min:1|max:30',
            'max_guests'             => 'required|integer|min:1|max:100',
            'category'               => 'required|in:reguler,custom',
            'region'                 => ['required', Rule::in(Package::regionKeys())],
            'cover_image'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active'              => 'nullable|boolean',
            'destinations'           => 'nullable|array',
            'destinations.*'         => Rule::exists('destinations', 'id')->where('is_active', true),
            'schedules'              => 'required|array|min:1',
            'schedules.*.start_date' => "required|date|after_or_equal:{$minStartDate}",
            'schedules.*.end_date'   => 'nullable|date|after_or_equal:schedules.*.start_date',
            'schedules.*.quota'      => 'required|integer|min:1',
        ], [
            'name.required'                       => __('Package name is required.'),
            'description.required'                => __('Package description is required.'),
            'price_per_pax.required'              => __('Price per person is required.'),
            'category.required'                   => __('Package category is required.'),
            'region.required'                     => __('Destination region is required.'),
            'schedules.required'                  => __('At least one trip schedule is required.'),
            'schedules.*.start_date.required'     => __('Schedule start date is required.'),
            'schedules.*.start_date.after_or_equal' => __('Schedule start date must be at least 3 days from today (:date).', ['date' => $minStartDate]),
            'schedules.*.end_date.after_or_equal' => __('End date must be on or after start date.'),
            'schedules.*.quota.required'          => __('Guest quota is required.'),
        ]);

        $durationDays = (int) $validated['duration_days'];
        $maxGuests = (int) $validated['max_guests'];
        $scheduleErrors = [];
        $normalizedSchedules = [];

        foreach ($validated['schedules'] as $idx => $scheduleData) {
            $start = Carbon::parse($scheduleData['start_date'], $tz)->startOfDay();
            $expectedEnd = $start->copy()->addDays($durationDays - 1);

            if (!empty($scheduleData['end_date'])) {
                $givenEnd = Carbon::parse($scheduleData['end_date'], $tz)->startOfDay();
                if (!$givenEnd->equalTo($expectedEnd)) {
                    $scheduleErrors["schedules.{$idx}.end_date"] = __('Return date must match package duration (:days day(s), expected :date).', [
                        'days' => $durationDays,
                        'date' => $expectedEnd->toDateString(),
                    ]);
                }
            }

            if ((int) $scheduleData['quota'] > $maxGuests) {
                $scheduleErrors["schedules.{$idx}.quota"] = __('Schedule quota cannot exceed package max guests (:max).', [
                    'max' => $maxGuests,
                ]);
            }

            $normalizedSchedules[] = [
                'start_date' => $start->toDateString(),
                'end_date'   => $expectedEnd->toDateString(),
                'quota'      => (int) $scheduleData['quota'],
            ];
        }

        if (!empty($scheduleErrors)) {
            throw ValidationException::withMessages($scheduleErrors);
        }

        // Generate unique slug
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        while (Package::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        $coverImagePath = null;
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $request->file('cover_image')->store('packages', 'public');
        }

        DB::transaction(function () use ($validated, $slug, $coverImagePath, $agentId, $request, $normalizedSchedules) {
            $package = Package::create([
                'agent_id'      => $agentId,
                'name'          => $validated['name'],
                'slug'          => $slug,
                'description'   => $validated['description'],
                'category'      => $validated['category'],
                'region'        => $validated['region'],
                'price_per_pax' => $validated['price_per_pax'],
                'duration_days' => $validated['duration_days'],
                'max_guests'    => $validated['max_guests'],
                'cover_image'   => $coverImagePath,
                'is_active'     => $request->boolean('is_active', true),
            ]);

            if (!empty($validated['destinations'])) {
                $attachData = [];
                foreach ($validated['destinations'] as $order => $destId) {
                    $attachData[$destId] = ['visit_order' => $order + 1];
                }
                $package->destinations()->sync($attachData);
            }

            foreach ($normalizedSchedules as $scheduleData) {
                $package->schedules()->create($scheduleData);
            }
        });

        return redirect()->route('agent.packages.index')
            ->with('success', __('Tour package and schedules created successfully.'));
    }

    /**
     * Menampilkan form edit paket wisata milik agen.
     */
    public function edit(string $id)
    {
        $agentId = $this->getAgentId();

        $package = Package::where('agent_id', $agentId)
            ->with([
                'schedules' => fn ($q) => $q->withCount('bookings'),
                'destinations',
            ])
            ->findOrFail($id);

        $destinations = Destination::where('is_active', true)
            ->where(function ($query) use ($agentId) {
                $query->where('agent_id', $agentId)->orWhereNull('agent_id');
            })
            ->orderBy('name')
            ->get();

        return view('agent.packages.edit', compact('package', 'destinations'));
    }

    /**
     * Memperbarui paket wisata milik agen dan memperbarui package_schedules dengan pengaman booking.
     */
    public function update(Request $request, string $id)
    {
        $agentId = $this->getAgentId();
        $tz = config('app.timezone', 'Asia/Makassar');
        $minStartDate = now($tz)->addDays(3)->toDateString();

        $package = Package::where('agent_id', $agentId)
            ->with(['schedules' => fn ($q) => $q->withCount('bookings')])
            ->findOrFail($id);

        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'description'            => 'required|string',
            'price_per_pax'          => 'required|numeric|min:10000|max:99999999',
            'duration_days'          => 'required|integer|min:1|max:30',
            'max_guests'             => 'required|integer|min:1|max:100',
            'category'               => 'required|in:reguler,custom',
            'region'                 => ['required', Rule::in(Package::regionKeys())],
            'cover_image'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active'              => 'nullable|boolean',
            'destinations'           => 'nullable|array',
            'destinations.*'         => Rule::exists('destinations', 'id')->where('is_active', true),
            'schedules'              => 'required|array|min:1',
            'schedules.*.id'         => 'nullable|integer',
            'schedules.*.start_date' => 'required|date',
            'schedules.*.end_date'   => 'nullable|date|after_or_equal:schedules.*.start_date',
            'schedules.*.quota'      => 'required|integer|min:1',
        ], [
            'name.required'                       => __('Package name is required.'),
            'description.required'                => __('Package description is required.'),
            'price_per_pax.required'              => __('Price per person is required.'),
            'category.required'                   => __('Package category is required.'),
            'region.required'                     => __('Destination region is required.'),
            'schedules.required'                  => __('At least one trip schedule is required.'),
            'schedules.*.start_date.required'     => __('Schedule start date is required.'),
            'schedules.*.end_date.after_or_equal' => __('End date must be on or after start date.'),
            'schedules.*.quota.required'          => __('Guest quota is required.'),
        ]);

        $durationDays = (int) $validated['duration_days'];
        $maxGuests = (int) $validated['max_guests'];
        $existingSchedulesById = $package->schedules->keyBy('id');

        $submittedScheduleIds = collect($validated['schedules'])
            ->pluck('id')
            ->filter()
            ->map(fn ($val) => (int) $val)
            ->values()
            ->all();

        $scheduleErrors = [];

        // 1. Cegah penghapusan jadwal yang sudah memiliki booking (status apa pun)
        $schedulesToDelete = $package->schedules->filter(fn ($sch) => !in_array($sch->id, $submittedScheduleIds, true));
        foreach ($schedulesToDelete as $sch) {
            if ($sch->bookings_count > 0) {
                $scheduleErrors['schedules'] = __('Schedule starting on :date cannot be deleted because it already has bookings.', [
                    'date' => $sch->start_date->translatedFormat('d M Y'),
                ]);
                break;
            }
        }

        // 2. Validasi setiap baris jadwal (H+3 untuk baru/diubah, kecocokan durasi, batas kuota & kursi terpesan)
        $normalizedSchedules = [];
        foreach ($validated['schedules'] as $idx => $scheduleData) {
            $schId = !empty($scheduleData['id']) ? (int) $scheduleData['id'] : null;
            $existing = ($schId && $existingSchedulesById->has($schId)) ? $existingSchedulesById->get($schId) : null;

            $start = Carbon::parse($scheduleData['start_date'], $tz)->startOfDay();
            $expectedEnd = $start->copy()->addDays($durationDays - 1);

            $startDateChanged = !$existing || $existing->start_date->toDateString() !== $start->toDateString();

            // H+3 hanya berlaku untuk jadwal baru atau jadwal yang tanggal mulainya diubah
            if ($startDateChanged && $start->lt(Carbon::parse($minStartDate, $tz)->startOfDay())) {
                $scheduleErrors["schedules.{$idx}.start_date"] = __('Schedule start date must be at least 3 days from today (:date).', [
                    'date' => $minStartDate,
                ]);
            }

            // Validasi end_date cocok dengan duration_days (kecuali jadwal lama yang tanggalnya tidak diubah sama sekali)
            $endDateToSave = $expectedEnd->toDateString();
            if (!empty($scheduleData['end_date'])) {
                $givenEnd = Carbon::parse($scheduleData['end_date'], $tz)->startOfDay();
                $endDateUnchanged = $existing && !$startDateChanged && $existing->end_date->toDateString() === $givenEnd->toDateString();
                if (!$endDateUnchanged && !$givenEnd->equalTo($expectedEnd)) {
                    $scheduleErrors["schedules.{$idx}.end_date"] = __('Return date must match package duration (:days day(s), expected :date).', [
                        'days' => $durationDays,
                        'date' => $expectedEnd->toDateString(),
                    ]);
                }
                if ($endDateUnchanged) {
                    $endDateToSave = $givenEnd->toDateString();
                }
            }

            $quota = (int) $scheduleData['quota'];
            if ($quota > $maxGuests) {
                $scheduleErrors["schedules.{$idx}.quota"] = __('Schedule quota cannot exceed package max guests (:max).', [
                    'max' => $maxGuests,
                ]);
            }

            // Cek kursi terpesan pada jadwal yang sudah memiliki booking aktif
            if ($existing) {
                $activeBookedSeats = (int) Booking::where('package_schedule_id', $existing->id)
                    ->whereIn('status', ['menunggu_pembayaran', 'dibayar', 'berjalan'])
                    ->sum('guest_count');

                if ($activeBookedSeats > 0 && $quota < $activeBookedSeats) {
                    $scheduleErrors["schedules.{$idx}.quota"] = __('Schedule quota cannot be lower than already booked seats (:booked).', [
                        'booked' => $activeBookedSeats,
                    ]);
                }
            }

            $normalizedSchedules[] = [
                'id'         => $existing?->id,
                'start_date' => $start->toDateString(),
                'end_date'   => $endDateToSave,
                'quota'      => $quota,
            ];
        }

        if (!empty($scheduleErrors)) {
            throw ValidationException::withMessages($scheduleErrors);
        }

        DB::transaction(function () use ($validated, $package, $request, $normalizedSchedules, $schedulesToDelete) {
            $updateData = [
                'name'          => $validated['name'],
                'description'   => $validated['description'],
                'category'      => $validated['category'],
                'region'        => $validated['region'],
                'price_per_pax' => $validated['price_per_pax'],
                'duration_days' => $validated['duration_days'],
                'max_guests'    => $validated['max_guests'],
                'is_active'     => $request->boolean('is_active', true),
            ];

            if ($package->name !== $validated['name']) {
                $slug = Str::slug($validated['name']);
                $originalSlug = $slug;
                $counter = 1;
                while (Package::where('slug', $slug)->where('id', '!=', $package->id)->exists()) {
                    $slug = "{$originalSlug}-{$counter}";
                    $counter++;
                }
                $updateData['slug'] = $slug;
            }

            if ($request->hasFile('cover_image')) {
                if ($package->cover_image && Storage::disk('public')->exists($package->cover_image)) {
                    Storage::disk('public')->delete($package->cover_image);
                }
                $updateData['cover_image'] = $request->file('cover_image')->store('packages', 'public');
            }

            $package->update($updateData);

            if (isset($validated['destinations'])) {
                $attachData = [];
                foreach ($validated['destinations'] as $order => $destId) {
                    $attachData[$destId] = ['visit_order' => $order + 1];
                }
                $package->destinations()->sync($attachData);
            } else {
                $package->destinations()->detach();
            }

            // Hapus hanya jadwal tanpa booking
            $deletableIds = $schedulesToDelete
                ->filter(fn ($sch) => $sch->bookings_count === 0)
                ->pluck('id')
                ->all();

            if (!empty($deletableIds)) {
                $package->schedules()->whereIn('id', $deletableIds)->delete();
            }

            foreach ($normalizedSchedules as $scheduleData) {
                if (!empty($scheduleData['id'])) {
                    $package->schedules()->where('id', $scheduleData['id'])->update([
                        'start_date' => $scheduleData['start_date'],
                        'end_date'   => $scheduleData['end_date'],
                        'quota'      => $scheduleData['quota'],
                    ]);
                } else {
                    $package->schedules()->create([
                        'start_date' => $scheduleData['start_date'],
                        'end_date'   => $scheduleData['end_date'],
                        'quota'      => $scheduleData['quota'],
                    ]);
                }
            }
        });

        return redirect()->route('agent.packages.index')
            ->with('success', __('Tour package and schedules updated successfully.'));
    }

    /**
     * Menghapus paket wisata milik agen (ditolak jika pernah memiliki booking).
     */
    public function destroy(string $id)
    {
        $agentId = $this->getAgentId();

        $package = Package::where('agent_id', $agentId)->findOrFail($id);

        if ($package->bookings()->exists()) {
            return redirect()->route('agent.packages.index')
                ->with('error', __('This package cannot be deleted because it has existing booking history. Please deactivate it instead.'));
        }

        if ($package->cover_image && Storage::disk('public')->exists($package->cover_image)) {
            Storage::disk('public')->delete($package->cover_image);
        }

        $package->delete();

        return redirect()->route('agent.packages.index')
            ->with('success', __('Tour package deleted successfully.'));
    }
}
