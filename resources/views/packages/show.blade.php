<x-layouts.app title="{{ $package->name }} - TriHitaTour">
    <!-- Leaflet.js CSS & JS (OpenStreetMap) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Leaflet Routing Machine CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
    <style>
        .leaflet-routing-container {
            display: none !important;
        }
    </style>

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
                <a href="{{ route('home') }}" class="hover:text-tropical-teal transition-colors">Beranda</a>
                <span>/</span>
                <a href="{{ route('packages.index') }}" class="hover:text-tropical-teal transition-colors">Paket Wisata</a>
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
                        <div class="font-bold text-sm">Paket Wisata Sedang Belum Aktif</div>
                        <div class="text-xs text-amber-800">Paket ini masih dalam tahap penyiapan oleh agen atau sedang dinonaktifkan sementara dan belum dapat dipesan.</div>
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
                                Bali {{ ucfirst($package->region) }}
                            </span>
                        </div>

                        <!-- Bottom Overlaid Content -->
                        <div class="absolute bottom-6 left-6 right-6 text-white space-y-2">
                            <div class="flex items-center gap-2">
                                <div class="bg-amber-500 text-white px-2.5 py-0.5 rounded-full text-xs font-bold flex items-center gap-1 shadow">
                                    <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span>{{ number_format($package->avg_rating, 1) }}</span>
                                </div>
                                <span class="text-xs text-white/80">Diselenggarakan oleh: {{ $package->agent->agent_name ?? 'Agen Resmi' }}</span>
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
                                <span class="text-[11px] text-outline block">Durasi</span>
                                <span class="text-sm font-bold text-oceanic-deep">{{ $package->duration_days }} Hari</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-tropical-teal/10 text-tropical-teal flex items-center justify-center">
                                <span class="material-symbols-outlined">group</span>
                            </div>
                            <div>
                                <span class="text-[11px] text-outline block">Maks. Peserta</span>
                                <span class="text-sm font-bold text-oceanic-deep">{{ $package->max_guests }} Orang</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-tropical-teal/10 text-tropical-teal flex items-center justify-center">
                                <span class="material-symbols-outlined">event_available</span>
                            </div>
                            <div>
                                <span class="text-[11px] text-outline block">Jadwal H-3+</span>
                                <span class="text-sm font-bold text-emerald-600">{{ $availableSchedules->count() }} Pilihan</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-tropical-teal/10 text-tropical-teal flex items-center justify-center">
                                <span class="material-symbols-outlined">verified</span>
                            </div>
                            <div>
                                <span class="text-[11px] text-outline block">Pemandu</span>
                                <span class="text-sm font-bold text-oceanic-deep">Berlisensi</span>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="bg-surface-white p-6 md:p-8 rounded-2xl border border-outline-variant/30 shadow-sm space-y-4">
                        <h2 class="font-display-lg text-lg md:text-xl font-bold text-oceanic-deep flex items-center gap-2">
                            <span class="material-symbols-outlined text-tropical-teal">description</span>
                            <span>Tentang Paket Ini</span>
                        </h2>
                        <div class="text-sm md:text-base text-on-surface-variant leading-relaxed whitespace-pre-line">
                            {{ $package->description }}
                        </div>
                    </div>

                    <!-- Lokasi & Rute Interaktif (Leaflet.js + OpenStreetMap) -->
                    @php
                        $mapDestinations = $package->destinations->map(function ($dest, $index) {
                            return [
                                'order'       => $index + 1,
                                'name'        => $dest->name,
                                'location'    => $dest->location,
                                'latitude'    => $dest->latitude !== null ? (float) $dest->latitude : null,
                                'longitude'   => $dest->longitude !== null ? (float) $dest->longitude : null,
                            ];
                        })->filter(fn ($d) => $d['latitude'] !== null && $d['longitude'] !== null)->values();
                    @endphp
                    <div class="bg-surface-white p-6 md:p-8 rounded-2xl border border-outline-variant/30 shadow-sm space-y-5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <h2 class="font-display-lg text-lg md:text-xl font-bold text-oceanic-deep flex items-center gap-2">
                                <span class="material-symbols-outlined text-tropical-teal">route</span>
                                <span>Lokasi &amp; Rute Perjalanan</span>
                            </h2>
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-tropical-teal bg-tropical-teal/10 px-3 py-1 rounded-full w-fit">
                                <span class="material-symbols-outlined text-sm">pin_drop</span>
                                <span>{{ $package->destinations->count() }} Titik Kunjungan</span>
                            </span>
                        </div>

                        <p class="text-xs md:text-sm text-on-surface-variant leading-relaxed">
                            Visualisasi peta interaktif di bawah ini menampilkan titik lokasi dan garis rute perjalanan berurutan antar destinasi paket wisata. Klik pada penanda (marker) untuk melihat nama destinasi.
                        </p>

                        <!-- Elemen Peta Interaktif Leaflet -->
                        <div id="map" class="w-full h-80 rounded-xl shadow-sm z-0 relative border border-outline-variant/30"></div>

                        <!-- Daftar Urutan Rute Destinasi -->
                        @if($package->destinations->isNotEmpty())
                            <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/30 space-y-3">
                                <span class="text-[11px] font-bold text-outline uppercase tracking-wider block">Urutan Rute Perjalanan</span>
                                <ol class="space-y-2.5">
                                    @foreach($package->destinations as $idx => $dest)
                                        <li class="flex items-start gap-3 text-xs md:text-sm">
                                            <span class="w-6 h-6 rounded-full {{ $loop->last ? 'bg-oceanic-deep text-white' : 'bg-tropical-teal/15 text-tropical-teal' }} font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                                {{ $idx + 1 }}
                                            </span>
                                            <div class="min-w-0 flex-1">
                                                <span class="font-bold text-oceanic-deep">{{ $dest->name }}</span>
                                                <span class="text-outline"> — {{ $dest->location }}</span>
                                                @if($loop->first && $package->destinations->count() > 1)
                                                    <span class="ml-1.5 inline-block px-2 py-0.5 text-[10px] font-semibold bg-tropical-teal/10 text-tropical-teal rounded-full">Titik Awal</span>
                                                @elseif($loop->last && $package->destinations->count() > 1)
                                                    <span class="ml-1.5 inline-block px-2 py-0.5 text-[10px] font-bold bg-oceanic-deep/10 text-oceanic-deep rounded-full">Tujuan Akhir</span>
                                                @elseif($package->destinations->count() > 2)
                                                    <span class="ml-1.5 inline-block px-2 py-0.5 text-[10px] font-semibold bg-amber-500/10 text-amber-700 rounded-full">Pemberhentian {{ $idx + 1 }}</span>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        @endif
                    </div>

                    <!-- Destinasi yang Dikunjungi -->
                    @if($package->destinations->isNotEmpty())
                        <div class="bg-surface-white p-6 md:p-8 rounded-2xl border border-outline-variant/30 shadow-sm space-y-4">
                            <h2 class="font-display-lg text-lg md:text-xl font-bold text-oceanic-deep flex items-center gap-2">
                                <span class="material-symbols-outlined text-tropical-teal">map</span>
                                <span>Destinasi yang Dikunjungi</span>
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
                                <span>Penyelenggara Tur</span>
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
                                        <span>Armada: {{ $package->agent->vehicle_count }} Unit</span>
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
                            <span class="text-xs text-outline block">Harga Resmi per Orang</span>
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

                            <!-- Section: Jadwal Perjalanan (Validasi H-3) -->
                            <div class="space-y-2">
                                <label for="package_schedule_id" class="block font-label-bold text-xs text-on-surface uppercase tracking-wider">
                                    Pilih Jadwal Keberangkatan <span class="text-error">*</span>
                                </label>

                                @if($availableSchedules->isEmpty())
                                    <!-- Warning Alert jika tidak ada jadwal yang memenuhi syarat H-3 -->
                                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl text-amber-900 space-y-2">
                                        <div class="flex items-center gap-2 font-bold text-xs">
                                            <span class="material-symbols-outlined text-amber-600 text-lg">event_busy</span>
                                            <span>Jadwal Tidak Tersedia</span>
                                        </div>
                                        <p class="text-xs leading-relaxed">
                                            Saat ini belum ada jadwal keberangkatan yang dapat dipesan. Pemesanan wajib dilakukan minimal lebih dari 3 hari sebelum keberangkatan (Batas H-3) dan kuota masih tersedia.
                                        </p>
                                    </div>
                                @else
                                    <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                                        @foreach($availableSchedules as $sch)
                                            @php
                                                $daysLeft = now()->diffInDays($sch->start_date, false);
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
                                                            <span class="text-emerald-700 font-semibold">Sisa Kuota: {{ $sch->quota }}</span>
                                                            <span class="text-amber-800 font-medium">H-{{ $daysLeft }}</span>
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

                            <!-- Section: Jumlah Peserta -->
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <label for="guest_count" class="font-label-bold text-xs text-on-surface uppercase tracking-wider">
                                        Jumlah Peserta (Pax) <span class="text-error">*</span>
                                    </label>
                                    <span class="text-[11px] text-outline">Maks. {{ $package->max_guests }} pax</span>
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
                                    <span>Estimasi Total:</span>
                                    <span id="paxCountInfo">1 Pax × Rp {{ number_format($package->price_per_pax, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-sm text-oceanic-deep">Total Bayar:</span>
                                    <span class="font-display-lg text-xl font-bold text-tropical-teal" id="totalPriceDisplay">
                                        Rp {{ number_format($package->price_per_pax, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Action Button -->
                            @guest
                                <a href="{{ route('login') }}" class="w-full bg-oceanic-deep hover:bg-primary-container text-white font-label-bold text-sm py-4 rounded-xl flex items-center justify-center gap-2 shadow-md transition-colors">
                                    <span class="material-symbols-outlined text-lg">login</span>
                                    <span>Masuk untuk Memesan</span>
                                </a>
                                <p class="text-[11px] text-outline text-center">
                                    Belum punya akun? <a href="{{ route('register') }}" class="text-tropical-teal underline font-medium">Daftar sekarang</a>
                                </p>
                            @else
                                @if(!$package->is_active)
                                    <div class="p-3 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl text-xs space-y-1 text-center">
                                        <div class="font-bold">Paket Belum Aktif</div>
                                        <p class="text-[11px]">Paket wisata ini belum dibuka untuk pemesanan oleh agen.</p>
                                    </div>
                                    <button type="button" disabled class="w-full bg-slate-200 text-slate-400 font-bold text-sm py-4 rounded-xl cursor-not-allowed flex items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-lg">pause_circle</span>
                                        <span>Paket Belum Aktif</span>
                                    </button>
                                @elseif(auth()->user()->role !== 'user')
                                    <div class="p-3 bg-blue-50 border border-blue-200 text-blue-900 rounded-xl text-xs space-y-1 text-center">
                                        <div class="font-bold">Akun {{ ucfirst(auth()->user()->role) }}</div>
                                        <p class="text-[11px]">Pemesanan tur hanya dapat dilakukan oleh akun Customer / Pengguna (role: user).</p>
                                    </div>
                                    <button type="button" disabled class="w-full bg-slate-200 text-slate-400 font-bold text-sm py-4 rounded-xl cursor-not-allowed">
                                        Akses Ditolak (Khusus User)
                                    </button>
                                @elseif($availableSchedules->isEmpty())
                                    <button type="button" disabled class="w-full bg-slate-200 text-slate-400 font-bold text-sm py-4 rounded-xl cursor-not-allowed flex items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-lg">block</span>
                                        <span>Jadwal Tidak Tersedia</span>
                                    </button>
                                @else
                                    <button type="submit" class="w-full bg-tropical-teal hover:bg-secondary text-white font-label-bold text-sm py-4 rounded-xl flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transition-all duration-200 active:scale-98">
                                        <span class="material-symbols-outlined text-lg">check_circle</span>
                                        <span>Pesan Sekarang (H-3)</span>
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

    <!-- Leaflet.js Interactive Map & Real Road Routing (Leaflet Routing Machine) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapContainer = document.getElementById('map');
            if (!mapContainer || typeof L === 'undefined') return;

            const destinations = @json($mapDestinations);

            // Koordinat default Bali Tengah jika paket belum memiliki koordinat destinasi
            const defaultCenter = [-8.409518, 115.188916];
            const map = L.map('map').setView(defaultCenter, 10);

            // Tile Layer OpenStreetMap (Gratis tanpa API Key)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            if (!destinations || destinations.length === 0) {
                return;
            }

            // 1. Tambahkan Marker & Popup untuk setiap destinasi paket wisata
            const destinationWaypoints = [];
            const bounds = L.latLngBounds();

            destinations.forEach(function (dest) {
                const latLng = L.latLng(dest.latitude, dest.longitude);
                destinationWaypoints.push(latLng);
                bounds.extend(latLng);

                const marker = L.marker(latLng).addTo(map);
                marker.bindPopup(
                    `<div class="text-xs">
                        <div class="font-bold text-slate-900">${dest.order}. ${dest.name}</div>
                        <div class="text-slate-500 mt-0.5">${dest.location}</div>
                    </div>`
                );
            });

            // Pasang tampilan awal mencakup destinasi paket sembari menunggu geolocation
            if (destinationWaypoints.length > 1) {
                map.fitBounds(bounds, { padding: [40, 40] });
            } else if (destinationWaypoints.length === 1) {
                map.setView(destinationWaypoints[0], 13);
            }

            // 2. Fungsi untuk menggambar rute jalan raya menggunakan L.Routing.control
            let routingControl = null;

            function drawRoadRoute(waypointsArray) {
                if (typeof L.Routing === 'undefined' || waypointsArray.length < 2) {
                    return;
                }

                if (routingControl) {
                    map.removeControl(routingControl);
                }

                routingControl = L.Routing.control({
                    waypoints: waypointsArray,
                    show: false,
                    addWaypoints: false,
                    routeWhileDragging: false,
                    draggableWaypoints: false,
                    fitSelectedRoutes: true,
                    lineOptions: {
                        styles: [
                            { color: '#006A6A', opacity: 0.9, weight: 5 }
                        ],
                        extendToWaypoints: true,
                        missingRouteTolerance: 0
                    },
                    // Kembalikan null agar marker bawaan plugin tidak menimpa/dobel dengan marker kita
                    createMarker: function () {
                        return null;
                    }
                }).addTo(map);
            }

            // 3. Deteksi Lokasi User (Titik Awal Rute)
            if ('geolocation' in navigator) {
                navigator.geolocation.getCurrentPosition(
                    function (pos) {
                        const userLatLng = L.latLng(pos.coords.latitude, pos.coords.longitude);

                        // Marker khusus dengan warna berbeda untuk Lokasi Anda
                        const userIcon = L.divIcon({
                            className: 'custom-user-location-marker',
                            html: `<div style="width: 18px; height: 18px; background-color: #ef4444; border: 3px solid #ffffff; border-radius: 9999px; box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.35);"></div>`,
                            iconSize: [18, 18],
                            iconAnchor: [9, 9],
                            popupAnchor: [0, -10]
                        });

                        L.marker(userLatLng, { icon: userIcon })
                            .addTo(map)
                            .bindPopup(
                                `<div class="text-xs">
                                    <div class="font-bold text-rose-600">Lokasi Anda Saat Ini</div>
                                    <div class="text-slate-500 mt-0.5">Titik awal keberangkatan</div>
                                </div>`
                            );

                        // Gabungkan lokasi user sebagai titik pertama diikuti seluruh destinasi paket
                        const allWaypoints = [userLatLng, ...destinationWaypoints];
                        drawRoadRoute(allWaypoints);
                    },
                    function () {
                        // Fallback jika user menolak izin lokasi: mulai rute dari destinasi pertama
                        drawRoadRoute(destinationWaypoints);
                    },
                    { enableHighAccuracy: true, timeout: 8000, maximumAge: 60000 }
                );
            } else {
                // Fallback jika browser tidak mendukung Geolocation
                drawRoadRoute(destinationWaypoints);
            }
        });
    </script>
</x-layouts.app>
