<div>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Left Column: Step-by-Step Configuration (8 cols) -->
        <div class="lg:col-span-8 space-y-8">

            <!-- STEP 1: Select Region -->
            <div class="bg-surface-white rounded-2xl p-6 md:p-8 shadow-sm border border-outline-variant/30 space-y-5">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-tropical-teal text-white font-bold text-sm flex items-center justify-center shrink-0">
                        1
                    </span>
                    <div>
                        <span class="text-xs font-bold text-tropical-teal uppercase tracking-wider">Step 1</span>
                        <h2 class="font-display-lg text-lg md:text-xl font-bold text-oceanic-deep">
                            Choose Your Bali Region
                        </h2>
                    </div>
                </div>
                <p class="text-xs md:text-sm text-on-surface-variant leading-relaxed">
                    Select the Bali geographical territory you want to explore. Only destinations and partner tour operators operating within this region will be presented.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    @foreach($regions as $key => $reg)
                        @php
                            $isSelected = ($selectedRegion === $key);
                        @endphp
                        <label wire:click="$set('selectedRegion', '{{ $key }}')"
                               class="relative p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 flex flex-col justify-between gap-3 text-left
                                      {{ $isSelected ? 'border-tropical-teal bg-tropical-teal/5 ring-1 ring-tropical-teal shadow-sm' : 'border-outline-variant/30 hover:border-tropical-teal/50 bg-surface-container-low/40' }}">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-lg {{ $isSelected ? 'bg-tropical-teal text-white' : 'bg-surface-container text-on-surface-variant' }} flex items-center justify-center">
                                        <span class="material-symbols-outlined text-lg">{{ $reg['icon'] }}</span>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-sm text-oceanic-deep">{{ $reg['name'] }}</h3>
                                        <span class="text-[11px] text-outline block">{{ $reg['badge'] }}</span>
                                    </div>
                                </div>
                                <span class="w-5 h-5 rounded-full border-2 flex items-center justify-center {{ $isSelected ? 'border-tropical-teal bg-tropical-teal text-white' : 'border-outline-variant' }}">
                                    @if($isSelected)
                                        <span class="material-symbols-outlined text-xs">check</span>
                                    @endif
                                </span>
                            </div>
                            <p class="text-xs text-on-surface-variant leading-relaxed">
                                {{ $reg['description'] }}
                            </p>
                        </label>
                    @endforeach
                </div>
                @error('selectedRegion')
                    <span class="text-error text-xs block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <!-- STEP 2: Select Travel Agent -->
            <div class="bg-surface-white rounded-2xl p-6 md:p-8 shadow-sm border border-outline-variant/30 space-y-5">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-tropical-teal text-white font-bold text-sm flex items-center justify-center shrink-0">
                        2
                    </span>
                    <div>
                        <span class="text-xs font-bold text-tropical-teal uppercase tracking-wider">Step 2</span>
                        <h2 class="font-display-lg text-lg md:text-xl font-bold text-oceanic-deep">
                            Select Verified Partner Agent in {{ $regions[$selectedRegion]['name'] }}
                        </h2>
                    </div>
                </div>
                <p class="text-xs md:text-sm text-on-surface-variant leading-relaxed">
                    Choose one of our licensed travel operators. Your driver and charter vehicle will be dispatched directly by this agent.
                </p>

                @if($availableAgents->isEmpty())
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-xs">
                        No active tour agent found in this region. Please select another region.
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                        @foreach($availableAgents as $ag)
                            @php
                                $isAgSelected = ($selectedAgentId === $ag->id);
                                $agPkg = $ag->packages->where('region', $selectedRegion)->first() ?? $ag->packages->first();
                                $price = $agPkg ? (int) $agPkg->price_per_pax : 350000;
                            @endphp
                            <label wire:click="selectAgent({{ $ag->id }})"
                                   class="relative p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 flex flex-col justify-between gap-3 text-left
                                          {{ $isAgSelected ? 'border-tropical-teal bg-tropical-teal/5 ring-1 ring-tropical-teal shadow-sm' : 'border-outline-variant/30 hover:border-tropical-teal/50 bg-surface-container-low/40' }}">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <h3 class="font-bold text-sm text-oceanic-deep truncate">{{ $ag->agent_name }}</h3>
                                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                <span class="material-symbols-outlined text-[12px]">verified</span>
                                                <span>Verified</span>
                                            </span>
                                        </div>
                                        <p class="text-xs text-outline mt-1 line-clamp-2">
                                            <span class="material-symbols-outlined text-[13px] align-middle text-tropical-teal">location_on</span>
                                            {{ $ag->address }}
                                        </p>
                                    </div>
                                    <span class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 {{ $isAgSelected ? 'border-tropical-teal bg-tropical-teal text-white' : 'border-outline-variant' }}">
                                        @if($isAgSelected)
                                            <span class="material-symbols-outlined text-xs">check</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="pt-2 border-t border-outline-variant/20 flex items-center justify-between text-xs">
                                    <span class="text-outline">Fleet: <strong>{{ $ag->vehicle_count }} Vehicles</strong></span>
                                    <span class="font-bold text-tropical-teal">Rp {{ number_format($price, 0, ',', '.') }}/pax</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
                @error('selectedAgentId')
                    <span class="text-error text-xs block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <!-- STEP 3: Start Point & Trip Details -->
            <div class="bg-surface-white rounded-2xl p-6 md:p-8 shadow-sm border border-outline-variant/30 space-y-5">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-tropical-teal text-white font-bold text-sm flex items-center justify-center shrink-0">
                        3
                    </span>
                    <div>
                        <span class="text-xs font-bold text-tropical-teal uppercase tracking-wider">Step 3</span>
                        <h2 class="font-display-lg text-lg md:text-xl font-bold text-oceanic-deep">
                            Departure Point &amp; Travel Schedule
                        </h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Start Point -->
                    <div class="md:col-span-2 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="startPoint" class="font-label-bold text-xs text-on-surface uppercase tracking-wider">
                                Starting Point / Pick-Up Location <span class="text-error">*</span>
                            </label>
                            <span class="text-[11px] text-tropical-teal font-semibold">
                                Defaults to Partner Agent Office
                            </span>
                        </div>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-3.5 text-tropical-teal text-lg">flag</span>
                            <input type="text" id="startPoint" wire:model.live.debounce.500ms="startPoint"
                                   placeholder="e.g. Hotel Mulia Nusa Dua or Agency Office"
                                   class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl pl-10 pr-4 py-3 text-sm font-medium text-on-surface focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow @error('startPoint') border-error @enderror">
                        </div>
                        <p class="text-[11px] text-outline">
                            Our chauffeur will pick you up at this starting address. If staying at a resort or villa, please enter your hotel name or address above.
                        </p>
                        @error('startPoint')
                            <span class="text-error text-xs block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Travel Date -->
                    <div class="space-y-1.5">
                        <label for="travelDate" class="font-label-bold text-xs text-on-surface uppercase tracking-wider">
                            Travel Date (D-3 Rule) <span class="text-error">*</span>
                        </label>
                        <input type="date" id="travelDate" wire:model.live="travelDate" min="{{ $minDate }}"
                               class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-4 py-3 text-sm font-medium text-on-surface focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow @error('travelDate') border-error @enderror">
                        @error('travelDate')
                            <span class="text-error text-xs block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Guest Count -->
                    <div class="space-y-1.5">
                        <label for="guestCount" class="font-label-bold text-xs text-on-surface uppercase tracking-wider">
                            Number of Guests <span class="text-error">*</span>
                        </label>
                        <input type="number" id="guestCount" wire:model.live="guestCount" min="1" max="{{ $maxGuests }}"
                               class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-4 py-3 text-sm font-medium text-on-surface focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow @error('guestCount') border-error @enderror">
                        @error('guestCount')
                            <span class="text-error text-xs block font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- STEP 4: Choose Destinations & Interactive Route Map -->
            <div class="bg-surface-white rounded-2xl p-6 md:p-8 shadow-sm border border-outline-variant/30 space-y-6">
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-tropical-teal text-white font-bold text-sm flex items-center justify-center shrink-0">
                            4
                        </span>
                        <div>
                            <span class="text-xs font-bold text-tropical-teal uppercase tracking-wider">Step 4</span>
                            <h2 class="font-display-lg text-lg md:text-xl font-bold text-oceanic-deep">
                                Select Destinations in {{ $regions[$selectedRegion]['name'] }}
                            </h2>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-tropical-teal bg-tropical-teal/10 px-3 py-1.5 rounded-full">
                        <span class="material-symbols-outlined text-sm">pin_drop</span>
                        <span>{{ count($stops) }} {{ Str::plural('Stop', count($stops)) }} Configured</span>
                    </span>
                </div>

                <p class="text-xs md:text-sm text-on-surface-variant leading-relaxed">
                    Choose multiple sights and customize how many hours you want to spend at each spot. Driving distance and travel duration are calculated automatically from your Start Point through each stop.
                </p>

                <!-- Destination Selector Dropdown & Quick Add -->
                <div class="bg-surface-container-low p-4 rounded-xl border border-outline-variant/30 space-y-3">
                    <div class="flex flex-col sm:flex-row gap-2.5 items-stretch sm:items-center">
                        <div class="flex-1">
                            <label for="addDestSelect" class="sr-only">Choose a destination to add</label>
                            <select id="addDestSelect" class="w-full bg-surface-white border border-outline-variant/40 rounded-xl px-3 py-2.5 text-xs sm:text-sm font-medium text-on-surface focus:ring-2 focus:ring-tropical-teal focus:outline-none">
                                <option value="">-- Choose destination in {{ $regions[$selectedRegion]['name'] }} --</option>
                                @foreach($availableDestinations as $dest)
                                    @php
                                        $alreadyInStops = collect($stops)->contains('id', $dest->id);
                                    @endphp
                                    <option value="{{ $dest->id }}" {{ $alreadyInStops ? 'disabled' : '' }}>
                                        {{ $dest->name }} ({{ $dest->location }}) {{ $alreadyInStops ? '— [Already in route]' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button"
                                onclick="const sel = document.getElementById('addDestSelect'); if(sel.value) { @this.addDestination(parseInt(sel.value)); sel.value = ''; }"
                                class="inline-flex items-center justify-center gap-1.5 bg-tropical-teal hover:bg-secondary text-white font-semibold text-xs px-5 py-2.5 rounded-xl shadow-sm transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-base">add</span>
                            <span>Add to Route</span>
                        </button>
                    </div>

                    <!-- Quick Tag Pills of Region Destinations -->
                    <div class="flex flex-wrap gap-1.5 pt-1">
                        @foreach($availableDestinations as $dest)
                            @php
                                $isPicked = collect($stops)->contains('id', $dest->id);
                            @endphp
                            <button type="button" wire:click="toggleDestination({{ $dest->id }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium border transition-all cursor-pointer
                                           {{ $isPicked ? 'bg-tropical-teal text-white border-tropical-teal shadow-xs' : 'bg-surface-white hover:bg-tropical-teal/10 text-on-surface border-outline-variant/40' }}">
                                <span class="material-symbols-outlined text-xs">{{ $isPicked ? 'check_circle' : 'add_circle' }}</span>
                                <span>{{ $dest->name }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Interactive Leaflet Map for Route Visualization -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs text-outline">
                        <span class="font-semibold flex items-center gap-1 text-oceanic-deep">
                            <span class="material-symbols-outlined text-sm text-tropical-teal">route</span>
                            <span>Interactive Route Map</span>
                        </span>
                        <span>Red marker = Start Point &bull; Teal markers = Sequential Stops</span>
                    </div>
                    <div id="customItineraryMap" wire:ignore wire:key="custom-itinerary-map-container" class="w-full h-80 rounded-2xl border border-outline-variant/30 shadow-sm z-0 relative"></div>
                </div>

                <!-- Sequence of Stops List -->
                <div class="space-y-3 pt-2">
                    <span class="text-xs font-bold text-on-surface uppercase tracking-wider block">
                        Sequential Route &amp; Stop Details
                    </span>

                    <!-- Start Point Indicator -->
                    <div class="p-3.5 rounded-xl bg-tropical-teal/10 border border-tropical-teal/30 flex items-center gap-3">
                        <span class="w-7 h-7 rounded-full bg-rose-500 text-white font-bold text-xs flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-sm">flag</span>
                        </span>
                        <div class="min-w-0 flex-1">
                            <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wider block">Origin / Start Point</span>
                            <span class="text-xs font-semibold text-oceanic-deep truncate block">{{ $startPoint }}</span>
                        </div>
                    </div>

                    <!-- Destination Stops -->
                    @foreach($stops as $idx => $stop)
                        <div class="p-4 rounded-xl bg-surface-container-low/70 border border-outline-variant/30 space-y-3 transition-all hover:bg-surface-container-low"
                             wire:key="stop-row-{{ $stop['id'] }}-{{ $idx }}">

                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-3 min-w-0 flex-1">
                                    <span class="w-7 h-7 rounded-full bg-tropical-teal text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                        {{ $idx + 1 }}
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-bold text-sm text-oceanic-deep">{{ $stop['name'] }}</h4>
                                        <span class="text-xs text-outline">{{ $stop['location'] }}</span>

                                        <!-- Driving metric from previous point -->
                                        <div class="mt-1 flex items-center gap-2 text-[11px] text-emerald-800 font-semibold bg-emerald-50 px-2 py-0.5 rounded-md w-fit">
                                            <span class="material-symbols-outlined text-xs">directions_car</span>
                                            <span>
                                                Drive from {{ $idx === 0 ? 'Start' : 'Stop ' . $idx }}:
                                                ~{{ round($stop['leg_travel_hours'] * 60) }} mins ({{ $stop['leg_distance_km'] }} km)
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons (Reorder & Remove) -->
                                <div class="flex items-center gap-1 shrink-0">
                                    <button type="button" wire:click="moveStopUp({{ $idx }})" {{ $idx === 0 ? 'disabled' : '' }}
                                            class="p-1.5 rounded-lg border border-outline-variant/30 hover:bg-surface-container disabled:opacity-30 disabled:cursor-not-allowed text-outline hover:text-oceanic-deep"
                                            title="Move Up">
                                        <span class="material-symbols-outlined text-sm">arrow_upward</span>
                                    </button>
                                    <button type="button" wire:click="moveStopDown({{ $idx }})" {{ $idx === count($stops) - 1 ? 'disabled' : '' }}
                                            class="p-1.5 rounded-lg border border-outline-variant/30 hover:bg-surface-container disabled:opacity-30 disabled:cursor-not-allowed text-outline hover:text-oceanic-deep"
                                            title="Move Down">
                                        <span class="material-symbols-outlined text-sm">arrow_downward</span>
                                    </button>
                                    <button type="button" wire:click="removeStop({{ $idx }})" {{ count($stops) <= 1 ? 'disabled' : '' }}
                                            class="p-1.5 rounded-lg border border-outline-variant/30 hover:bg-rose-50 text-outline hover:text-error disabled:opacity-30 disabled:cursor-not-allowed"
                                            title="Remove Stop">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Duration Input for this Stop -->
                            <div class="flex items-center justify-between pt-2 border-t border-outline-variant/20 text-xs">
                                <label class="font-medium text-on-surface-variant">Stay Duration at {{ $stop['name'] }}:</label>
                                <div class="flex items-center gap-2">
                                    <input type="number" step="0.5" min="0.5" max="12"
                                           value="{{ $stop['stay_hours'] }}"
                                           wire:change="updateStayHours({{ $idx }}, $event.target.value)"
                                           class="w-20 bg-surface-white border border-outline-variant/40 rounded-lg px-2.5 py-1 text-center font-bold text-oceanic-deep focus:ring-1 focus:ring-tropical-teal focus:outline-none">
                                    <span class="text-outline font-medium">hours</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('stops')
                    <span class="text-error text-xs block font-medium">{{ $message }}</span>
                @enderror

                <!-- Special Requests -->
                <div class="space-y-1.5 pt-2">
                    <label for="specialRequests" class="font-label-bold text-xs text-on-surface uppercase tracking-wider">
                        Special Requests or Route Preferences (Optional)
                    </label>
                    <textarea id="specialRequests" wire:model.defer="specialRequests" rows="3"
                              placeholder="e.g. Vegetarian dining stops, infant car seat, specific photo spots..."
                              class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl p-3.5 text-xs sm:text-sm font-medium text-on-surface focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow"></textarea>
                </div>
            </div>

        </div>

        <!-- Right Column: Sticky Summary & Real-Time Calculation (4 cols) -->
        <div class="lg:col-span-4 sticky top-28 space-y-6">

            <div class="bg-surface-white rounded-3xl p-6 md:p-8 shadow-xl border border-outline-variant/30 space-y-6">

                <div>
                    <span class="text-xs font-bold text-tropical-teal uppercase tracking-wider">Trip Summary</span>
                    <h3 class="font-display-lg text-xl font-bold text-oceanic-deep mt-0.5">
                        {{ $regions[$selectedRegion]['name'] }} Custom Tour
                    </h3>
                    <p class="text-xs text-outline mt-1">
                        Operated by: <strong class="text-oceanic-deep">{{ $selectedAgent->agent_name ?? 'Official Partner Agent' }}</strong>
                    </p>
                </div>

                <!-- Timing Breakdown Card -->
                <div class="bg-surface-container-low rounded-2xl p-4 space-y-3 border border-outline-variant/20">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-outline flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm">directions_car</span>
                            <span>Est. Road Travel</span>
                        </span>
                        <span class="font-bold text-oceanic-deep">{{ $totalTravelTime }} hrs (~{{ $totalDistanceKm }} km)</span>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="text-outline flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm">hourglass_bottom</span>
                            <span>Destination Stays</span>
                        </span>
                        <span class="font-bold text-oceanic-deep">{{ $totalStayTime }} hrs</span>
                    </div>

                    <div class="pt-2 border-t border-dashed border-outline-variant/40 flex items-center justify-between text-sm">
                        <span class="font-bold text-oceanic-deep">Total Tour Duration</span>
                        <span class="font-display-lg text-base font-bold text-tropical-teal">{{ $totalHours }} hrs</span>
                    </div>

                    <!-- Overtime Status Indicator -->
                    <div class="pt-1">
                        @if($overtimeCalc['is_overtime'])
                            <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center gap-2">
                                <span class="material-symbols-outlined text-amber-600 text-sm">warning</span>
                                <div>
                                    <span class="font-bold">Overtime: +{{ $overtimeCalc['overtime_hours'] }} {{ Str::plural('hour', $overtimeCalc['overtime_hours']) }}</span>
                                    <div class="text-[11px] text-amber-800">
                                        Exceeds {{ $baseHours }}h base charter (+Rp {{ number_format($overtimeCalc['overtime_charge'], 0, ',', '.') }})
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="p-2 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-emerald-600 text-sm">check_circle</span>
                                <span>Within Base {{ $baseHours }}-Hour Limit (No Overtime Charge)</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Price Breakdown -->
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between items-center text-outline">
                        <span>Base Rate per Pax</span>
                        <span class="font-semibold text-oceanic-deep">Rp {{ number_format($basePricePerPax, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-outline">
                        <span>Number of Travelers</span>
                        <span class="font-semibold text-oceanic-deep">{{ $guestCount }} Pax</span>
                    </div>
                    <div class="flex justify-between items-center text-outline">
                        <span>Base Tour Subtotal</span>
                        <span class="font-semibold text-oceanic-deep">Rp {{ number_format($baseTourCost, 0, ',', '.') }}</span>
                    </div>
                    @if($overtimeCharge > 0)
                        <div class="flex justify-between items-center text-amber-700 font-semibold">
                            <span>Overtime Charge ({{ $overtimeCalc['overtime_hours'] }} hrs)</span>
                            <span>+Rp {{ number_format($overtimeCharge, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Grand Total -->
                <div class="pt-4 border-t border-outline-variant/30">
                    <span class="text-xs text-outline block">Total Payable:</span>
                    <div class="flex items-baseline gap-1 mt-1">
                        <span class="font-display-lg text-2xl md:text-3xl font-bold text-tropical-teal">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </span>
                    </div>
                    <span class="text-[11px] text-outline mt-1 block">Includes vehicle, driver, fuel, &amp; insurance</span>
                </div>

                <!-- Booking Action Button -->
                <button type="button" wire:click="submitBooking" wire:loading.attr="disabled"
                        class="w-full bg-tropical-teal hover:bg-secondary text-white font-label-bold text-sm py-4 rounded-xl flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transition-all duration-200 active:scale-98 cursor-pointer disabled:opacity-50">
                    <span wire:loading.remove wire:target="submitBooking" class="material-symbols-outlined text-lg">check_circle</span>
                    <span wire:loading wire:target="submitBooking" class="material-symbols-outlined text-lg animate-spin">refresh</span>
                    <span wire:loading.remove wire:target="submitBooking">Confirm &amp; Proceed to Payment</span>
                    <span wire:loading wire:target="submitBooking">Processing Booking...</span>
                </button>

                <p class="text-[11px] text-outline text-center leading-relaxed">
                    By confirming, you will be redirected to secure Midtrans payment. Reservation is held for {{ config('trihita.booking_expiry_minutes', 60) }} minutes.
                </p>

            </div>

        </div>

    </div>

    <!-- Leaflet & Leaflet Routing Machine Assets & Map Initialization -->
    @assets
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @endassets

    @script
    <script>
        const DEFAULT_ORIGIN_LAT = -8.670458; // Denpasar / Sanur
        const DEFAULT_ORIGIN_LNG = 115.262512;
        const DEFAULT_ORIGIN_NAME = 'Denpasar / Sanur Starting Point';

        let map = null;
        let originLayerGroup = null; // Independent layer: holds ONLY the Origin Marker
        let stopsLayerGroup = null;  // Independent layer: holds destination stop markers
        let routeLayerGroup = null;  // Independent layer: holds OSRM GeoJSON / polyline routes
        let originMarker = null;     // Direct persistent reference to the Origin marker
        let currentRenderRequestId = 0;

        function sanitizeStartPoint(sp) {
            let lat = sp && sp.lat !== undefined && sp.lat !== null ? parseFloat(sp.lat) : NaN;
            let lng = sp && sp.lng !== undefined && sp.lng !== null ? parseFloat(sp.lng) : NaN;
            let name = (sp && sp.name) ? String(sp.name) : '';

            if (isNaN(lat) || lat === 0) {
                lat = (typeof $wire !== 'undefined' && $wire.startLat) ? parseFloat($wire.startLat) : NaN;
            }
            if (isNaN(lng) || lng === 0) {
                lng = (typeof $wire !== 'undefined' && $wire.startLng) ? parseFloat($wire.startLng) : NaN;
            }
            if (!name && typeof $wire !== 'undefined' && $wire.startPoint) {
                name = $wire.startPoint;
            }

            // Safe fallback coordinates (Denpasar) if null or NaN
            if (isNaN(lat)) lat = DEFAULT_ORIGIN_LAT;
            if (isNaN(lng)) lng = DEFAULT_ORIGIN_LNG;
            if (!name) name = DEFAULT_ORIGIN_NAME;

            return { lat, lng, name };
        }

        function initMap() {
            const mapEl = document.getElementById('customItineraryMap');
            if (!mapEl) return;

            // Safe cleanup if existing map instance exists
            if (map) {
                try {
                    map.remove();
                } catch (e) {}
                map = null;
                originMarker = null;
            }

            if (mapEl._leaflet_id) {
                delete mapEl._leaflet_id;
            }

            // Initial view centered on Bali
            map = L.map('customItineraryMap').setView([DEFAULT_ORIGIN_LAT, DEFAULT_ORIGIN_LNG], 11);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            // Separate Layer Groups:
            // 1. routeLayerGroup: cleared & redrawn on route changes
            // 2. stopsLayerGroup: destination markers
            // 3. originLayerGroup: persists the starting point marker
            routeLayerGroup = L.layerGroup().addTo(map);
            stopsLayerGroup = L.layerGroup().addTo(map);
            originLayerGroup = L.layerGroup().addTo(map);

            setTimeout(() => {
                if (map) map.invalidateSize();
            }, 250);

            // Initial state from Livewire with safe fallbacks
            const safeStart = sanitizeStartPoint({
                name: typeof $wire !== 'undefined' ? $wire.startPoint : @json($startPoint),
                lat: typeof $wire !== 'undefined' ? $wire.startLat : @json($startLat),
                lng: typeof $wire !== 'undefined' ? $wire.startLng : @json($startLng)
            });

            const safeStops = (typeof $wire !== 'undefined' && $wire.stops) ? $wire.stops : @json($stops);

            // Ensure Origin Marker is placed on the map
            ensureOriginMarker(safeStart, safeStops);

            // Render destinations and OSRM route
            renderRouteAndStops(safeStops, safeStart, false);
        }

        function ensureOriginMarker(startPoint, currentStops) {
            if (!map || !originLayerGroup) return null;

            const safe = sanitizeStartPoint(startPoint);
            const startLatLng = L.latLng(safe.lat, safe.lng);

            const popupHtml = `
                <div style="font-size:12px; line-height:1.4;">
                    <strong style="color:#ef4444;">🚩 Origin / Starting Point</strong><br>
                    <span>${safe.name || 'Custom Pick-Up'}</span>
                    <div style="margin-top:4px; font-size:10px; color:#6b7280;">💡 Drag marker anywhere to adjust pick-up spot</div>
                </div>
            `;

            // If Origin Marker already exists, update position without re-creating or clearing layers
            if (originMarker) {
                originMarker.setLatLng(startLatLng);
                originMarker.setPopupContent(popupHtml);
                return originMarker;
            }

            const startIcon = L.divIcon({
                className: 'start-marker',
                html: `<div style="background-color:#ef4444; color:#fff; width:28px; height:28px; border-radius:9999px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:13px; border:2px solid #fff; box-shadow:0 3px 6px rgba(0,0,0,0.35); cursor:grab;">🚩</div>`,
                iconSize: [28, 28],
                iconAnchor: [14, 14],
                popupAnchor: [0, -14]
            });

            originMarker = L.marker(startLatLng, {
                icon: startIcon,
                draggable: true,
                title: 'Pick-Up Location / Origin (Drag to change)'
            }).addTo(originLayerGroup);

            originMarker.bindPopup(popupHtml);

            // Capture dragend event
            originMarker.on('dragend', function (e) {
                const newPos = e.target.getLatLng();
                const newLat = Number(newPos.lat.toFixed(7));
                const newLng = Number(newPos.lng.toFixed(7));

                const updatedStart = {
                    name: safe.name,
                    lat: newLat,
                    lng: newLng
                };

                // 1. Immediately redraw OSRM route on map (do NOT touch or destroy origin marker)
                const stopsToUse = (typeof $wire !== 'undefined' && $wire.stops) ? $wire.stops : (currentStops || []);
                renderRouteAndStops(stopsToUse, updatedStart, false);

                // 2. Synchronize with Livewire using deferred set (false) to avoid full DOM re-render
                if (typeof $wire !== 'undefined' && $wire) {
                    try {
                        $wire.set('startLat', newLat, false);
                        $wire.set('startLng', newLng, false);
                    } catch (err) {
                        $wire.set('startLat', newLat);
                        $wire.set('startLng', newLng);
                    }
                    if (typeof $wire.updateStartCoordinates === 'function') {
                        $wire.updateStartCoordinates(newLat, newLng);
                    }
                }
            });

            return originMarker;
        }

        async function drawSegmentRoute(p1, p2, targetLayerGroup, requestId) {
            const lon1 = p1.lng;
            const lat1 = p1.lat;
            const lon2 = p2.lng;
            const lat2 = p2.lat;

            // OSRM Driving Route API
            const url = `https://router.project-osrm.org/route/v1/driving/${lon1},${lat1};${lon2},${lat2}?overview=full&geometries=geojson`;

            try {
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 6000);

                const response = await fetch(url, { signal: controller.signal });
                clearTimeout(timeoutId);

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();

                // Check if request is still current (not superseded by newer user interaction)
                if (requestId !== currentRenderRequestId) return;

                if (data && data.code === 'Ok' && Array.isArray(data.routes) && data.routes.length > 0 && data.routes[0].geometry) {
                    // Draw realistic driving route via GeoJSON
                    L.geoJSON(data.routes[0].geometry, {
                        style: {
                            color: '#006A6A',
                            weight: 4,
                            opacity: 0.85,
                            lineCap: 'round',
                            lineJoin: 'round'
                        }
                    }).addTo(targetLayerGroup);
                    return;
                }

                // No valid driving route (e.g. crossing sea / NoRoute)
                throw new Error(data ? (data.message || data.code) : 'No route returned');
            } catch (err) {
                // Fallback System: draw straight polyline if OSRM fails or has no driving route
                if (requestId !== currentRenderRequestId) return;

                L.polyline([[lat1, lon1], [lat2, lon2]], {
                    color: '#006A6A',
                    weight: 4,
                    opacity: 0.85,
                    dashArray: '6, 8',
                    lineCap: 'round'
                }).addTo(targetLayerGroup);
            }
        }

        function renderRouteAndStops(stops, startPoint, updateMarker = true) {
            if (!map || !routeLayerGroup || !stopsLayerGroup) return;

            const safeStart = sanitizeStartPoint(startPoint);

            if (updateMarker) {
                ensureOriginMarker(safeStart, stops);
            }

            const requestId = ++currentRenderRequestId;

            // ISOLATED CLEAR: ONLY clear route lines and stop markers!
            // originLayerGroup is NEVER cleared here, keeping Origin marker always visible!
            stopsLayerGroup.clearLayers();
            routeLayerGroup.clearLayers();

            const points = [];
            const bounds = L.latLngBounds();

            // 1. Add Start Point to bounds and routing coordinates
            const startLatLng = L.latLng(safeStart.lat, safeStart.lng);
            points.push({
                lat: safeStart.lat,
                lng: safeStart.lng,
                name: safeStart.name,
                isStart: true
            });
            bounds.extend(startLatLng);

            // 2. Add Stop Markers to stopsLayerGroup (NOT originLayerGroup)
            if (Array.isArray(stops)) {
                stops.forEach((stop, index) => {
                    if (stop.latitude && stop.longitude) {
                        const pt = L.latLng(Number(stop.latitude), Number(stop.longitude));
                        points.push({
                            lat: Number(stop.latitude),
                            lng: Number(stop.longitude),
                            name: stop.name,
                            location: stop.location,
                            stay_hours: stop.stay_hours
                        });
                        bounds.extend(pt);

                        const stopIcon = L.divIcon({
                            className: 'stop-marker',
                            html: `<div style="background-color:#006A6A; color:#fff; width:26px; height:26px; border-radius:9999px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:12px; border:2px solid #fff; box-shadow:0 2px 5px rgba(0,0,0,0.3);">${index + 1}</div>`,
                            iconSize: [26, 26],
                            iconAnchor: [13, 13],
                            popupAnchor: [0, -14]
                        });

                        L.marker(pt, { icon: stopIcon })
                            .addTo(stopsLayerGroup)
                            .bindPopup(`<strong>Stop ${index + 1}: ${stop.name}</strong><br><span style="font-size:11px;">${stop.location || ''} (Stay: ${stop.stay_hours || 2}h)</span>`);
                    }
                });
            }

            // 3. Draw Sequential Route via OSRM Driving Roads (with Fallback to Polyline)
            if (points.length > 1) {
                map.fitBounds(bounds, { padding: [40, 40] });

                for (let i = 0; i < points.length - 1; i++) {
                    drawSegmentRoute(points[i], points[i + 1], routeLayerGroup, requestId);
                }
            } else if (points.length === 1) {
                map.setView([points[0].lat, points[0].lng], 12);
            }
        }

        function handleMapUpdate(payload) {
            if (Array.isArray(payload)) {
                payload = payload[0] || {};
            } else if (payload && typeof payload === 'object' && payload[0]) {
                payload = payload[0];
            }
            payload = payload || {};

            const stops = payload.stops || (typeof $wire !== 'undefined' ? $wire.stops : []) || [];
            const startPoint = payload.startPoint || {
                name: typeof $wire !== 'undefined' ? $wire.startPoint : '',
                lat: typeof $wire !== 'undefined' ? $wire.startLat : DEFAULT_ORIGIN_LAT,
                lng: typeof $wire !== 'undefined' ? $wire.startLng : DEFAULT_ORIGIN_LNG
            };

            if (!map) {
                tryInitMap();
                return;
            }

            // Move Origin marker to new coordinates & redraw route
            renderRouteAndStops(stops, startPoint, true);
        }

        function tryInitMap() {
            if (typeof L === 'undefined') {
                setTimeout(tryInitMap, 50);
                return;
            }
            initMap();
        }

        tryInitMap();

        // Listen for Livewire events (changing agent, changing region, adding/removing/reordering stops)
        if (typeof $wire !== 'undefined' && $wire) {
            $wire.on('itinerary-map-updated', (data) => handleMapUpdate(data));
        }
        window.addEventListener('itinerary-map-updated', (event) => {
            handleMapUpdate(event.detail);
        });
    </script>
    @endscript
</div>

