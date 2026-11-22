<div>
    <!-- Leaflet.js CSS & JS (same library as Custom Itinerary) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Actions Bar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div class="flex items-center gap-4 w-full md:w-auto">
            <div class="relative w-full md:w-80">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
                <input wire:model.live="search" class="w-full bg-surface-white border border-outline-variant/50 rounded-lg pl-10 pr-4 py-2 text-body-md font-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-tropical-teal/50 focus:border-tropical-teal transition-all placeholder:text-outline/70 shadow-sm" placeholder="{{ __('Search destinations...') }}" type="text">
            </div>
            @if($search)
                <div class="hidden md:flex items-center gap-2">
                    <span class="font-caption text-[11px] uppercase tracking-wider text-outline">{{ __('Showing results for:') }}</span>
                    <span class="font-label-bold text-sm text-oceanic-deep">{{ $search }}</span>
                </div>
            @endif
        </div>
        <button wire:click="openCreate" class="w-full md:w-auto bg-tropical-teal text-surface-white font-label-bold text-label-bold px-6 py-2.5 rounded-full flex items-center justify-center gap-2 hover:bg-oceanic-deep transition-all shadow-[0_4px_10px_rgba(0,35,102,0.1)] hover:shadow-[0_10px_30px_rgba(0,35,102,0.15)]">
            <span class="material-symbols-outlined text-[20px]">add</span>
            <span>{{ __('Add New Destination') }}</span>
        </button>
    </div>

    @if (session()->has('success'))
        <div class="bg-primary-container text-on-primary-container p-4 rounded-xl mb-6 font-label-bold text-sm shadow-sm border border-primary-container/20">
            {{ session('success') }}
        </div>
    @endif

    <!-- Data Table Container -->
    <div class="bg-surface-white rounded-2xl border border-outline-variant/30 shadow-[0_10px_30px_rgba(0,35,102,0.05)] overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[920px]">
                <thead>
                    <tr class="bg-surface-container-lowest border-b border-outline-variant/30 text-on-surface-variant">
                        <th class="py-4 px-5 font-label-bold text-label-bold w-24">ID</th>
                        <th class="py-4 px-5 font-label-bold text-label-bold">{{ __('Destination Details') }}</th>
                        <th class="py-4 px-5 font-label-bold text-label-bold">{{ __('Location & Coordinates') }}</th>
                        <th class="py-4 px-5 font-label-bold text-label-bold text-center">{{ __('Status') }}</th>
                        <th class="py-4 px-5 font-label-bold text-label-bold w-32 text-center">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20 text-body-md font-body-md">
                    @php
                        $regionLabels = [
                            'utara'   => __('North Bali'),
                            'selatan' => __('South Bali'),
                            'timur'   => __('East Bali'),
                            'barat'   => __('West Bali'),
                        ];
                    @endphp
                    @forelse($destinations as $destination)
                        @php
                            $hasCoords = $destination->latitude !== null && $destination->longitude !== null;
                            $imgUrl = null;
                            if ($destination->image_path) {
                                $imgUrl = str_starts_with($destination->image_path, 'http')
                                    ? $destination->image_path
                                    : asset('storage/' . $destination->image_path);
                            }
                        @endphp
                        <tr class="hover:bg-surface-container-low/50 transition-colors group">
                            <td class="py-4 px-5 text-outline font-medium">#DST-{{ $destination->id }}</td>
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-4">
                                    @if($imgUrl)
                                        <img src="{{ $imgUrl }}" alt="{{ $destination->name }}" class="w-14 h-14 rounded-lg object-cover shadow-sm border border-outline-variant/20 shrink-0">
                                    @else
                                        <div class="w-14 h-14 rounded-lg bg-surface-container-low border border-outline-variant/30 flex items-center justify-center text-outline/50 shrink-0">
                                            <span class="material-symbols-outlined text-2xl">image</span>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="font-label-bold text-sm text-oceanic-deep mb-0.5 flex flex-wrap items-center gap-1.5">
                                            <span>{{ $destination->name }}</span>
                                            @if($destination->requires_sea_crossing)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-200">
                                                    <span class="material-symbols-outlined text-[12px]">sailing</span>
                                                    <span>{{ __('Requires sea crossing') }}</span>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="font-caption text-[11px] text-on-surface-variant line-clamp-1">
                                            {{ \Illuminate\Support\Str::limit($destination->localized_description, 65) }}
                                        </div>
                                        <div class="text-[10px] text-outline mt-0.5">
                                            {{ __('Default Stay:') }} {{ $destination->default_stay_minutes ?? 120 }} {{ __('mins') }}
                                            @if($destination->region)
                                                • {{ $regionLabels[$destination->region] ?? ucfirst($destination->region) }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-5">
                                <div class="text-on-surface font-medium text-sm">{{ $destination->location }}</div>
                                @if($hasCoords)
                                    <div class="font-mono text-[11px] text-outline mt-0.5">
                                        {{ number_format((float) $destination->latitude, 5) }}, {{ number_format((float) $destination->longitude, 5) }}
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 mt-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                        <span class="material-symbols-outlined text-xs">warning</span>
                                        <span>{{ __('Missing coordinates') }}</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-center">
                                <button type="button"
                                    wire:click="toggleActive({{ $destination->id }})"
                                    title="{{ __('Toggle active status') }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider border transition-colors cursor-pointer {{ $destination->is_active ? 'bg-emerald-100 text-emerald-800 border-emerald-300 hover:bg-emerald-200/70' : 'bg-slate-100 text-slate-700 border-slate-300 hover:bg-slate-200/70' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $destination->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    <span>{{ $destination->is_active ? __('Active') : __('Inactive') }}</span>
                                </button>
                            </td>
                            <td class="py-4 px-5">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="openEdit({{ $destination->id }})" class="p-1.5 text-on-surface-variant hover:text-tropical-teal hover:bg-surface-container-low rounded-md transition-colors" title="{{ __('Edit') }}">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </button>
                                    <button wire:click="delete({{ $destination->id }})" wire:confirm="{{ __('Are you sure you want to delete this destination?') }}" class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container/30 rounded-md transition-colors" title="{{ __('Delete') }}">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-on-surface-variant">
                                <span class="material-symbols-outlined text-4xl mb-2 text-outline/50">map</span>
                                <p class="font-label-bold">{{ __('No destinations found.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-outline-variant/30 bg-surface-container-lowest">
            {{ $destinations->links() }}
        </div>
    </div>

    <!-- Modal Add / Edit Destination -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="$set('showModal', false)" class="absolute inset-0 bg-oceanic-deep/40 backdrop-blur-sm transition-opacity"></div>

            <div class="bg-surface-white w-full max-w-4xl rounded-2xl shadow-[0_20px_60px_rgba(0,35,102,0.15)] relative z-10 flex flex-col max-h-[92vh] border border-outline-variant/30">
                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant/30 bg-surface-white rounded-t-2xl shrink-0">
                    <h2 class="font-headline-md text-xl font-semibold text-oceanic-deep">
                        {{ $editing ? __('Edit Destination') : __('Add New Destination') }}
                    </h2>
                    <button type="button" wire:click="$set('showModal', false)" class="p-1.5 text-outline hover:text-error hover:bg-error-container/20 rounded-full transition-colors">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <!-- Body -->
                <form wire:submit.prevent="save" class="flex flex-col flex-1 overflow-hidden">
                    <div class="p-6 overflow-y-auto flex-1 space-y-5">

                        <!-- Name & Location -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-label-bold text-sm text-oceanic-deep mb-1.5">{{ __('Destination Name') }} <span class="text-error">*</span></label>
                                <input wire:model="name" class="w-full bg-surface-container-low border border-outline-variant/50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-tropical-teal/40 focus:border-tropical-teal" type="text" placeholder="{{ __('e.g. Tegalalang Rice Terrace') }}" required>
                                @error('name') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block font-label-bold text-sm text-oceanic-deep mb-1.5">{{ __('Location / Area') }} <span class="text-error">*</span></label>
                                <input wire:model="location" class="w-full bg-surface-container-low border border-outline-variant/50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-tropical-teal/40 focus:border-tropical-teal" type="text" placeholder="{{ __('e.g. Ubud, Gianyar') }}" required>
                                @error('location') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Interactive Leaflet Map + Nominatim Bali Search -->
                        <div class="p-4 rounded-2xl bg-surface-container-lowest border border-outline-variant/40 space-y-3">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <label class="font-label-bold text-sm text-oceanic-deep flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-tropical-teal text-lg">pin_drop</span>
                                    <span>{{ __('Map Location & Coordinates (Bali)') }} <span class="text-error">*</span></span>
                                </label>
                                <span class="text-[11px] text-outline">{{ __('Click on map or drag pin to set location') }}</span>
                            </div>

                            <!-- Address Search (Nominatim bounded to Bali) -->
                            <div class="space-y-2" wire:ignore>
                                <div class="flex gap-2">
                                    <input type="text"
                                           id="destAddressSearchInput"
                                           placeholder="{{ __('Search address or place in Bali (e.g. Uluwatu Temple)...') }}"
                                           class="flex-1 bg-surface-white border border-outline-variant/50 rounded-xl px-3.5 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-tropical-teal/40">
                                    <button type="button"
                                            id="destAddressSearchBtn"
                                            class="px-4 py-2 rounded-xl bg-oceanic-deep hover:bg-primary text-white font-label-bold text-xs flex items-center gap-1.5 shrink-0 cursor-pointer">
                                        <span class="material-symbols-outlined text-sm">search</span>
                                        <span>{{ __('Search') }}</span>
                                    </button>
                                </div>
                                <div id="destSearchStatus" class="hidden text-xs px-3 py-2 rounded-lg"></div>
                                <div id="destSearchResults" class="hidden max-h-40 overflow-y-auto rounded-xl border border-outline-variant/40 bg-surface-white divide-y divide-outline-variant/20 text-xs shadow-sm"></div>
                            </div>

                            <!-- Leaflet Map Container -->
                            <div wire:ignore>
                                <div id="destinationPickerMap" class="w-full h-64 rounded-xl border border-outline-variant/40 z-0 relative"></div>
                            </div>

                            <!-- Lat / Lng / Region -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                                <div>
                                    <label class="block font-label-bold text-xs text-oceanic-deep mb-1">{{ __('Latitude') }} <span class="text-error">*</span></label>
                                    <input wire:model.live.debounce.400ms="latitude" id="destLatInput" type="number" step="0.000001" placeholder="-8.650000" class="w-full bg-surface-white border border-outline-variant/50 rounded-xl px-3 py-2 text-xs font-mono focus:outline-none focus:ring-1 focus:ring-tropical-teal">
                                    @error('latitude') <span class="text-error text-[11px] mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block font-label-bold text-xs text-oceanic-deep mb-1">{{ __('Longitude') }} <span class="text-error">*</span></label>
                                    <input wire:model.live.debounce.400ms="longitude" id="destLngInput" type="number" step="0.000001" placeholder="115.216667" class="w-full bg-surface-white border border-outline-variant/50 rounded-xl px-3 py-2 text-xs font-mono focus:outline-none focus:ring-1 focus:ring-tropical-teal">
                                    @error('longitude') <span class="text-error text-[11px] mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block font-label-bold text-xs text-oceanic-deep mb-1">{{ __('Bali Region (Auto / Editable)') }} <span class="text-error">*</span></label>
                                    <select wire:model="region" class="w-full bg-surface-white border border-outline-variant/50 rounded-xl px-3 py-2 text-xs font-semibold text-oceanic-deep focus:outline-none focus:ring-1 focus:ring-tropical-teal">
                                        <option value="utara">{{ __('North Bali') }}</option>
                                        <option value="selatan">{{ __('South Bali') }}</option>
                                        <option value="timur">{{ __('East Bali') }}</option>
                                        <option value="barat">{{ __('West Bali') }}</option>
                                    </select>
                                    @error('region') <span class="text-error text-[11px] mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Supporting Options: Stay Duration, Sea Crossing, Active Status -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                            <div>
                                <label class="block font-label-bold text-sm text-oceanic-deep mb-1.5">{{ __('Default Stay Duration (minutes)') }} <span class="text-error">*</span></label>
                                <input wire:model="default_stay_minutes" type="number" min="30" max="720" step="15" class="w-full bg-surface-container-low border border-outline-variant/50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-tropical-teal/40" required>
                                @error('default_stay_minutes') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-outline-variant/40 bg-surface-container-low cursor-pointer mt-2 sm:mt-6">
                                <input type="checkbox" wire:model="requires_sea_crossing" class="rounded text-tropical-teal focus:ring-tropical-teal">
                                <span class="text-xs font-bold text-oceanic-deep">{{ __('Requires sea crossing') }}</span>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-outline-variant/40 bg-surface-container-low cursor-pointer mt-2 sm:mt-6">
                                <input type="checkbox" wire:model="is_active" class="rounded text-tropical-teal focus:ring-tropical-teal">
                                <span class="text-xs font-bold text-oceanic-deep">{{ __('Active destination') }}</span>
                            </label>
                        </div>

                        <!-- Bilingual Descriptions (EN & ID) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-label-bold text-sm text-oceanic-deep mb-1.5">{{ __('Description (English)') }} <span class="text-error">*</span></label>
                                <textarea wire:model="description" class="w-full bg-surface-container-low border border-outline-variant/50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-tropical-teal/40 resize-none" rows="3" placeholder="{{ __('Describe this destination in English...') }}" required></textarea>
                                @error('description') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block font-label-bold text-sm text-oceanic-deep mb-1.5">{{ __('Description (Indonesian - Optional)') }}</label>
                                <textarea wire:model="description_id" class="w-full bg-surface-container-low border border-outline-variant/50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-tropical-teal/40 resize-none" rows="3" placeholder="{{ __('Describe this destination in Indonesian (falls back to English if empty)...') }}"></textarea>
                                @error('description_id') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div>
                            <label class="block font-label-bold text-sm text-oceanic-deep mb-1.5">{{ __('Destination Image') }}</label>
                            <input id="dest-image-{{ $iteration }}" wire:model="image" type="file" class="block w-full text-sm text-outline file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-label-bold file:bg-surface-container-low file:text-oceanic-deep hover:file:bg-surface-container transition-all cursor-pointer">
                            @error('image') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 border-t border-outline-variant/30 bg-surface-container-lowest rounded-b-2xl flex justify-end gap-3 shrink-0">
                        <button type="button" wire:click="$set('showModal', false)" class="px-6 py-2.5 rounded-full border border-outline-variant text-on-surface-variant hover:bg-surface-container-low hover:text-oceanic-deep font-label-bold text-sm transition-colors">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="image, save" class="px-6 py-2.5 rounded-full bg-tropical-teal hover:bg-oceanic-deep text-surface-white font-label-bold text-sm transition-colors shadow-sm flex items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            <span wire:loading.remove wire:target="image">{{ __('Save Destination') }}</span>
                            <span wire:loading wire:target="image">{{ __('Uploading...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @script
    <script>
        let destMap = null;
        let destMarker = null;

        const i18nDestMap = {
            searching: @json(__('Searching address in Bali...')),
            notFound: @json(__('Address not found in Bali. Please try another keyword or click/drag the pin directly on the map.')),
            searchError: @json(__('Could not reach address search service. Please click or drag the pin directly on the map.')),
            enterKeyword: @json(__('Please enter a place name or address in Bali first.')),
        };

        function initDestinationPickerMap(lat, lng) {
            const container = document.getElementById('destinationPickerMap');
            if (!container || typeof L === 'undefined') {
                setTimeout(() => initDestinationPickerMap(lat, lng), 80);
                return;
            }

            if (destMap) {
                destMap.remove();
                destMap = null;
                destMarker = null;
            }

            const startLat = Number.isFinite(Number(lat)) ? Number(lat) : -8.650000;
            const startLng = Number.isFinite(Number(lng)) ? Number(lng) : 115.216667;

            destMap = L.map('destinationPickerMap', { scrollWheelZoom: true }).setView([startLat, startLng], 11);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(destMap);

            destMarker = L.marker([startLat, startLng], { draggable: true }).addTo(destMap);

            function syncPointToLivewire(newLat, newLng, suggestedLocation = null) {
                const roundedLat = Number(newLat.toFixed(6));
                const roundedLng = Number(newLng.toFixed(6));
                destMarker.setLatLng([roundedLat, roundedLng]);
                $wire.setCoordinatesFromMap(roundedLat, roundedLng, suggestedLocation);
            }

            destMarker.on('dragend', function () {
                const pos = destMarker.getLatLng();
                syncPointToLivewire(pos.lat, pos.lng);
            });

            destMap.on('click', function (e) {
                syncPointToLivewire(e.latlng.lat, e.latlng.lng);
            });

            // Sync manual lat/lng typing back to map pin
            const latInput = document.getElementById('destLatInput');
            const lngInput = document.getElementById('destLngInput');
            function movePinFromInputs() {
                if (!destMap || !destMarker || !latInput || !lngInput) return;
                const vLat = parseFloat(latInput.value);
                const vLng = parseFloat(lngInput.value);
                if (Number.isFinite(vLat) && Number.isFinite(vLng) && vLat >= -8.95 && vLat <= -8.00 && vLng >= 114.40 && vLng <= 115.75) {
                    destMarker.setLatLng([vLat, vLng]);
                    destMap.panTo([vLat, vLng]);
                }
            }
            if (latInput && lngInput) {
                latInput.addEventListener('change', movePinFromInputs);
                lngInput.addEventListener('change', movePinFromInputs);
            }

            // Nominatim Geocoding Search (bounded to Bali)
            const searchInput = document.getElementById('destAddressSearchInput');
            const searchBtn = document.getElementById('destAddressSearchBtn');
            const statusBox = document.getElementById('destSearchStatus');
            const resultsBox = document.getElementById('destSearchResults');

            async function performBaliSearch() {
                if (!searchInput || !statusBox || !resultsBox) return;
                const query = searchInput.value.trim();
                resultsBox.innerHTML = '';
                resultsBox.classList.add('hidden');

                if (!query) {
                    statusBox.textContent = i18nDestMap.enterKeyword;
                    statusBox.className = 'text-xs px-3 py-2 rounded-lg bg-amber-50 text-amber-900 border border-amber-200';
                    statusBox.classList.remove('hidden');
                    return;
                }

                statusBox.textContent = i18nDestMap.searching;
                statusBox.className = 'text-xs px-3 py-2 rounded-lg bg-surface-container-low text-outline border border-outline-variant/30';
                statusBox.classList.remove('hidden');

                try {
                    const url = `https://nominatim.openstreetmap.org/search?format=json&limit=5&countrycodes=id&bounded=1&viewbox=114.40,-8.00,115.75,-8.95&q=${encodeURIComponent(query + ', Bali')}`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();

                    if (!Array.isArray(data) || data.length === 0) {
                        statusBox.textContent = i18nDestMap.notFound;
                        statusBox.className = 'text-xs px-3 py-2 rounded-lg bg-amber-50 text-amber-900 border border-amber-200';
                        statusBox.classList.remove('hidden');
                        return;
                    }

                    statusBox.classList.add('hidden');
                    resultsBox.innerHTML = '';
                    data.forEach((item) => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'w-full text-left px-3 py-2 hover:bg-surface-container-low transition-colors cursor-pointer block';
                        btn.textContent = item.display_name;
                        btn.addEventListener('click', () => {
                            const rLat = parseFloat(item.lat);
                            const rLng = parseFloat(item.lon);
                            if (Number.isFinite(rLat) && Number.isFinite(rLng)) {
                                destMap.setView([rLat, rLng], 14);
                                const shortLoc = item.display_name.split(',').slice(0, 2).join(',').trim();
                                syncPointToLivewire(rLat, rLng, shortLoc);
                                resultsBox.classList.add('hidden');
                            }
                        });
                        resultsBox.appendChild(btn);
                    });
                    resultsBox.classList.remove('hidden');
                } catch (err) {
                    statusBox.textContent = i18nDestMap.searchError;
                    statusBox.className = 'text-xs px-3 py-2 rounded-lg bg-amber-50 text-amber-900 border border-amber-200';
                    statusBox.classList.remove('hidden');
                }
            }

            if (searchBtn) {
                searchBtn.onclick = performBaliSearch;
            }
            if (searchInput) {
                searchInput.onkeydown = (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        performBaliSearch();
                    }
                };
            }

            setTimeout(() => {
                if (destMap) destMap.invalidateSize();
            }, 200);
        }

        $wire.on('destination-map-open', (payload) => {
            const data = Array.isArray(payload) ? (payload[0] || {}) : (payload || {});
            setTimeout(() => {
                initDestinationPickerMap(data.lat, data.lng);
            }, 120);
        });
    </script>
    @endscript
</div>
