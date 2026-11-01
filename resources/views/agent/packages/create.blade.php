<x-layouts.agent title="Tambah Paket Wisata Baru">
    <div class="max-w-5xl mx-auto space-y-6">

        <!-- Navigation Bar / Breadcrumb -->
        <div class="flex items-center gap-3 text-sm">
            <a href="{{ route('agent.packages.index') }}" class="text-tropical-teal hover:underline flex items-center gap-1">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                <span>Kembali ke Daftar Paket</span>
            </a>
            <span class="text-outline">/</span>
            <span class="text-on-surface-variant font-medium">Tambah Paket Baru</span>
        </div>

        <!-- Form Card -->
        <form action="{{ route('agent.packages.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- Section 1: Informasi Utama Paket -->
            <div class="bg-surface-white p-6 md:p-8 rounded-2xl border border-outline-variant/30 shadow-sm space-y-6">
                <div>
                    <h2 class="font-display-lg text-lg md:text-xl font-bold text-oceanic-deep">Informasi Dasar Paket</h2>
                    <p class="text-xs text-on-surface-variant mt-0.5">Lengkapi data dasar paket perjalanan wisata yang Anda sediakan.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Paket -->
                    <div class="md:col-span-2 space-y-1.5">
                        <label for="name" class="block font-label-bold text-sm text-on-surface">Nama Paket Wisata <span class="text-error">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: Eksplorasi Nusa Penida Timur & Snorkeling" required
                            class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow @error('name') border-error @enderror">
                        @error('name') <span class="text-error text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Kategori (Dropdown) -->
                    <div class="space-y-1.5">
                        <label for="category" class="block font-label-bold text-sm text-on-surface">Kategori Paket <span class="text-error">*</span></label>
                        <select name="category" id="category" required
                            class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow @error('category') border-error @enderror">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="reguler" {{ old('category', 'reguler') === 'reguler' ? 'selected' : '' }}>Reguler (Open Trip / Terjadwal)</option>
                            <option value="custom" {{ old('category') === 'custom' ? 'selected' : '' }}>Custom (Private / Fleksibel)</option>
                        </select>
                        @error('category') <span class="text-error text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Region (Dropdown) -->
                    <div class="space-y-1.5">
                        <label for="region" class="block font-label-bold text-sm text-on-surface">Region / Wilayah <span class="text-error">*</span></label>
                        <select name="region" id="region" required
                            class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow @error('region') border-error @enderror">
                            <option value="">-- Pilih Wilayah --</option>
                            <option value="timur" {{ old('region', 'timur') === 'timur' ? 'selected' : '' }}>Bali Timur (Nusa Penida, Karangasem, Klungkung)</option>
                            <option value="barat" {{ old('region') === 'barat' ? 'selected' : '' }}>Bali Barat (Tabanan, Jembrana, Buleleng)</option>
                            <option value="lainnya" {{ old('region') === 'lainnya' ? 'selected' : '' }}>Lainnya / Seluruh Bali</option>
                        </select>
                        @error('region') <span class="text-error text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Harga Per Pax -->
                    <div class="space-y-1.5">
                        <label for="price_per_pax" class="block font-label-bold text-sm text-on-surface">Harga per Pax (Rp) <span class="text-error">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-outline font-medium text-sm">Rp</span>
                            <input type="number" name="price_per_pax" id="price_per_pax" value="{{ old('price_per_pax') }}" min="0" step="1000" placeholder="750000" required
                                class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl pl-12 pr-4 py-3 text-sm focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow @error('price_per_pax') border-error @enderror">
                        </div>
                        @error('price_per_pax') <span class="text-error text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Durasi Hari -->
                    <div class="space-y-1.5">
                        <label for="duration_days" class="block font-label-bold text-sm text-on-surface">Durasi (Hari) <span class="text-error">*</span></label>
                        <input type="number" name="duration_days" id="duration_days" value="{{ old('duration_days', 1) }}" min="1" required
                            class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow @error('duration_days') border-error @enderror">
                        @error('duration_days') <span class="text-error text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Maksimal Tamu / Peserta -->
                    <div class="space-y-1.5">
                        <label for="max_guests" class="block font-label-bold text-sm text-on-surface">Maksimal Peserta per Tour <span class="text-error">*</span></label>
                        <input type="number" name="max_guests" id="max_guests" value="{{ old('max_guests', 10) }}" min="1" required
                            class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow @error('max_guests') border-error @enderror">
                        @error('max_guests') <span class="text-error text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Foto Cover -->
                    <div class="space-y-1.5">
                        <label for="cover_image" class="block font-label-bold text-sm text-on-surface">Foto Cover Paket</label>
                        <input type="file" name="cover_image" id="cover_image" accept="image/*"
                            class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-4 py-2.5 text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-tropical-teal/10 file:text-tropical-teal hover:file:bg-tropical-teal/20 focus:outline-none">
                        <span class="text-[11px] text-outline">Format: JPG, PNG, WEBP. Maks 2MB.</span>
                        @error('cover_image') <span class="text-error text-xs block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Deskripsi -->
                    <div class="md:col-span-2 space-y-1.5">
                        <label for="description" class="block font-label-bold text-sm text-on-surface">Deskripsi Paket <span class="text-error">*</span></label>
                        <textarea name="description" id="description" rows="5" required placeholder="Tuliskan gambaran lengkap perjalanan, fasilitas yang didapat, itinerary singkat, dll..."
                            class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl p-4 text-sm focus:ring-2 focus:ring-tropical-teal focus:outline-none transition-shadow @error('description') border-error @enderror">{{ old('description') }}</textarea>
                        @error('description') <span class="text-error text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Destinasi Wisata Pilihan -->
                    @if($destinations->isNotEmpty())
                        <div class="md:col-span-2 space-y-2 pt-2 border-t border-outline-variant/20">
                            <label class="block font-label-bold text-sm text-on-surface">Destinasi yang Dikunjungi (Opsional)</label>
                            <p class="text-xs text-outline mb-2">Pilih destinasi yang termasuk dalam paket perjalanan ini:</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 max-h-48 overflow-y-auto p-2 bg-surface-container-lowest border border-outline-variant/30 rounded-xl">
                                @foreach($destinations as $dest)
                                    <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-surface-container-low cursor-pointer transition-colors text-xs">
                                        <input type="checkbox" name="destinations[]" value="{{ $dest->id }}" {{ in_array($dest->id, old('destinations', [])) ? 'checked' : '' }} class="rounded border-outline-variant text-tropical-teal focus:ring-tropical-teal">
                                        <span class="font-medium text-on-surface truncate">{{ $dest->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Status Aktif / Publikasikan -->
                    <div class="md:col-span-2 flex items-center gap-3 pt-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="w-4 h-4 rounded border-outline-variant text-tropical-teal focus:ring-tropical-teal">
                        <label for="is_active" class="text-sm font-semibold text-on-surface cursor-pointer">
                            Publikasikan paket ini (Paket Aktif & dapat dipesan pengguna)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Section 2: Jadwal Perjalanan & Kuota (Dynamic Input) -->
            <div class="bg-surface-white p-6 md:p-8 rounded-2xl border border-outline-variant/30 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="font-display-lg text-lg md:text-xl font-bold text-oceanic-deep">Jadwal Perjalanan & Kuota</h2>
                        <p class="text-xs text-on-surface-variant mt-0.5">Atur tanggal keberangkatan, tanggal selesai, dan kuota peserta untuk setiap jadwal.</p>
                    </div>
                    <button type="button" id="addScheduleBtn" class="inline-flex items-center gap-2 bg-tropical-teal/10 hover:bg-tropical-teal/20 text-tropical-teal font-label-bold text-xs px-4 py-2.5 rounded-xl transition-colors shrink-0">
                        <span class="material-symbols-outlined text-base">add</span>
                        <span>Tambah Jadwal</span>
                    </button>
                </div>

                @error('schedules')
                    <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-medium">
                        {{ $message }}
                    </div>
                @enderror

                <!-- Dynamic Container -->
                <div id="schedulesContainer" class="space-y-4">
                    <!-- Dynamic Rows will be inserted here by JavaScript -->
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-4 pt-2">
                <a href="{{ route('agent.packages.index') }}" class="px-6 py-3 rounded-xl border border-outline-variant/50 text-on-surface hover:bg-surface-container-low font-semibold text-sm transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-8 py-3 rounded-xl bg-tropical-teal hover:bg-secondary text-white font-label-bold text-sm shadow-md hover:shadow-lg transition-all duration-200">
                    Simpan Paket & Jadwal
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('schedulesContainer');
            const addBtn = document.getElementById('addScheduleBtn');

            // Default data from old input if validation failed, or at least 1 empty row
            const oldSchedules = @json(old('schedules', [['start_date' => '', 'end_date' => '', 'quota' => 10]]));
            let scheduleIndex = 0;

            function createScheduleRow(index, data = {}) {
                const row = document.createElement('div');
                row.className = 'schedule-row bg-surface-container-low/50 p-4 rounded-xl border border-outline-variant/30 transition-all';
                row.dataset.index = index;

                const startDate = data.start_date || '';
                const endDate = data.end_date || '';
                const quota = data.quota || 10;

                row.innerHTML = `
                    <div class="flex flex-col md:flex-row md:items-end gap-4">
                        <div class="flex-1 space-y-1">
                            <label class="block font-label-bold text-xs text-on-surface">Tanggal Mulai <span class="text-error">*</span></label>
                            <input type="date" name="schedules[${index}][start_date]" value="${startDate}" required
                                class="w-full bg-surface-white border border-outline-variant/40 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-tropical-teal focus:outline-none schedule-start">
                        </div>
                        <div class="flex-1 space-y-1">
                            <label class="block font-label-bold text-xs text-on-surface">Tanggal Selesai <span class="text-error">*</span></label>
                            <input type="date" name="schedules[${index}][end_date]" value="${endDate}" required
                                class="w-full bg-surface-white border border-outline-variant/40 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-tropical-teal focus:outline-none schedule-end">
                        </div>
                        <div class="w-full md:w-36 space-y-1">
                            <label class="block font-label-bold text-xs text-on-surface">Kuota Peserta <span class="text-error">*</span></label>
                            <input type="number" name="schedules[${index}][quota]" value="${quota}" min="1" required
                                class="w-full bg-surface-white border border-outline-variant/40 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-tropical-teal focus:outline-none">
                        </div>
                        <div class="shrink-0 flex items-center justify-end">
                            <button type="button" class="removeScheduleBtn p-2 text-error hover:bg-rose-100 rounded-lg transition-colors" title="Hapus Jadwal">
                                <span class="material-symbols-outlined text-xl">delete</span>
                            </button>
                        </div>
                    </div>
                `;

                // Add delete listener
                row.querySelector('.removeScheduleBtn').addEventListener('click', function () {
                    const totalRows = container.querySelectorAll('.schedule-row').length;
                    if (totalRows <= 1) {
                        alert('Minimal satu jadwal perjalanan wajib tersedia.');
                        return;
                    }
                    row.remove();
                });

                container.appendChild(row);
            }

            // Render initial rows
            if (Array.isArray(oldSchedules) && oldSchedules.length > 0) {
                oldSchedules.forEach(item => {
                    createScheduleRow(scheduleIndex++, item);
                });
            } else {
                createScheduleRow(scheduleIndex++);
            }

            // Add row button
            addBtn.addEventListener('click', function () {
                createScheduleRow(scheduleIndex++);
            });
        });
    </script>
    @endpush
</x-layouts.agent>
