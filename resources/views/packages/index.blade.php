<x-layouts.app title="Eksplorasi Paket Wisata - TriHitaTour">
    <!-- Hero / Header Section -->
    <div class="relative bg-oceanic-deep text-surface-white pt-28 pb-16 px-margin-mobile md:px-margin-desktop overflow-hidden">
        <!-- Decorative subtle background -->
        <div class="absolute inset-0 pointer-events-none opacity-20">
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-tropical-teal blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-primary-fixed blur-3xl"></div>
        </div>

        <div class="relative max-w-container-max mx-auto text-center space-y-4">
            <div class="inline-flex items-center gap-2 bg-tropical-teal/20 text-secondary-fixed border border-tropical-teal/30 px-4 py-1.5 rounded-full text-xs font-semibold tracking-wider uppercase">
                <span class="material-symbols-outlined text-sm">explore</span>
                <span>Destinasi & Petualangan Bali</span>
            </div>
            <h1 class="font-display-lg text-3xl md:text-5xl font-bold tracking-tight">
                Temukan Paket Wisata Impian Anda
            </h1>
            <p class="font-body-md text-on-primary/80 max-w-2xl mx-auto text-sm md:text-base">
                Pilih paket perjalanan terbaik yang dikelola langsung oleh agen travel lokal berlisensi dengan itinerary transparan dan jadwal terjamin.
            </p>

            <!-- Search & Filter Bar -->
            <form action="{{ route('packages.index') }}" method="GET" class="mt-8 max-w-4xl mx-auto bg-surface-white p-3 md:p-4 rounded-2xl md:rounded-full shadow-2xl border border-outline-variant/30 flex flex-col md:flex-row items-center gap-3 text-on-surface">
                <!-- Search Input -->
                <div class="flex-1 flex items-center gap-3 px-4 w-full">
                    <span class="material-symbols-outlined text-outline">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama paket wisata..." class="w-full text-sm bg-transparent border-none focus:outline-none placeholder:text-outline">
                </div>

                <div class="hidden md:block w-px h-8 bg-outline-variant/40"></div>

                <!-- Region Filter -->
                <div class="flex items-center gap-2 px-3 w-full md:w-auto">
                    <span class="material-symbols-outlined text-outline text-lg">location_on</span>
                    <select name="region" class="text-xs md:text-sm bg-transparent border-none focus:outline-none text-on-surface font-medium cursor-pointer w-full md:w-auto">
                        <option value="">Semua Wilayah</option>
                        <option value="timur" {{ request('region') === 'timur' ? 'selected' : '' }}>Bali Timur</option>
                        <option value="barat" {{ request('region') === 'barat' ? 'selected' : '' }}>Bali Barat</option>
                        <option value="lainnya" {{ request('region') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <div class="hidden md:block w-px h-8 bg-outline-variant/40"></div>

                <!-- Category Filter -->
                <div class="flex items-center gap-2 px-3 w-full md:w-auto">
                    <span class="material-symbols-outlined text-outline text-lg">category</span>
                    <select name="category" class="text-xs md:text-sm bg-transparent border-none focus:outline-none text-on-surface font-medium cursor-pointer w-full md:w-auto">
                        <option value="">Semua Kategori</option>
                        <option value="reguler" {{ request('category') === 'reguler' ? 'selected' : '' }}>Reguler (Open Trip)</option>
                        <option value="custom" {{ request('category') === 'custom' ? 'selected' : '' }}>Custom (Private)</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full md:w-auto bg-tropical-teal hover:bg-secondary text-surface-white px-6 py-3 rounded-xl md:rounded-full font-label-bold text-sm flex items-center justify-center gap-2 transition-colors shadow">
                    <span>Filter</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content: Package Grid -->
    <div class="flex-1 bg-surface-container-lowest py-12 px-margin-mobile md:px-margin-desktop">
        <div class="max-w-container-max mx-auto space-y-8">

            <!-- Result Count & Clear Filter -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-outline-variant/30 pb-4">
                <div class="text-sm text-on-surface-variant">
                    Menampilkan <span class="font-bold text-on-surface">{{ $packages->total() }}</span> paket wisata aktif
                </div>
                @if(request()->anyFilled(['search', 'region', 'category']))
                    <a href="{{ route('packages.index') }}" class="text-xs text-tropical-teal hover:underline flex items-center gap-1 font-semibold">
                        <span class="material-symbols-outlined text-sm">close</span>
                        <span>Reset Semua Filter</span>
                    </a>
                @endif
            </div>

            <!-- Packages Cards Grid -->
            @if($packages->isEmpty())
                <div class="text-center py-16 bg-surface-white rounded-3xl border border-outline-variant/30 p-8">
                    <div class="w-16 h-16 bg-surface-container-low rounded-full flex items-center justify-center mx-auto mb-4 text-outline">
                        <span class="material-symbols-outlined text-4xl">travel_explore</span>
                    </div>
                    <h3 class="font-headline-md text-xl font-bold text-oceanic-deep mb-2">Paket Tidak Ditemukan</h3>
                    <p class="text-sm text-on-surface-variant max-w-md mx-auto mb-6">
                        Maaf, kami tidak menemukan paket wisata yang cocok dengan kriteria pencarian Anda. Silakan coba kata kunci atau filter lain.
                    </p>
                    <a href="{{ route('packages.index') }}" class="inline-flex items-center gap-2 bg-tropical-teal text-white px-6 py-2.5 rounded-full text-sm font-semibold shadow hover:bg-secondary transition-colors">
                        Lihat Semua Paket
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($packages as $pkg)
                        @php
                            $coverUrl = 'https://placehold.co/800x600/00327D/ffffff?text=' . urlencode($pkg->name);
                            if ($pkg->cover_image) {
                                if (str_starts_with($pkg->cover_image, 'http')) {
                                    $coverUrl = $pkg->cover_image;
                                } else {
                                    $coverUrl = asset('storage/' . $pkg->cover_image);
                                }
                            }
                        @endphp

                        <div class="bg-surface-white rounded-2xl border border-outline-variant/30 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden group">
                            <!-- Cover Image Container -->
                            <div class="relative h-56 w-full overflow-hidden bg-surface-container">
                                <img src="{{ $coverUrl }}" alt="{{ $pkg->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                
                                <!-- Badges on top of image -->
                                <div class="absolute top-3 left-3 flex flex-wrap gap-1.5 z-10">
                                    @if(!$pkg->is_active)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500 text-white shadow-md">
                                            Belum Aktif
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold shadow-md {{ $pkg->category === 'custom' ? 'bg-purple-600 text-white' : 'bg-oceanic-deep text-white' }}">
                                        {{ ucfirst($pkg->category) }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-white/90 text-oceanic-deep backdrop-blur-sm shadow-md">
                                        Bali {{ ucfirst($pkg->region) }}
                                    </span>
                                </div>

                                <!-- Rating badge -->
                                <div class="absolute top-3 right-3 bg-white/95 backdrop-blur-sm px-2.5 py-1 rounded-full text-xs font-bold text-amber-900 shadow-md flex items-center gap-1 z-10">
                                    <span class="material-symbols-outlined text-amber-500 text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span>{{ number_format($pkg->avg_rating, 1) }}</span>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                                <div class="space-y-2">
                                    <h3 class="font-headline-md text-lg font-bold text-oceanic-deep group-hover:text-tropical-teal transition-colors line-clamp-2">
                                        <a href="{{ route('packages.show', $pkg->slug) }}">
                                            {{ $pkg->name }}
                                        </a>
                                    </h3>
                                    <p class="text-xs text-on-surface-variant line-clamp-2 leading-relaxed">
                                        {{ $pkg->description }}
                                    </p>
                                </div>

                                <!-- Metadata info -->
                                <div class="pt-3 border-t border-outline-variant/20 space-y-2.5 text-xs text-on-surface-variant">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-tropical-teal text-base">schedule</span>
                                            <span>{{ $pkg->duration_days }} Hari Tour</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-outline text-base">group</span>
                                            <span>Maks. {{ $pkg->max_guests }} Orang</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5 text-outline">
                                            <span class="material-symbols-outlined text-base">business_center</span>
                                            <span class="truncate max-w-[140px]">{{ $pkg->agent->agent_name ?? 'Agen Resmi' }}</span>
                                        </div>
                                        @if($pkg->is_active)
                                            <div class="text-emerald-700 font-semibold flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                <span>{{ $pkg->schedules_count }} Jadwal Aktif</span>
                                            </div>
                                        @else
                                            <div class="text-amber-700 font-semibold flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                <span>Belum Aktif</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Card Footer: Price & CTA -->
                                <div class="pt-3 border-t border-outline-variant/30 flex items-center justify-between">
                                    <div>
                                        <span class="text-[11px] text-outline block leading-none">Mulai dari</span>
                                        <span class="font-display-lg text-lg font-bold text-oceanic-deep">
                                            Rp {{ number_format($pkg->price_per_pax, 0, ',', '.') }}
                                        </span>
                                        <span class="text-[10px] text-outline">/pax</span>
                                    </div>
                                    <a href="{{ route('packages.show', $pkg->slug) }}" class="inline-flex items-center gap-1 bg-tropical-teal hover:bg-secondary text-white font-label-bold text-xs px-4 py-2.5 rounded-xl shadow transition-colors">
                                        <span>Detail</span>
                                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($packages->hasPages())
                    <div class="pt-8">
                        {{ $packages->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.app>
