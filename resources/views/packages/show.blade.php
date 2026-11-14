<x-layouts.app title="{{ $package->name }} - TriHitaTour">
    @php
        $coverUrl = 'https://placehold.co/1200x600/00327D/ffffff?text=' . urlencode($package->name);
        if ($package->cover_image) {
            if (str_starts_with($package->cover_image, 'http')) {
                $coverUrl = $package->cover_image;
            } else {
                $coverUrl = asset('storage/' . $package->cover_image);
            }
        }
    @endphp

    <div class="bg-surface-container-lowest min-h-screen pt-24 pb-16 px-margin-mobile md:px-margin-desktop">
        <div class="max-w-container-max mx-auto space-y-8">

            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-2 text-xs md:text-sm text-outline">
                <a href="{{ route('home') }}" class="hover:text-tropical-teal transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('packages.index') }}" class="hover:text-tropical-teal transition-colors">Tour Packages</a>
                <span>/</span>
                <span class="text-on-surface font-semibold truncate max-w-xs md:max-w-md">{{ $package->name }}</span>
            </nav>

            <!-- Flash Success / Error Messages -->
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl flex items-center gap-3 shadow-sm">
                    <span class="material-symbols-outlined text-emerald-600 text-2xl">check_circle</span>
                    <div class="text-sm font-semibold">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl flex items-center gap-3 shadow-sm">
                    <span class="material-symbols-outlined text-rose-600 text-2xl">error</span>
                    <div class="text-sm font-semibold">{{ session('error') }}</div>
                </div>
            @endif

            @if(!$package->is_active)
                <div class="bg-amber-50 border border-amber-300 text-amber-900 p-4 rounded-2xl flex items-center gap-3 shadow-sm">
                    <span class="material-symbols-outlined text-amber-600 text-2xl">pause_circle</span>
                    <div>
                        <div class="font-bold text-sm">Tour Package Currently Inactive</div>
                        <div class="text-xs text-amber-800">This package is currently being prepared or temporarily paused by the agent and cannot be reserved yet.</div>
                    </div>
                </div>
            @endif

            <!-- Main Grid: Details (Left) + Booking Sticky Form (Right) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left Column (8 cols): Package Details -->
                <div class="lg:col-span-8 space-y-8">
                    
                    <!-- Cover Image Banner -->
                    <div class="relative h-72 md:h-[420px] rounded-3xl overflow-hidden shadow-lg border border-outline-variant/30 bg-surface-container">
                        <img src="{{ $coverUrl }}" alt="{{ $package->name }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                        
                        <!-- Top Badges -->
                        <div class="absolute top-4 left-4 flex gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-bold text-white shadow-md {{ $package->category === 'custom' ? 'bg-purple-600' : 'bg-oceanic-deep' }}">
                                {{ ucfirst($package->category) }}
                            </span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/90 text-oceanic-deep shadow-md">
                                {{ \App\Models\Package::REGIONS[$package->region] ?? ucfirst($package->region) . ' Bali' }}
                            </span>
                        </div>

                        <!-- Bottom Overlaid Content -->
                        <div class="absolute bottom-6 left-6 right-6 text-white space-y-2">
                            <div class="flex items-center gap-2">
                                <div class="bg-amber-500 text-white px-2.5 py-0.5 rounded-full text-xs font-bold flex items-center gap-1 shadow">
                                    <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span>{{ number_format($package->avg_rating, 1) }}</span>
                                </div>
                                <span class="text-xs text-white/80">Operated by: {{ $package->agent->agent_name ?? 'Official Partner Agent' }}</span>
                            </div>
                            <h1 class="font-display-lg text-2xl md:text-4xl font-bold tracking-tight text-shadow-subtle">
                                {{ $package->name }}
                            </h1>
                        </div>
                    </div>

                    <!-- Highlights Strip -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 bg-surface-white p-5 rounded-2xl border border-outline-variant/30 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-tropical-teal/10 text-tropical-teal flex items-center justify-center">
                                <span class="material-symbols-outlined">schedule</span>
                            </div>
                            <div>
                                <span class="text-[11px] text-outline block">Duration</span>
                                <span class="text-sm font-bold text-oceanic-deep">{{ $package->duration_days }} {{ $package->duration_days > 1 ? 'Days' : 'Day' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-tropical-teal/10 text-tropical-teal flex items-center justify-center">
                                <span class="material-symbols-outlined">group</span>
                            </div>
                            <div>
                                <span class="text-[11px] text-outline block">Max Capacity</span>
                                <span class="text-sm font-bold text-oceanic-deep">{{ $package->max_guests }} Guests</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-tropical-teal/10 text-tropical-teal flex items-center justify-center">
                                <span class="material-symbols-outlined">event_available</span>
                            </div>
                            <div>
                                <span class="text-[11px] text-outline block">D-3+ Schedules</span>
                                <span class="text-sm font-bold text-emerald-600">{{ $availableSchedules->count() }} Available</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-tropical-teal/10 text-tropical-teal flex items-center justify-center">
                                <span class="material-symbols-outlined">verified</span>
                            </div>
                            <div>
                                <span class="text-[11px] text-outline block">Tour Guide</span>
                                <span class="text-sm font-bold text-oceanic-deep">Certified</span>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="bg-surface-white p-6 md:p-8 rounded-2xl border border-outline-variant/30 shadow-sm space-y-4">
                        <h2 class="font-display-lg text-lg md:text-xl font-bold text-oceanic-deep flex items-center gap-2">
                            <span class="material-symbols-outlined text-tropical-teal">description</span>
                            <span>About This Package</span>
                        </h2>
                        <div class="text-sm md:text-base text-on-surface-variant leading-relaxed whitespace-pre-line">
                            {{ $package->description }}
                        </div>
                    </div>

                    <!-- Destinations Visited -->
                    @if($package->destinations->isNotEmpty())
                        <div class="bg-surface-white p-6 md:p-8 rounded-2xl border border-outline-variant/30 shadow-sm space-y-4">
                            <h2 class="font-display-lg text-lg md:text-xl font-bold text-oceanic-deep flex items-center gap-2">
                                <span class="material-symbols-outlined text-tropical-teal">map</span>
                                <span>Destinations Visited</span>
                            </h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($package->destinations as $dest)
                                    @php
                                        $destImg = 'https://placehold.co/400x300/00327D/ffffff?text=' . urlencode($dest->name);
                                        if ($dest->image_path) {
                                            $destImg = str_starts_with($dest->image_path, 'http') ? $dest->image_path : asset('storage/' . $dest->image_path);
                                        }
                                    @endphp
                                    <div class="flex items-center gap-3 p-3 rounded-xl border border-outline-variant/30 hover:border-tropical-teal/50 transition-colors">
                                        <img src="{{ $destImg }}" alt="{{ $dest->name }}" class="w-16 h-16 rounded-lg object-cover shrink-0 border border-outline-variant/20">
                                        <div class="min-w-0">
                                            <div class="font-bold text-sm text-oceanic-deep truncate">{{ $dest->name }}</div>
                                            <div class="text-xs text-outline flex items-center gap-1 mt-0.5">
                                                <span class="material-symbols-outlined text-xs">location_on</span>
                                                <span class="truncate">{{ $dest->location }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Informasi Agen Travel -->
                    @if($package->agent)
                        <div class="bg-surface-white p-6 md:p-8 rounded-2xl border border-outline-variant/30 shadow-sm space-y-4">
                            <h2 class="font-display-lg text-lg md:text-xl font-bold text-oceanic-deep flex items-center gap-2">
                                <span class="material-symbols-outlined text-tropical-teal">business_center</span>
                                <span>Tour Operator</span>
                            </h2>
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl bg-tropical-teal text-white flex items-center justify-center font-bold text-xl shadow">
                                    {{ substr($package->agent->agent_name, 0, 1) }}
                                </div>
                                <div class="space-y-1">
                                    <h3 class="font-bold text-base text-oceanic-deep">{{ $package->agent->agent_name }}</h3>
                                    <p class="text-xs text-on-surface-variant">{{ $package->agent->address }}</p>
                                    <div class="flex items-center gap-4 text-xs text-outline pt-1">
                                        <span>NIB: {{ $package->agent->nib_number ?? '-' }}</span>
                                        <span>•</span>
                                        <span>Fleet: {{ $package->agent->vehicle_count }} Vehicles</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column (4 cols): Booking Sticky Card -->
                <div class="lg:col-span-4 sticky top-28 space-y-6">
                    <div class="bg-surface-white p-6 md:p-8 rounded-3xl border border-outline-variant/30 shadow-xl space-y-6">
                        
                        <!-- Price Header -->
                        <div class="border-b border-outline-variant/30 pb-4">
                            <span class="text-xs text-outline block">Official Rate per Person</span>
                            <div class="flex items-baseline gap-2 mt-1">
                                <span class="font-display-lg text-3xl font-bold text-oceanic-deep" id="priceDisplay">
                                    Rp {{ number_format($package->price_per_pax, 0, ',', '.') }}
                                </span>
                                <span class="text-xs text-outline">/pax</span>
                            </div>
                        </div>

                        <!-- Booking Form -->
                        <form action="{{ route('packages.book', $package->slug) }}" method="POST" class="space-y-6" id="bookingForm">
                            @csrf

                            <!-- Section: Departure Schedule (D-3 Rule) -->
                            <div class="space-y-2">
                                <label for="package_schedule_id" class="block font-label-bold text-xs text-on-surface uppercase tracking-wider">
                                    Select Departure Schedule <span class="text-error">*</span>
                                </label>

                                @if($availableSchedules->isEmpty())
                                    <!-- Warning Alert if no schedules match D-3 rule -->
                                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl text-amber-900 space-y-2">
                                        <div class="flex items-center gap-2 font-bold text-xs">
                                            <span class="material-symbols-outlined text-amber-600 text-lg">event_busy</span>
                                            <span>No Schedules Available</span>
                                        </div>
                                        <p class="text-xs leading-relaxed">
                                            Currently there are no departure schedules available for booking. Reservations must be made at least 3 days prior to departure (D-3 rule) and subject to remaining quota.
                                        </p>
                                    </div>
                                @else
                                    <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                                        @foreach($availableSchedules as $sch)
                                            @php
                                                $scheduleDate = \Carbon\Carbon::parse($sch->start_date)->startOfDay();
                                                $daysLeft = (int) round($scheduleDate->diffInDays(now()->startOfDay()));
                                            @endphp
                                            <label class="block p-3 rounded-xl border border-outline-variant/40 hover:border-tropical-teal cursor-pointer transition-all has-[:checked]:border-tropical-teal has-[:checked]:bg-tropical-teal/5 bg-surface-container-lowest">
                                                <div class="flex items-start gap-3">
                                                    <input type="radio" name="package_schedule_id" value="{{ $sch->id }}" {{ old('package_schedule_id') == $sch->id || $loop->first ? 'checked' : '' }}
                                                        data-quota="{{ $sch->quota }}"
                                                        class="mt-1 text-tropical-teal focus:ring-tropical-teal schedule-radio">
                                                    <div class="flex-1 text-xs">
                                                        <div class="font-bold text-oceanic-deep">
                                                            {{ $sch->start_date->format('d M Y') }} - {{ $sch->end_date->format('d M Y') }}
                                                        </div>
                                                        <div class="flex items-center justify-between text-[11px] text-outline mt-1">
                                                            <span class="text-emerald-700 font-semibold">Seats Left: {{ $sch->quota }}</span>
                                                            <span class="text-amber-800 font-medium">D-{{ $daysLeft }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('package_schedule_id')
                                        <span class="text-error text-xs block">{{ $message }}</span>
                                    @enderror
                                @endif
                            </div>

                            <!-- Section: Number of Guests -->
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <label for="guest_count" class="font-label-bold text-xs text-on-surface uppercase tracking-wider">
                                        Number of Guests (Pax) <span class="text-error">*</span>
                                    </label>
                                    <span class="text-[11px] text-outline">Max. {{ $package->max_guests }} pax</span>
                                </div>
                                
                                <input type="number" name="guest_count" id="guest_count" value="{{ old('guest_count', 1) }}" min="1" max="{{ $package->max_guests }}" required
                                    {{ $availableSchedules->isEmpty() ? 'disabled' : '' }}
                                    class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow disabled:opacity-50 @error('guest_count') border-error @enderror">
                                
                                @error('guest_count')
                                    <span class="text-error text-xs block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Total Price Calculation -->
                            <div class="bg-surface-container-low p-4 rounded-xl space-y-1">
                                <div class="flex justify-between items-center text-xs text-outline">
                                    <span>Estimated Breakdown:</span>
                                    <span id="paxCountInfo">1 Pax × Rp {{ number_format($package->price_per_pax, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-sm text-oceanic-deep">Total Payable:</span>
                                    <span class="font-display-lg text-xl font-bold text-tropical-teal" id="totalPriceDisplay">
                                        Rp {{ number_format($package->price_per_pax, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Action Button -->
                            @guest
                                <a href="{{ route('login') }}" class="w-full bg-oceanic-deep hover:bg-primary-container text-white font-label-bold text-sm py-4 rounded-xl flex items-center justify-center gap-2 shadow-md transition-colors">
                                    <span class="material-symbols-outlined text-lg">login</span>
                                    <span>Login to Book</span>
                                </a>
                                <p class="text-[11px] text-outline text-center">
                                    Don't have an account? <a href="{{ route('register') }}" class="text-tropical-teal underline font-medium">Register now</a>
                                </p>
                            @else
                                @if(!$package->is_active)
                                    <div class="p-3 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl text-xs space-y-1 text-center">
                                        <div class="font-bold">Package Inactive</div>
                                        <p class="text-[11px]">This tour package is currently not open for bookings by the partner agent.</p>
                                    </div>
                                    <button type="button" disabled class="w-full bg-slate-200 text-slate-400 font-bold text-sm py-4 rounded-xl cursor-not-allowed flex items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-lg">pause_circle</span>
                                        <span>Package Inactive</span>
                                    </button>
                                @elseif(auth()->user()->role !== 'user')
                                    <div class="p-3 bg-blue-50 border border-blue-200 text-blue-900 rounded-xl text-xs space-y-1 text-center">
                                        <div class="font-bold">Account Role: {{ ucfirst(auth()->user()->role) }}</div>
                                        <p class="text-[11px]">Tour bookings can only be made by Traveler accounts (role: user).</p>
                                    </div>
                                    <button type="button" disabled class="w-full bg-slate-200 text-slate-400 font-bold text-sm py-4 rounded-xl cursor-not-allowed">
                                        Access Denied (Travelers Only)
                                    </button>
                                @elseif($availableSchedules->isEmpty())
                                    <button type="button" disabled class="w-full bg-slate-200 text-slate-400 font-bold text-sm py-4 rounded-xl cursor-not-allowed flex items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-lg">block</span>
                                        <span>No Schedules Available</span>
                                    </button>
                                @else
                                    <button type="submit" class="w-full bg-tropical-teal hover:bg-secondary text-white font-label-bold text-sm py-4 rounded-xl flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transition-all duration-200 active:scale-98">
                                        <span class="material-symbols-outlined text-lg">check_circle</span>
                                        <span>Book Tour Now</span>
                                    </button>
                                @endif
                            @endguest
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Client-side dynamic price calculator script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pricePerPax = {{ (float) $package->price_per_pax }};
            const guestInput = document.getElementById('guest_count');
            const paxCountInfo = document.getElementById('paxCountInfo');
            const totalPriceDisplay = document.getElementById('totalPriceDisplay');

            function updatePrice() {
                if (!guestInput) return;
                const count = Math.max(1, parseInt(guestInput.value) || 1);
                const total = count * pricePerPax;

                if (paxCountInfo) {
                    paxCountInfo.textContent = `${count} Pax × Rp ${new Intl.NumberFormat('id-ID').format(pricePerPax)}`;
                }
                if (totalPriceDisplay) {
                    totalPriceDisplay.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`;
                }
            }

            if (guestInput) {
                guestInput.addEventListener('input', updatePrice);
                guestInput.addEventListener('change', updatePrice);
            }
        });
    </script>
</x-layouts.app>
