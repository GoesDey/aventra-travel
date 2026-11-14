<x-layouts.app title="Build Custom Itinerary - TriHitaTour">
    @php
        $packagesJson = $packages->map(fn($p) => [
            'id'            => $p->id,
            'name'          => $p->name,
            'category'      => $p->category,
            'region'        => config('trihita.regions.' . $p->region, ucfirst($p->region) . ' Bali'),
            'price_per_pax' => (int) $p->price_per_pax,
            'max_guests'    => (int) $p->max_guests,
            'agent_name'    => $p->agent->company_name ?? 'Official Partner Agent',
        ])->values();

        $destinationsJson = $destinations->map(fn($d) => [
            'id'          => $d->id,
            'name'        => $d->name,
            'location'    => $d->location,
            'description' => $d->description,
        ])->values();

        $initialStops = old('destinations', [
            ['id' => $destinations->first()?->id ?? '', 'hours' => 3],
            ['id' => $destinations->skip(1)->first()?->id ?? ($destinations->first()?->id ?? ''), 'hours' => 3],
        ]);
    @endphp

    <div class="min-h-screen bg-surface-container-lowest pt-24 pb-16 px-margin-mobile md:px-margin-desktop"
         x-data="{
            baseHours: {{ $baseHours }},
            overtimeRate: {{ $overtimeRate }},
            packages: {{ Js::from($packagesJson) }},
            destinations: {{ Js::from($destinationsJson) }},
            selectedPackageId: {{ Js::from((int) old('package_id', $selectedPackageId)) }},
            guestCount: {{ (int) old('guest_count', 2) }},
            travelDate: {{ Js::from(old('travel_date', $minDate)) }},
            stops: {{ Js::from(array_values($initialStops)) }},

            get selectedPackage() {
                return this.packages.find(p => Number(p.id) === Number(this.selectedPackageId)) || this.packages[0] || null;
            },
            get totalHours() {
                const sum = this.stops.reduce((acc, stop) => {
                    const val = parseFloat(stop.hours);
                    return acc + (isNaN(val) || val < 0 ? 0 : val);
                }, 0);
                return Math.round(sum * 100) / 100;
            },
            get isOvertime() {
                return this.totalHours > this.baseHours;
            },
            get overtimeHours() {
                if (!this.isOvertime) return 0;
                return Math.ceil(this.totalHours - this.baseHours);
            },
            get overtimeCharge() {
                return this.overtimeHours * this.overtimeRate;
            },
            get baseTourCost() {
                if (!this.selectedPackage) return 0;
                const guests = Math.max(1, parseInt(this.guestCount || 1, 10));
                return guests * this.selectedPackage.price_per_pax;
            },
            get grandTotal() {
                return this.baseTourCost + this.overtimeCharge;
            },
            addStop() {
                const defaultId = this.destinations.length > 0 ? this.destinations[0].id : '';
                this.stops.push({ id: defaultId, hours: 2 });
            },
            removeStop(index) {
                if (this.stops.length > 1) {
                    this.stops.splice(index, 1);
                }
            },
            formatRupiah(value) {
                return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
            }
         }">
        <div class="max-w-container-max mx-auto space-y-6">

            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-2 text-xs md:text-sm text-outline">
                <a href="{{ route('home') }}" class="hover:text-tropical-teal transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('packages.index') }}" class="hover:text-tropical-teal transition-colors">Tour Packages</a>
                <span>/</span>
                <span class="text-on-surface font-semibold">Custom Itinerary</span>
            </nav>

            <!-- Header -->
            <div class="mb-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-tropical-teal/10 text-tropical-teal mb-2">
                    <span class="material-symbols-outlined text-sm">tune</span>
                    <span>Tailor-Made Bali Experience</span>
                </span>
                <h1 class="font-display-lg text-2xl md:text-4xl font-bold text-oceanic-deep tracking-tight">
                    Build Your Custom Itinerary
                </h1>
                <p class="mt-2 text-sm md:text-base text-on-surface-variant max-w-2xl leading-relaxed">
                    Select your favorite Bali destinations and set how long you want to spend at each stop. Standard charter includes up to <strong>{{ $baseHours }} hours</strong>, with transparent overtime at <strong>Rp {{ number_format($overtimeRate, 0, ',', '.') }}/hour</strong> (rounded up).
                </p>
            </div>

            <!-- Validation Errors Alert -->
            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl shadow-sm">
                    <div class="flex items-center gap-2 font-bold text-sm mb-1.5">
                        <span class="material-symbols-outlined text-rose-600 text-lg">error</span>
                        <span>Please review the following fields:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('custom-itinerary.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                @csrf

                <!-- Left Column: Form Inputs (7 Cols) -->
                <div class="lg:col-span-7 space-y-6">

                    <!-- Step 1: Base Package / Travel Agent & Trip Details -->
                    <div class="bg-surface-white rounded-2xl p-6 md:p-8 shadow-sm border border-outline-variant/30 space-y-6">
                        <div class="flex items-center gap-3 border-b border-outline-variant/20 pb-4">
                            <div class="w-9 h-9 rounded-xl bg-oceanic-deep text-white flex items-center justify-center font-bold text-sm shrink-0">1</div>
                            <div>
                                <h2 class="font-display-lg text-base md:text-lg font-bold text-oceanic-deep">Select Base Charter &amp; Travel Details</h2>
                                <p class="text-xs text-on-surface-variant mt-0.5">Choose a partner travel package, departure date (min. D-3), and party size.</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label for="package_id" class="block font-label-bold text-xs text-on-surface uppercase tracking-wider mb-2">
                                    Base Tour Package &amp; Partner Agent <span class="text-error">*</span>
                                </label>
                                <select id="package_id"
                                        name="package_id"
                                        x-model.number="selectedPackageId"
                                        required
                                        class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-4 py-3 text-sm font-medium text-on-surface focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow">
                                    @foreach($packages as $pkg)
                                        <option value="{{ $pkg->id }}">
                                            {{ $pkg->name }} — {{ config('trihita.regions.' . $pkg->region, ucfirst($pkg->region) . ' Bali') }} (Rp {{ number_format($pkg->price_per_pax, 0, ',', '.') }}/pax • Max {{ $pkg->max_guests }} guests)
                                        </option>
                                    @endforeach
                                </select>
                                <template x-if="selectedPackage">
                                    <p class="mt-2 text-xs text-on-surface-variant flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-sm text-tropical-teal">verified_user</span>
                                        <span>Operated by <strong x-text="selectedPackage.agent_name" class="text-on-surface"></strong> • Max capacity: <strong x-text="selectedPackage.max_guests + ' guests'" class="text-on-surface"></strong></span>
                                    </p>
                                </template>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="travel_date" class="block font-label-bold text-xs text-on-surface uppercase tracking-wider mb-2">
                                        Travel Date (Min. D-3) <span class="text-error">*</span>
                                    </label>
                                    <input type="date"
                                           id="travel_date"
                                           name="travel_date"
                                           min="{{ $minDate }}"
                                           x-model="travelDate"
                                           required
                                           class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-4 py-3 text-sm font-medium text-on-surface focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow">
                                </div>

                                <div>
                                    <label for="guest_count" class="block font-label-bold text-xs text-on-surface uppercase tracking-wider mb-2">
                                        Number of Guests <span class="text-error">*</span>
                                    </label>
                                    <input type="number"
                                           id="guest_count"
                                           name="guest_count"
                                           min="1"
                                           :max="selectedPackage ? selectedPackage.max_guests : {{ $maxGuests }}"
                                           x-model.number="guestCount"
                                           required
                                           class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-4 py-3 text-sm font-medium text-on-surface focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Destination Stops & Duration Builder -->
                    <div class="bg-surface-white rounded-2xl p-6 md:p-8 shadow-sm border border-outline-variant/30 space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-outline-variant/20 pb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-oceanic-deep text-white flex items-center justify-center font-bold text-sm shrink-0">2</div>
                                <div>
                                    <h2 class="font-display-lg text-base md:text-lg font-bold text-oceanic-deep">Choose Destinations &amp; Duration per Stop</h2>
                                    <p class="text-xs text-on-surface-variant mt-0.5">Add destinations and specify how many hours you plan to spend at each location.</p>
                                </div>
                            </div>
                            <button type="button"
                                    @click="addStop()"
                                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-tropical-teal/10 hover:bg-tropical-teal/20 text-tropical-teal font-label-bold text-xs transition-colors cursor-pointer shrink-0">
                                <span class="material-symbols-outlined text-base">add_location_alt</span>
                                <span>Add Destination Stop</span>
                            </button>
                        </div>

                        <!-- Dynamic Stops List -->
                        <div class="space-y-4">
                            <template x-for="(stop, index) in stops" :key="index">
                                <div class="p-4 rounded-xl bg-surface-container-low/60 border border-outline-variant/30 flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                                    <div class="flex items-center gap-2.5 sm:w-8 shrink-0">
                                        <span class="w-7 h-7 rounded-full bg-oceanic-deep/10 text-oceanic-deep font-bold text-xs flex items-center justify-center" x-text="index + 1"></span>
                                        <span class="sm:hidden text-xs font-bold text-oceanic-deep">Stop #<span x-text="index + 1"></span></span>
                                    </div>

                                    <!-- Destination Select -->
                                    <div class="flex-grow">
                                        <label class="block font-label-bold text-[11px] uppercase tracking-wider text-outline mb-1">
                                            Destination
                                        </label>
                                        <select :name="`destinations[${index}][id]`"
                                                x-model.number="stop.id"
                                                required
                                                class="w-full bg-surface-white border border-outline-variant/40 rounded-lg px-3.5 py-2.5 text-sm font-medium text-on-surface focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow">
                                            <template x-for="dest in destinations" :key="dest.id">
                                                <option :value="dest.id" x-text="`${dest.name} (${dest.location})`"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <!-- Duration Hours Input -->
                                    <div class="w-full sm:w-36 shrink-0">
                                        <label class="block font-label-bold text-[11px] uppercase tracking-wider text-outline mb-1">
                                            Duration (Hours)
                                        </label>
                                        <div class="relative">
                                            <input type="number"
                                                   step="0.5"
                                                   min="0.5"
                                                   max="24"
                                                   :name="`destinations[${index}][hours]`"
                                                   x-model.number="stop.hours"
                                                   required
                                                   class="w-full bg-surface-white border border-outline-variant/40 rounded-lg px-3.5 py-2.5 pr-12 text-sm font-bold text-oceanic-deep focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow">
                                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-outline">hrs</span>
                                        </div>
                                    </div>

                                    <!-- Remove Stop Button -->
                                    <div class="flex items-end sm:pt-5 shrink-0">
                                        <button type="button"
                                                @click="removeStop(index)"
                                                :disabled="stops.length <= 1"
                                                :class="stops.length <= 1 ? 'opacity-30 cursor-not-allowed text-outline' : 'hover:bg-rose-100 text-error cursor-pointer'"
                                                class="p-2 rounded-lg transition-colors"
                                                title="Remove Stop">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Live Duration Progress Bar inside Form -->
                        <div class="p-4 rounded-xl border transition-colors"
                             :class="isOvertime ? 'bg-amber-50/90 border-amber-300 text-amber-900' : 'bg-emerald-50/90 border-emerald-200 text-emerald-900'">
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-lg" :class="isOvertime ? 'text-amber-600' : 'text-emerald-600'" x-text="isOvertime ? 'warning' : 'check_circle'"></span>
                                    <span class="text-xs font-bold uppercase tracking-wider" x-text="isOvertime ? 'Overtime Activated (> ' + baseHours + ' Hours)' : 'Within ' + baseHours + ' Hours Standard Limit'"></span>
                                </div>
                                <span class="text-sm font-extrabold">
                                    Total: <span x-text="totalHours"></span> / <span x-text="baseHours"></span> Hours
                                </span>
                            </div>
                            <p class="text-xs" x-show="!isOvertime">
                                Great! Your custom route is within the standard <strong x-text="baseHours"></strong>-hour allowance. <strong>No overtime extra charge (Rp 0)</strong> will be added.
                            </p>
                            <p class="text-xs" x-show="isOvertime" style="display: none;">
                                Your itinerary exceeds <strong x-text="baseHours"></strong> hours by <strong x-text="Math.round((totalHours - baseHours) * 100) / 100"></strong> hour(s). Rounded up to <strong x-text="overtimeHours"></strong> overtime hour(s) at <strong x-text="formatRupiah(overtimeRate) + '/hour'"></strong> = <strong x-text="formatRupiah(overtimeCharge)"></strong> extra charge.
                            </p>
                        </div>
                    </div>

                    <!-- Step 3: Special Requests / Event Notes -->
                    <div class="bg-surface-white rounded-2xl p-6 md:p-8 shadow-sm border border-outline-variant/30 space-y-4">
                        <div class="flex items-center gap-3 border-b border-outline-variant/20 pb-4">
                            <div class="w-9 h-9 rounded-xl bg-oceanic-deep text-white flex items-center justify-center font-bold text-sm shrink-0">3</div>
                            <div>
                                <h2 class="font-display-lg text-base md:text-lg font-bold text-oceanic-deep">Special Requests &amp; Notes (Optional)</h2>
                                <p class="text-xs text-on-surface-variant mt-0.5">Let your travel agent know about pickup hotel, dietary needs, or special occasions.</p>
                            </div>
                        </div>

                        <div>
                            <textarea name="special_requests"
                                      rows="3"
                                      placeholder="Example: Pickup at Seminyak Resort lobby at 08:00 AM, honeymoon flower arrangement, or vegetarian lunch stop..."
                                      class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl p-4 text-sm text-on-surface focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow">{{ old('special_requests') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Sticky Live Summary & Overtime Breakdown (5 Cols) -->
                <div class="lg:col-span-5 lg:sticky lg:top-24">
                    <div class="bg-surface-white rounded-3xl p-6 md:p-8 shadow-xl border border-outline-variant/30 space-y-6">
                        <div class="flex items-center justify-between border-b border-outline-variant/20 pb-4">
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-widest text-tropical-teal block">Real-Time Estimator</span>
                                <h3 class="font-headline-md text-xl font-bold text-oceanic-deep">Custom Itinerary Summary</h3>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold"
                                  :class="isOvertime ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-emerald-100 text-emerald-800 border border-emerald-300'"
                                  x-text="isOvertime ? 'Overtime' : 'Within 8 Hours'">
                            </span>
                        </div>

                        <!-- Duration & Overtime Breakdown Card -->
                        <div class="bg-surface-container-low rounded-2xl p-4 space-y-3 border border-outline-variant/20">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-on-surface-variant font-medium">Selected Stops</span>
                                <span class="font-bold text-on-surface" x-text="stops.length + ' destination(s)'"></span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-on-surface-variant font-medium">Standard Included Duration</span>
                                <span class="font-bold text-on-surface" x-text="baseHours + ' hours'"></span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-on-surface-variant font-medium">Estimated Total Duration</span>
                                <span class="font-bold text-oceanic-deep" x-text="totalHours + ' hours'"></span>
                            </div>
                            <div class="flex items-center justify-between text-xs pt-2 border-t border-outline-variant/20">
                                <span class="text-on-surface-variant font-medium">Overtime Hours (Rounded Up)</span>
                                <span class="font-bold" :class="isOvertime ? 'text-amber-700' : 'text-emerald-700'" x-text="overtimeHours + ' hour(s)'"></span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-on-surface-variant font-medium">Overtime Rate / Hour</span>
                                <span class="font-bold text-on-surface" x-text="formatRupiah(overtimeRate)"></span>
                            </div>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="space-y-3 pt-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-on-surface-variant">
                                    Base Tour (<span x-text="guestCount"></span> pax × <span x-text="selectedPackage ? formatRupiah(selectedPackage.price_per_pax) : 'Rp 0'"></span>)
                                </span>
                                <span class="font-semibold text-on-surface" x-text="formatRupiah(baseTourCost)"></span>
                            </div>

                            <div class="flex items-center justify-between text-sm">
                                <span class="text-on-surface-variant">
                                    Overtime Extra Charge (<span x-text="overtimeHours"></span> hr × <span x-text="formatRupiah(overtimeRate)"></span>)
                                </span>
                                <span class="font-bold" :class="isOvertime ? 'text-amber-600' : 'text-emerald-600'" x-text="formatRupiah(overtimeCharge)"></span>
                            </div>

                            <div class="pt-4 border-t border-outline-variant/30 flex items-end justify-between">
                                <div>
                                    <span class="text-xs font-bold uppercase tracking-wider text-outline block">Estimated Total Price</span>
                                    <span class="text-[11px] text-on-surface-variant">Includes base package &amp; overtime fee</span>
                                </div>
                                <span class="font-display-lg text-2xl font-bold text-tropical-teal" x-text="formatRupiah(grandTotal)"></span>
                            </div>
                        </div>

                        @auth
                            @if(auth()->user()->isUser())
                                <button type="submit"
                                        class="w-full bg-tropical-teal hover:bg-secondary text-white font-label-bold text-sm py-4 rounded-xl flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transition-all duration-200 active:scale-98 cursor-pointer">
                                    <span>Book Custom Itinerary</span>
                                    <span class="material-symbols-outlined text-lg">arrow_forward</span>
                                </button>
                            @else
                                <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium text-center">
                                    Please sign in with a Traveler account to submit a custom itinerary booking.
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                               class="w-full bg-oceanic-deep hover:bg-primary text-white font-label-bold text-sm py-4 rounded-xl flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transition-all duration-200 active:scale-98">
                                <span class="material-symbols-outlined text-lg">login</span>
                                <span>Login to Book Custom Itinerary</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>

