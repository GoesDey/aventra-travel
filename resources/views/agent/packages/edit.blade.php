<x-layouts.agent title="Edit Tour Package - {{ $package->name }}">
    <div class="max-w-5xl mx-auto space-y-6">

        <!-- Navigation Bar / Breadcrumb -->
        <div class="flex items-center gap-3 text-sm">
            <a href="{{ route('agent.packages.index') }}" class="text-tropical-teal hover:underline flex items-center gap-1">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                <span>Back to Packages</span>
            </a>
            <span class="text-outline">/</span>
            <span class="text-on-surface-variant font-medium">Edit Package</span>
        </div>

        <!-- Form Card -->
        <form action="{{ route('agent.packages.update', $package->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Section 1: Basic Package Details -->
            <div class="bg-surface-white p-6 md:p-8 rounded-2xl border border-outline-variant/30 shadow-sm space-y-6">
                <div>
                    <h2 class="font-display-lg text-lg md:text-xl font-bold text-oceanic-deep">Edit Basic Package Details</h2>
                    <p class="text-xs text-on-surface-variant mt-0.5">Update the parameters of your tour package.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Paket -->
                    <div class="md:col-span-2 space-y-1.5">
                        <label for="name" class="block font-label-bold text-sm text-on-surface">Tour Package Name <span class="text-error">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $package->name) }}" required
                            class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow @error('name') border-error @enderror">
                        @error('name') <span class="text-error text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Kategori (Dropdown) -->
                    <div class="space-y-1.5">
                        <label for="category" class="block font-label-bold text-sm text-on-surface">Package Category <span class="text-error">*</span></label>
                        <select name="category" id="category" required
                            class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow @error('category') border-error @enderror">
                            <option value="reguler" {{ old('category', $package->category) === 'reguler' ? 'selected' : '' }}>Regular (Open Trip / Scheduled)</option>
                            <option value="custom" {{ old('category', $package->category) === 'custom' ? 'selected' : '' }}>Custom (Private / Flexible Itinerary)</option>
                        </select>
                        @error('category') <span class="text-error text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Region (Dropdown) -->
                    <div class="space-y-1.5">
                        <label for="region" class="block font-label-bold text-sm text-on-surface">Region / Area <span class="text-error">*</span></label>
                        <select name="region" id="region" required
                            class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow @error('region') border-error @enderror">
                            <option value="utara" {{ old('region', $package->region) === 'utara' ? 'selected' : '' }}>North Bali (Buleleng, Singaraja, Lovina)</option>
                            <option value="selatan" {{ old('region', $package->region) === 'selatan' ? 'selected' : '' }}>South Bali (Badung, Denpasar, Gianyar, Kuta)</option>
                            <option value="timur" {{ old('region', $package->region) === 'timur' ? 'selected' : '' }}>East Bali (Nusa Penida, Karangasem, Klungkung)</option>
                            <option value="barat" {{ old('region', $package->region) === 'barat' ? 'selected' : '' }}>West Bali (Tabanan, Jembrana, Medewi)</option>
                        </select>
                        @error('region') <span class="text-error text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Harga Per Pax -->
                    <div class="space-y-1.5">
                        <label for="price_per_pax" class="block font-label-bold text-sm text-on-surface">Price per Person (Rp) <span class="text-error">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-outline font-medium text-sm">Rp</span>
                            <input type="number" name="price_per_pax" id="price_per_pax" value="{{ old('price_per_pax', (int)$package->price_per_pax) }}" min="10000" max="99999999" step="1000" required
                                class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl pl-12 pr-4 py-3 text-sm focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow @error('price_per_pax') border-error @enderror">
                        </div>
                        @error('price_per_pax') <span class="text-error text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Durasi Hari -->
                    <div class="space-y-1.5">
                        <label for="duration_days" class="block font-label-bold text-sm text-on-surface">Duration (Days) <span class="text-error">*</span></label>
                        <input type="number" name="duration_days" id="duration_days" value="{{ old('duration_days', $package->duration_days) }}" min="1" max="30" required
                            class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow @error('duration_days') border-error @enderror">
                        @error('duration_days') <span class="text-error text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Maksimal Tamu / Peserta -->
                    <div class="space-y-1.5">
                        <label for="max_guests" class="block font-label-bold text-sm text-on-surface">Max Guests per Booking <span class="text-error">*</span></label>
                        <input type="number" name="max_guests" id="max_guests" value="{{ old('max_guests', $package->max_guests) }}" min="1" max="100" required
                            class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow @error('max_guests') border-error @enderror">
                        @error('max_guests') <span class="text-error text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Foto Cover -->
                    <div class="space-y-2">
                        <label for="cover_image" class="block font-label-bold text-sm text-on-surface">Cover Image</label>
                        @if($package->cover_image)
                            <div class="flex items-center gap-3 mb-2">
                                <img src="{{ str_starts_with($package->cover_image, 'http') ? $package->cover_image : asset('storage/' . $package->cover_image) }}" alt="{{ $package->name }}" class="w-16 h-16 rounded-xl object-cover border border-outline-variant/30">
                                <span class="text-xs text-outline">Current cover. Upload a new image to replace it.</span>
                            </div>
                        @endif
                        <input type="file" name="cover_image" id="cover_image" accept="image/*"
                            class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-4 py-2.5 text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-tropical-teal/10 file:text-tropical-teal hover:file:bg-tropical-teal/20 focus:outline-none">
                        <span class="text-[11px] text-outline">Supported formats: JPG, PNG, WEBP. Max 2MB.</span>
                        @error('cover_image') <span class="text-error text-xs block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Deskripsi -->
                    <div class="md:col-span-2 space-y-1.5">
                        <label for="description" class="block font-label-bold text-sm text-on-surface">Package Description <span class="text-error">*</span></label>
                        <textarea name="description" id="description" rows="5" required
                            class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl p-4 text-sm focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow @error('description') border-error @enderror">{{ old('description', $package->description) }}</textarea>
                        @error('description') <span class="text-error text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Destinasi Wisata Pilihan -->
                    @if($destinations->isNotEmpty())
                        <div class="md:col-span-2 space-y-2 pt-2 border-t border-outline-variant/20">
                            <label class="block font-label-bold text-sm text-on-surface">Destinations Included</label>
                            @php
                                $selectedDestIds = old('destinations', $package->destinations->pluck('id')->toArray());
                            @endphp
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 max-h-48 overflow-y-auto p-2 bg-surface-container-lowest border border-outline-variant/30 rounded-xl">
                                @foreach($destinations as $dest)
                                    <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-surface-container-low cursor-pointer transition-colors text-xs">
                                        <input type="checkbox" name="destinations[]" value="{{ $dest->id }}" {{ in_array($dest->id, $selectedDestIds) ? 'checked' : '' }} class="rounded border-outline-variant text-tropical-teal focus:ring-tropical-teal">
                                        <span class="font-medium text-on-surface truncate">{{ $dest->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Status Aktif / Publikasikan -->
                    <div class="md:col-span-2 flex items-center gap-3 pt-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded border-outline-variant text-tropical-teal focus:ring-tropical-teal">
                        <label for="is_active" class="text-sm font-semibold text-on-surface cursor-pointer">
                            Publish this tour package (Active and visible for customer bookings)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Section 2: Jadwal Perjalanan & Kuota (Dynamic Input) -->
            <div class="bg-surface-white p-6 md:p-8 rounded-2xl border border-outline-variant/30 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="font-display-lg text-lg md:text-xl font-bold text-oceanic-deep">Departure Schedules &amp; Quotas</h2>
                        <p class="text-xs text-on-surface-variant mt-0.5">Manage departure dates, return dates, and quota for each schedule.</p>
                    </div>
                    <button type="button" id="addScheduleBtn" class="inline-flex items-center gap-2 bg-tropical-teal/10 hover:bg-tropical-teal/20 text-tropical-teal font-label-bold text-xs px-4 py-2.5 rounded-xl transition-colors shrink-0">
                        <span class="material-symbols-outlined text-base">add</span>
                        <span>Add Schedule</span>
                    </button>
                </div>

                @error('schedules')
                    <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-medium">
                        {{ $message }}
                    </div>
                @enderror

                <div id="scheduleInlineError" class="hidden p-3 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl text-xs font-medium"></div>

                <!-- Dynamic Container -->
                <div id="schedulesContainer" class="space-y-4">
                    <!-- Dynamic Rows will be inserted here by JavaScript -->
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-4 pt-2">
                <a href="{{ route('agent.packages.index') }}" class="px-6 py-3 rounded-xl border border-outline-variant/50 text-on-surface hover:bg-surface-container-low font-semibold text-sm transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-3 rounded-xl bg-tropical-teal hover:bg-secondary text-white font-label-bold text-sm shadow-md hover:shadow-lg transition-all duration-200">
                    Update Package &amp; Schedules
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('schedulesContainer');
            const addBtn = document.getElementById('addScheduleBtn');
            const durationInput = document.getElementById('duration_days');
            const maxGuestsInput = document.getElementById('max_guests');
            const inlineErrorBox = document.getElementById('scheduleInlineError');

            @php
                $minScheduleDate = now(config('app.timezone', 'Asia/Makassar'))->addDays(3)->toDateString();
                $existingSchedules = $package->schedules->map(function ($sch) {
                    return [
                        'id'             => $sch->id,
                        'start_date'     => $sch->start_date ? $sch->start_date->format('Y-m-d') : '',
                        'end_date'       => $sch->end_date ? $sch->end_date->format('Y-m-d') : '',
                        'quota'          => $sch->quota,
                        'bookings_count' => (int) ($sch->bookings_count ?? 0),
                    ];
                })->toArray();
                $bookingsCountById = collect($existingSchedules)->pluck('bookings_count', 'id')->toArray();
                $initialSchedulesData = old('schedules', $existingSchedules);
            @endphp

            const minScheduleDate = @json($minScheduleDate);
            const initialSchedules = @json($initialSchedulesData);
            const bookingsCountById = @json($bookingsCountById);
            const validationErrors = @json($errors->getMessages());
            const minOneScheduleMsg = @json(__('At least one departure schedule is required.'));
            const lockedScheduleMsg = @json(__('This schedule already has bookings and cannot be deleted.'));
            const hasBookingsLabel = @json(__('Has bookings'));
            let scheduleIndex = 0;

            function showInlineWarning(msg) {
                if (!inlineErrorBox) return;
                inlineErrorBox.textContent = msg;
                inlineErrorBox.classList.remove('hidden');
            }

            function computeEndDate(startDateStr) {
                if (!startDateStr) return '';
                const days = Math.max(1, parseInt(durationInput?.value || '1', 10) || 1);
                const d = new Date(startDateStr + 'T00:00:00');
                if (isNaN(d.getTime())) return '';
                d.setDate(d.getDate() + (days - 1));
                const pad = (n) => String(n).padStart(2, '0');
                return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
            }

            function fieldErr(index, field) {
                const key = `schedules.${index}.${field}`;
                return validationErrors[key] && validationErrors[key][0] ? validationErrors[key][0] : '';
            }

            function createScheduleRow(index, data = {}) {
                const row = document.createElement('div');
                row.className = 'schedule-row bg-surface-container-low/50 p-4 rounded-xl border border-outline-variant/30 transition-all';
                row.dataset.index = index;

                const scheduleId = data.id || '';
                const bookingsCount = scheduleId ? (bookingsCountById[scheduleId] || data.bookings_count || 0) : 0;
                const startDate = data.start_date || '';
                const endDate = data.end_date || computeEndDate(startDate);
                const maxGuests = Math.max(1, parseInt(maxGuestsInput?.value || '10', 10) || 10);
                const quota = data.quota || Math.min(10, maxGuests);

                const startErr = fieldErr(index, 'start_date');
                const endErr = fieldErr(index, 'end_date');
                const quotaErr = fieldErr(index, 'quota');

                row.innerHTML = `
                    <input type="hidden" name="schedules[${index}][id]" value="${scheduleId}">
                    <div class="flex flex-col md:flex-row md:items-start gap-4">
                        <div class="flex-1 space-y-1">
                            <label class="block font-label-bold text-xs text-on-surface">
                                Departure Date <span class="text-error">*</span>
                                ${bookingsCount > 0 ? `<span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">${hasBookingsLabel} (${bookingsCount})</span>` : ''}
                            </label>
                            <input type="date" name="schedules[${index}][start_date]" value="${startDate}" ${!scheduleId ? `min="${minScheduleDate}"` : ''} required
                                class="w-full bg-surface-white border ${startErr ? 'border-error' : 'border-outline-variant/40'} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-tropical-teal focus:outline-none schedule-start">
                            ${startErr ? `<span class="text-error text-xs block">${startErr}</span>` : ''}
                        </div>
                        <div class="flex-1 space-y-1">
                            <label class="block font-label-bold text-xs text-on-surface">Return Date <span class="text-error">*</span></label>
                            <input type="date" name="schedules[${index}][end_date]" value="${endDate}" readonly
                                class="w-full bg-surface-container-low border ${endErr ? 'border-error' : 'border-outline-variant/40'} rounded-lg px-3 py-2 text-sm text-on-surface-variant schedule-end">
                            ${endErr ? `<span class="text-error text-xs block">${endErr}</span>` : ''}
                        </div>
                        <div class="w-full md:w-36 space-y-1">
                            <label class="block font-label-bold text-xs text-on-surface">Quota (Seats) <span class="text-error">*</span></label>
                            <input type="number" name="schedules[${index}][quota]" value="${quota}" min="1" max="${maxGuests}" required
                                class="w-full bg-surface-white border ${quotaErr ? 'border-error' : 'border-outline-variant/40'} rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-tropical-teal focus:outline-none schedule-quota">
                            ${quotaErr ? `<span class="text-error text-xs block">${quotaErr}</span>` : ''}
                        </div>
                        <div class="shrink-0 flex items-center justify-end md:pt-5">
                            <button type="button" class="removeScheduleBtn p-2 rounded-lg transition-colors ${bookingsCount > 0 ? 'text-outline/40 cursor-not-allowed' : 'text-error hover:bg-rose-100'}" title="${bookingsCount > 0 ? lockedScheduleMsg : 'Delete Schedule'}">
                                <span class="material-symbols-outlined text-xl">${bookingsCount > 0 ? 'lock' : 'delete'}</span>
                            </button>
                        </div>
                    </div>
                `;

                const startEl = row.querySelector('.schedule-start');
                const endEl = row.querySelector('.schedule-end');
                startEl.addEventListener('change', function () {
                    endEl.value = computeEndDate(startEl.value);
                });

                row.querySelector('.removeScheduleBtn').addEventListener('click', function () {
                    if (bookingsCount > 0) {
                        showInlineWarning(lockedScheduleMsg);
                        return;
                    }
                    const totalRows = container.querySelectorAll('.schedule-row').length;
                    if (totalRows <= 1) {
                        showInlineWarning(minOneScheduleMsg);
                        return;
                    }
                    if (inlineErrorBox) inlineErrorBox.classList.add('hidden');
                    row.remove();
                });

                container.appendChild(row);
            }

            if (durationInput) {
                durationInput.addEventListener('change', function () {
                    container.querySelectorAll('.schedule-row').forEach((row) => {
                        const s = row.querySelector('.schedule-start');
                        const e = row.querySelector('.schedule-end');
                        if (s && e && s.value) e.value = computeEndDate(s.value);
                    });
                });
            }

            if (maxGuestsInput) {
                maxGuestsInput.addEventListener('change', function () {
                    const maxG = Math.max(1, parseInt(maxGuestsInput.value || '10', 10) || 10);
                    container.querySelectorAll('.schedule-quota').forEach((q) => { q.max = maxG; });
                });
            }

            if (Array.isArray(initialSchedules) && initialSchedules.length > 0) {
                initialSchedules.forEach(item => createScheduleRow(scheduleIndex++, item));
            } else {
                createScheduleRow(scheduleIndex++, { start_date: minScheduleDate, end_date: computeEndDate(minScheduleDate) });
            }

            addBtn.addEventListener('click', function () {
                if (inlineErrorBox) inlineErrorBox.classList.add('hidden');
                createScheduleRow(scheduleIndex++, { start_date: minScheduleDate, end_date: computeEndDate(minScheduleDate) });
            });
        });
    </script>
    @endpush
</x-layouts.agent>
