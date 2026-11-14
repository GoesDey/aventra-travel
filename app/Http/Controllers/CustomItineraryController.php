<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Destination;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CustomItineraryController extends Controller
{
    /**
     * Display the Custom Itinerary builder form.
     */
    public function create(Request $request)
    {
        $destinations = Destination::orderBy('name')->get();

        $packages = Package::where('is_active', true)
            ->whereHas('agent.user', function ($query) {
                $query->where('status', 'active');
            })
            ->with(['agent', 'destinations'])
            ->orderByRaw("CASE WHEN category = 'custom' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();

        $selectedPackageId = $request->query('package_id', $packages->first()?->id);
        $baseHours         = (int) config('trihita.custom_base_hours', 8);
        $overtimeRate      = (int) config('trihita.overtime_rate_per_hour', 75000);
        $minDate           = now()->addDays(3)->toDateString();
        $maxGuests         = (int) config('trihita.max_guest_per_booking', 20);

        return view('custom-itinerary.create', compact(
            'destinations',
            'packages',
            'selectedPackageId',
            'baseHours',
            'overtimeRate',
            'minDate',
            'maxGuests'
        ));
    }

    /**
     * Store a custom itinerary booking with automatic overtime extra charge calculation.
     */
    public function store(Request $request)
    {
        $maxConfig = (int) config('trihita.max_guest_per_booking', 20);
        $minDate   = now()->addDays(3)->toDateString();

        $validated = $request->validate([
            'package_id'           => 'required|exists:packages,id',
            'travel_date'          => "required|date|after_or_equal:{$minDate}",
            'guest_count'          => "required|integer|min:1|max:{$maxConfig}",
            'destinations'         => 'required|array|min:1',
            'destinations.*.id'    => 'required|exists:destinations,id',
            'destinations.*.hours' => 'required|numeric|min:0.5|max:24',
            'special_requests'     => 'nullable|string|max:1000',
        ], [
            'package_id.required'           => 'Please select a base tour package or travel agent.',
            'package_id.exists'             => 'The selected tour package is invalid.',
            'travel_date.required'          => 'Please choose your travel date.',
            'travel_date.after_or_equal'    => 'Reservations must be made at least 3 days before departure (D-3).',
            'guest_count.required'          => 'Number of guests is required.',
            'guest_count.min'               => 'Minimum number of guests is 1 person.',
            'guest_count.max'               => "Maximum number of guests is {$maxConfig} people.",
            'destinations.required'         => 'Please select at least one destination for your custom itinerary.',
            'destinations.min'              => 'Please select at least one destination for your custom itinerary.',
            'destinations.*.id.required'    => 'Each itinerary stop must have a valid destination.',
            'destinations.*.hours.required' => 'Please enter the duration (in hours) for each selected destination.',
            'destinations.*.hours.min'      => 'Each destination duration must be at least 0.5 hours.',
        ]);

        $guestCount = (int) $validated['guest_count'];

        $package = Package::with(['agent.user'])->findOrFail($validated['package_id']);

        if (!$package->is_active) {
            throw ValidationException::withMessages([
                'package_id' => 'Booking cannot be processed because this tour package is currently inactive.',
            ]);
        }

        if (!$package->agent || !$package->agent->user || $package->agent->user->status !== 'active') {
            throw ValidationException::withMessages([
                'package_id' => 'Booking cannot be processed because the travel agent is currently inactive.',
            ]);
        }

        if ($guestCount > $package->max_guests) {
            throw ValidationException::withMessages([
                'guest_count' => "Maximum capacity per booking for this package is {$package->max_guests} guests.",
            ]);
        }

        $destinationIds = collect($validated['destinations'])->pluck('id')->unique()->all();
        $destinationMap = Destination::whereIn('id', $destinationIds)->get()->keyBy('id');

        $customDestinations = [];
        $totalHours = 0.0;

        foreach ($validated['destinations'] as $item) {
            $dest = $destinationMap->get((int) $item['id']);
            if (!$dest) {
                continue;
            }
            $hours = round((float) $item['hours'], 2);
            $totalHours += $hours;

            $customDestinations[] = [
                'id'       => $dest->id,
                'name'     => $dest->name,
                'location' => $dest->location,
                'hours'    => $hours,
            ];
        }

        $overtime = Booking::calculateOvertime($totalHours);

        $booking = DB::transaction(function () use ($package, $validated, $guestCount, $customDestinations, $overtime) {
            $pricePerPax    = (int) $package->price_per_pax;
            $baseTourTotal  = $guestCount * $pricePerPax;
            $overtimeCharge = (int) $overtime['overtime_charge'];
            $totalPrice     = $baseTourTotal + $overtimeCharge;

            $commissionRate   = (float) config('trihita.commission_rate', 10);
            $commissionAmount = (int) round(($totalPrice * $commissionRate) / 100);
            $agentEarning     = $totalPrice - $commissionAmount;

            $expiryMinutes = (int) config('trihita.booking_expiry_minutes', 60);
            $expiresAt     = now()->addMinutes($expiryMinutes);

            do {
                $bookingCode = 'TRIP-' . strtoupper(Str::random(4)) . '-' . date('Ymd') . rand(10, 99);
            } while (Booking::where('booking_code', $bookingCode)->exists());

            return Booking::create([
                'booking_code'        => $bookingCode,
                'user_id'             => auth()->id(),
                'package_id'          => $package->id,
                'package_schedule_id' => null,
                'travel_date'         => Carbon::parse($validated['travel_date'])->toDateString(),
                'guest_count'         => $guestCount,
                'total_price'         => $totalPrice,
                'is_custom'           => true,
                'total_hours'         => $overtime['total_hours'],
                'overtime_hours'      => $overtime['overtime_hours'],
                'overtime_charge'     => $overtime['overtime_charge'],
                'custom_destinations' => $customDestinations,
                'special_requests'    => $validated['special_requests'] ?? null,
                'commission_rate'     => $commissionRate,
                'commission_amount'   => $commissionAmount,
                'agent_earning'       => $agentEarning,
                'expires_at'          => $expiresAt,
                'status'              => 'menunggu_pembayaran',
            ]);
        }, 3);

        return redirect()->route('payments.show', $booking->booking_code)
            ->with('success', 'Custom itinerary booking created! Please complete your payment before the timer expires.');
    }
}

