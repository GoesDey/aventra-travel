<?php

namespace App\Livewire\Customer;

use App\Models\Agent;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class CustomItineraryBuilder extends Component
{
    // Step 1: Selected Region
    public string $selectedRegion = 'selatan';

    // Step 2: Selected Partner Agent
    public ?int $selectedAgentId = null;

    // Step 3: Start Point & Trip Schedule
    public string $startPoint = '';
    public ?float $startLat = -8.670458;
    public ?float $startLng = 115.262512;
    public string $travelDate = '';
    public int $guestCount = 1;

    // Step 4: Selected Destinations & Route Stops
    /**
     * @var array<int, array{id: int, name: string, location: string, latitude: float, longitude: float, stay_hours: float, leg_distance_km: float, leg_travel_hours: float}>
     */
    public array $stops = [];

    // Optional user notes / requests
    public string $specialRequests = '';

    // Route Metrics
    public float $totalDistanceKm = 0.0;
    public float $totalTravelTime = 0.0;
    public float $totalStayTime = 0.0;
    public float $totalHours = 0.0;

    // Config cache
    public int $baseHours = 8;
    public int $overtimeRate = 75000;
    public int $maxGuests = 20;
    public string $minDate = '';

    protected function rules(): array
    {
        $minDate = now()->addDays(3)->toDateString();
        $maxGuests = (int) config('trihita.max_guest_per_booking', 20);

        return [
            'selectedRegion'  => 'required|in:utara,selatan,timur,barat',
            'selectedAgentId' => 'required|exists:agents,id',
            'startPoint'      => 'required|string|min:5|max:255',
            'travelDate'      => "required|date|after_or_equal:{$minDate}",
            'guestCount'      => "required|integer|min:1|max:{$maxGuests}",
            'stops'           => 'required|array|min:1',
            'stops.*.id'      => 'required|exists:destinations,id',
            'stops.*.stay_hours' => 'required|numeric|min:0.5|max:12',
            'specialRequests' => 'nullable|string|max:1000',
        ];
    }

    protected function messages(): array
    {
        return [
            'selectedRegion.required'   => __('Please select a Bali region for your custom tour.'),
            'selectedAgentId.required'  => __('Please select an authorized travel agent operating in this region.'),
            'startPoint.required'       => __('Please provide a pick-up / starting address.'),
            'travelDate.required'       => __('Please choose your travel date.'),
            'travelDate.after_or_equal' => __('Custom trip reservations must be made at least 3 days prior to departure (D-3).'),
            'guestCount.required'       => __('Please enter the number of guests.'),
            'guestCount.min'            => __('Guest count must be at least 1 person.'),
            'stops.required'            => __('Please select at least one destination to visit.'),
            'stops.min'                 => __('Please select at least one destination to visit.'),
            'stops.*.stay_hours.min'    => __('Minimum stay duration is 0.5 hours per destination.'),
        ];
    }

    public function mount(?string $initialRegion = null, ?int $initialPackageId = null): void
    {
        $this->baseHours    = (int) config('trihita.custom_base_hours', 8);
        $this->overtimeRate = (int) config('trihita.overtime_rate_per_hour', 75000);
        $this->maxGuests    = (int) config('trihita.max_guest_per_booking', 20);
        $this->minDate      = now()->addDays(3)->toDateString();
        $this->travelDate   = $this->minDate;
        $this->guestCount   = 1;

        if ($initialPackageId) {
            $pkg = Package::find($initialPackageId);
            if ($pkg && in_array($pkg->region, Package::regionKeys(), true)) {
                $this->selectedRegion = $pkg->region;
                $this->selectedAgentId = $pkg->agent_id;
            }
        } elseif ($initialRegion && in_array($initialRegion, Package::regionKeys(), true)) {
            $this->selectedRegion = $initialRegion;
        }

        $this->initRegionData();
    }

    /**
     * Memuat data agen dan destinasi sesuai region yang dipilih.
     */
    public function initRegionData(): void
    {
        // 1. Ambil agen yang melayani region ini
        $agents = $this->getAvailableAgentsProperty();

        if ($agents->isNotEmpty()) {
            if (!$this->selectedAgentId || !$agents->contains('id', $this->selectedAgentId)) {
                $this->selectedAgentId = $agents->first()->id;
            }
            $agent = $agents->firstWhere('id', $this->selectedAgentId) ?? $agents->first();
            $this->startPoint = $agent->address;
            $this->startLat   = $agent->latitude ?? -8.670458;
            $this->startLng   = $agent->longitude ?? 115.262512;
        }

        // 2. Ambil 2 destinasi default di region ini
        $availableDests = $this->getAvailableDestinationsProperty();
        $this->stops = [];

        foreach ($availableDests->take(2) as $dest) {
            $defaultHours = round(max(30, (int) ($dest->default_stay_minutes ?: 120)) / 60, 1);
            $this->stops[] = [
                'id'                    => $dest->id,
                'name'                  => $dest->name,
                'location'              => $dest->location,
                'latitude'              => (float) $dest->latitude,
                'longitude'             => (float) $dest->longitude,
                'stay_hours'            => $defaultHours,
                'requires_sea_crossing' => (bool) $dest->requires_sea_crossing,
                'leg_distance_km'       => 0.0,
                'leg_travel_hours'      => 0.0,
            ];
        }

        $this->recalculateRoute();
    }

    /**
     * Ketika user mengganti region.
     */
    public function updatedSelectedRegion(string $region): void
    {
        if (!in_array($region, Package::regionKeys(), true)) {
            $this->selectedRegion = 'selatan';
        }

        $this->initRegionData();
        $this->dispatchMapUpdate();
    }

    /**
     * Pilih agen travel secara eksplisit.
     */
    public function selectAgent(int $agentId): void
    {
        $this->selectedAgentId = $agentId;
        $this->updatedSelectedAgentId($agentId);
    }

    /**
     * Ketika user mengganti agen travel.
     */
    public function updatedSelectedAgentId($agentId): void
    {
        $agent = Agent::find((int) $agentId);
        if ($agent) {
            $this->startPoint = $agent->address;
            $this->startLat   = (float) ($agent->latitude ?? -8.670458);
            $this->startLng   = (float) ($agent->longitude ?? 115.262512);
            $this->recalculateRoute();
            $this->dispatchMapUpdate();
        }
    }

    /**
     * Reaktivitas koordinat titik awal (Origin) dari interaksi peta atau Livewire $wire.set().
     */
    public function updatedStartLat(): void
    {
        $this->recalculateRoute();
    }

    public function updatedStartLng(): void
    {
        $this->recalculateRoute();
    }

    public function updatedStartPoint(): void
    {
        $this->dispatchMapUpdate();
    }

    /**
     * Update titik koordinat awal secara atomik saat marker di-drag di peta Leaflet.
     */
    public function updateStartCoordinates(float $lat, float $lng, ?string $address = null): void
    {
        $this->startLat = $lat;
        $this->startLng = $lng;
        if ($address) {
            $this->startPoint = $address;
        }
        $this->recalculateRoute();
    }

    /**
     * Tambah destinasi ke urutan rute perjalanan.
     */
    public function addDestination(int $destId): void
    {
        $dest = Destination::where('region', $this->selectedRegion)
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->find($destId);
        if (!$dest) {
            return;
        }

        // Cek apakah sudah ada di list stops
        $exists = collect($this->stops)->contains('id', $destId);
        if ($exists) {
            return;
        }

        $defaultHours = round(max(30, (int) ($dest->default_stay_minutes ?: 120)) / 60, 1);

        $this->stops[] = [
            'id'                    => $dest->id,
            'name'                  => $dest->name,
            'location'              => $dest->location,
            'latitude'              => (float) $dest->latitude,
            'longitude'             => (float) $dest->longitude,
            'stay_hours'            => $defaultHours,
            'requires_sea_crossing' => (bool) $dest->requires_sea_crossing,
            'leg_distance_km'       => 0.0,
            'leg_travel_hours'      => 0.0,
        ];

        $this->recalculateRoute();
        $this->dispatchMapUpdate();
    }

    /**
     * Toggle penambahan / penghapusan destinasi dari interaksi kartu atau marker peta.
     */
    public function toggleDestination(int $destId): void
    {
        $index = collect($this->stops)->search(fn ($s) => $s['id'] === $destId);
        if ($index !== false) {
            if (count($this->stops) > 1) {
                $this->removeStop($index);
            }
        } else {
            $this->addDestination($destId);
        }
    }

    /**
     * Hapus destinasi dari urutan rute.
     */
    public function removeStop(int $index): void
    {
        if (isset($this->stops[$index]) && count($this->stops) > 1) {
            array_splice($this->stops, $index, 1);
            $this->recalculateRoute();
            $this->dispatchMapUpdate();
        }
    }

    /**
     * Naikkan urutan destinasi.
     */
    public function moveStopUp(int $index): void
    {
        if ($index > 0 && isset($this->stops[$index])) {
            $temp = $this->stops[$index - 1];
            $this->stops[$index - 1] = $this->stops[$index];
            $this->stops[$index] = $temp;
            $this->recalculateRoute();
            $this->dispatchMapUpdate();
        }
    }

    /**
     * Turunkan urutan destinasi.
     */
    public function moveStopDown(int $index): void
    {
        if ($index < count($this->stops) - 1 && isset($this->stops[$index])) {
            $temp = $this->stops[$index + 1];
            $this->stops[$index + 1] = $this->stops[$index];
            $this->stops[$index] = $temp;
            $this->recalculateRoute();
            $this->dispatchMapUpdate();
        }
    }

    /**
     * Perbarui durasi stay di salah satu stop.
     */
    public function updateStayHours(int $index, float $hours): void
    {
        if (isset($this->stops[$index])) {
            $this->stops[$index]['stay_hours'] = max(0.5, min(12.0, round($hours, 1)));
            $this->recalculateRoute();
        }
    }

    /**
     * Hitung ulang estimasi jarak (km), waktu tempuh (jam), dan total waktu.
     */
    public function recalculateRoute(): void
    {
        $currentLat = $this->startLat ?? -8.670458;
        $currentLng = $this->startLng ?? 115.262512;

        $totalDist = 0.0;
        $totalDrive = 0.0;
        $totalStay = 0.0;

        foreach ($this->stops as $index => &$stop) {
            $dist = self::calculateHaversineDistance(
                $currentLat,
                $currentLng,
                $stop['latitude'],
                $stop['longitude']
            );

            $drive = self::estimateDrivingTimeHours($dist);

            $stop['leg_distance_km']  = $dist;
            $stop['leg_travel_hours'] = $drive;

            $totalDist  += $dist;
            $totalDrive += $drive;
            $totalStay  += (float) $stop['stay_hours'];

            // Titik awal leg berikutnya adalah destinasi saat ini
            $currentLat = $stop['latitude'];
            $currentLng = $stop['longitude'];
        }
        unset($stop);

        $this->totalDistanceKm = round($totalDist, 1);
        $this->totalTravelTime = round($totalDrive, 2);
        $this->totalStayTime   = round($totalStay, 2);
        $this->totalHours      = round($this->totalTravelTime + $this->totalStayTime, 2);
    }

    /**
     * Dispatch event ke JavaScript agar Leaflet Map merender ulang marker & rute jalan.
     */
    public function dispatchMapUpdate(): void
    {
        $this->dispatch('itinerary-map-updated', [
            'startPoint' => [
                'name' => $this->startPoint,
                'lat'  => $this->startLat ?? -8.670458,
                'lng'  => $this->startLng ?? 115.262512,
            ],
            'stops' => $this->stops,
        ]);
    }

    /**
     * Rumus Haversine dikalikan faktor jalan berliku pulau Bali (1.35x).
     */
    public static function calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadiusKm = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadiusKm * $c * 1.35, 1);
    }

    /**
     * Estimasi waktu berkendara (jam) dengan kecepatan rata-rata turis Bali ~30 km/jam + 5 menit buffer.
     */
    public static function estimateDrivingTimeHours(float $distanceKm): float
    {
        if ($distanceKm <= 0.1) {
            return 0.0;
        }
        return round(($distanceKm / 30) + 0.08, 2);
    }

    // --- Computed Properties ---

    public function getRegionsProperty(): array
    {
        return [
            'utara' => [
                'key'         => 'utara',
                'name'        => 'North Bali',
                'title'       => 'Lakes, Waterfalls & Mount Batur',
                'badge'       => 'Mountain & Waterfalls',
                'icon'        => 'landscape',
                'description' => 'Cool mountain breeze, Lake Beratan Bedugul, Kintamani caldera, and majestic waterfalls.',
            ],
            'selatan' => [
                'key'         => 'selatan',
                'name'        => 'South Bali',
                'title'       => 'Beaches, Culture & Iconic Temples',
                'badge'       => 'Coastal & Heritage',
                'icon'        => 'beach_access',
                'description' => 'Ubud cultural rice terraces, Uluwatu cliff temple, Jimbaran seafood sunset, and Benoa harbor.',
            ],
            'timur' => [
                'key'         => 'timur',
                'name'        => 'East Bali',
                'title'       => 'Nusa Penida, Tropical Islands & Snorkeling',
                'badge'       => 'Islands & Marine',
                'icon'        => 'sailing',
                'description' => 'Kelingking T-Rex cliff, Diamond beach, crystal bay snorkeling, and exotic island sanctuaries.',
            ],
            'barat' => [
                'key'         => 'barat',
                'name'        => 'West Bali',
                'title'       => 'National Parks, Reefs & Sunset Coast',
                'badge'       => 'Nature Reserve',
                'icon'        => 'forest',
                'description' => 'Tanah Lot sea temple, West Bali National Park rainforest, and Menjangan coral wall diving.',
            ],
        ];
    }

    public function getAvailableAgentsProperty()
    {
        return Agent::whereHas('user', fn ($q) => $q->where('status', 'active'))
            ->where(function ($query) {
                $query->whereJsonContains('operating_regions', $this->selectedRegion)
                    ->orWhereHas('packages', fn ($p) => $p->where('region', $this->selectedRegion)->where('is_active', true));
            })
            ->with(['packages' => fn ($p) => $p->where('is_active', true)])
            ->get();
    }

    public function getAvailableDestinationsProperty()
    {
        return Destination::where('region', $this->selectedRegion)
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('name')
            ->get();
    }

    public function getHasSeaCrossingProperty(): bool
    {
        return collect($this->stops)->contains(fn ($stop) => !empty($stop['requires_sea_crossing']));
    }

    public function getSelectedAgentProperty(): ?Agent
    {
        return Agent::find($this->selectedAgentId);
    }

    public function getBasePricePerPaxProperty(): int
    {
        if (!$this->selectedAgent) {
            return 350000;
        }

        // Cari paket custom atau paket di region ini milik agen bersangkutan
        $pkg = $this->selectedAgent->packages
            ->where('region', $this->selectedRegion)
            ->sortBy('price_per_pax')
            ->first();

        if (!$pkg) {
            $pkg = $this->selectedAgent->packages->sortBy('price_per_pax')->first();
        }

        return $pkg ? (int) $pkg->price_per_pax : 350000;
    }

    public function getOvertimeCalculationProperty(): array
    {
        return Booking::calculateOvertime($this->totalHours);
    }

    public function getBaseTourCostProperty(): int
    {
        $guests = max(1, (int) ($this->guestCount ?? 1));
        return $guests * $this->basePricePerPax;
    }

    public function getOvertimeChargeProperty(): int
    {
        return (int) $this->overtimeCalculation['overtime_charge'];
    }

    public function getGrandTotalProperty(): int
    {
        return $this->baseTourCost + $this->overtimeCharge;
    }

    /**
     * Submit Form & Simpan Custom Itinerary ke database.
     */
    public function submitBooking()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $this->validate();

        $agent = Agent::with(['packages' => fn ($p) => $p->where('is_active', true)])->findOrFail($this->selectedAgentId);

        // Pilih paket acuan agen untuk integritas foreign key booking
        $package = $agent->packages->where('region', $this->selectedRegion)->first()
            ?? $agent->packages->first()
            ?? Package::where('is_active', true)->firstOrFail();

        $overtime = $this->overtimeCalculation;
        $totalPrice = $this->grandTotal;
        $guestCount = max(1, (int) ($this->guestCount ?? 1));

        $commissionRate   = (float) config('trihita.commission_rate', 10);
        $commissionAmount = (int) round(($totalPrice * $commissionRate) / 100);
        $agentEarning     = $totalPrice - $commissionAmount;

        $expiryMinutes = (int) config('trihita.booking_expiry_minutes', 60);
        $expiresAt     = now()->addMinutes($expiryMinutes);

        do {
            $bookingCode = 'TRIP-' . strtoupper(Str::random(4)) . '-' . date('Ymd') . rand(10, 99);
        } while (Booking::where('booking_code', $bookingCode)->exists());

        $booking = DB::transaction(function () use (
            $bookingCode,
            $package,
            $totalPrice,
            $guestCount,
            $overtime,
            $commissionRate,
            $commissionAmount,
            $agentEarning,
            $expiresAt
        ) {
            return Booking::create([
                'booking_code'        => $bookingCode,
                'user_id'             => auth()->id(),
                'package_id'          => $package->id,
                'package_schedule_id' => null,
                'travel_date'         => Carbon::parse($this->travelDate)->toDateString(),
                'guest_count'         => $guestCount,
                'start_point'         => $this->startPoint,
                'travel_time_hours'   => $this->totalTravelTime,
                'stay_time_hours'     => $this->totalStayTime,
                'travel_distance_km'  => $this->totalDistanceKm,
                'total_hours'         => $overtime['total_hours'],
                'overtime_hours'      => $overtime['overtime_hours'],
                'overtime_charge'     => $overtime['overtime_charge'],
                'total_price'         => $totalPrice,
                'is_custom'           => true,
                'custom_destinations' => $this->stops,
                'special_requests'    => !empty($this->specialRequests) ? $this->specialRequests : null,
                'commission_rate'     => $commissionRate,
                'commission_amount'   => $commissionAmount,
                'agent_earning'       => $agentEarning,
                'expires_at'          => $expiresAt,
                'status'              => 'menunggu_pembayaran',
            ]);
        });

        session()->flash('success', 'Custom itinerary successfully created! Please complete payment before time expires.');

        return redirect()->route('payments.show', $booking->booking_code);
    }

    public function render()
    {
        return view('livewire.customer.custom-itinerary-builder', [
            'regions'               => $this->getRegionsProperty(),
            'availableAgents'       => $this->getAvailableAgentsProperty(),
            'availableDestinations' => $this->getAvailableDestinationsProperty(),
            'hasSeaCrossing'        => $this->getHasSeaCrossingProperty(),
            'selectedAgent'         => $this->getSelectedAgentProperty(),
            'basePricePerPax'       => $this->getBasePricePerPaxProperty(),
            'overtimeCalc'          => $this->getOvertimeCalculationProperty(),
            'baseTourCost'          => $this->getBaseTourCostProperty(),
            'overtimeCharge'        => $this->getOvertimeChargeProperty(),
            'grandTotal'            => $this->getGrandTotalProperty(),
        ]);
    }
}

