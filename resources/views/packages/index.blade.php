<x-layouts.app :title="__('Explore Tour Packages') . ' - TriHitaTour'">
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
                <span>{{ __('Bali Destinations & Adventures') }}</span>
            </div>
            <h1 class="font-display-lg text-3xl md:text-5xl font-bold tracking-tight">
                {{ __('Discover Your Dream Tour Package') }}
            </h1>
            <p class="font-body-md text-on-primary/80 max-w-2xl mx-auto text-sm md:text-base">
                {{ __('Choose curated travel packages operated directly by licensed local travel agents with transparent itineraries and guaranteed departures.') }}
            </p>

            <!-- Search & Filter Bar (Category Filter Removed, Region Updated) -->
            <form action="{{ route('packages.index') }}" method="GET" class="mt-8 max-w-3xl mx-auto bg-surface-white p-3 md:p-4 rounded-2xl md:rounded-full shadow-2xl border border-outline-variant/30 flex flex-col md:flex-row items-center gap-3 text-on-surface">
                <!-- Search Input -->
                <div class="flex-1 flex items-center gap-3 px-4 w-full">
                    <span class="material-symbols-outlined text-outline">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search tour packages...') }}" class="w-full text-sm bg-transparent border-none focus:outline-none placeholder:text-outline">
                </div>

                <div class="hidden md:block w-px h-8 bg-outline-variant/40"></div>

                <!-- Region Filter -->
                <div class="flex items-center gap-2 px-3 w-full md:w-auto">
                    <span class="material-symbols-outlined text-outline text-lg">location_on</span>
                    <select name="region" class="text-xs md:text-sm bg-transparent border-none focus:outline-none text-on-surface font-medium cursor-pointer w-full md:w-auto">
                        <option value="">{{ __('All Regions') }}</option>
                        <option value="utara" {{ request('region') === 'utara' ? 'selected' : '' }}>{{ __('North Bali') }}</option>
                        <option value="selatan" {{ request('region') === 'selatan' ? 'selected' : '' }}>{{ __('South Bali') }}</option>
                        <option value="timur" {{ request('region') === 'timur' ? 'selected' : '' }}>{{ __('East Bali') }}</option>
                        <option value="barat" {{ request('region') === 'barat' ? 'selected' : '' }}>{{ __('West Bali') }}</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full md:w-auto bg-tropical-teal hover:bg-secondary text-surface-white px-6 py-3 rounded-xl md:rounded-full font-label-bold text-sm flex items-center justify-center gap-2 transition-colors shadow">
                    <span>{{ __('Filter') }}</span>
                </button>
            </form>

            <!-- Quick Custom Itinerary Hint -->
            <div class="pt-2">
                <a href="{{ route('custom-itinerary.create') }}" class="inline-flex items-center gap-1.5 text-xs text-tropical-teal hover:text-white transition-colors underline underline-offset-4">
                    <span>{{ __("Can't find the perfect trip? Request a Custom Itinerary") }}</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content: Package Grid -->
    <div class="flex-1 bg-surface-container-lowest py-12 px-margin-mobile md:px-margin-desktop">
        <div class="max-w-container-max mx-auto space-y-8">

            @if(session('success'))
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @auth
                @if(auth()->user()->isUser())
                    <div class="flex items-center justify-between gap-4 rounded-xl bg-tropical-teal/10 border border-tropical-teal/30 p-4 text-sm">
                        <span class="text-oceanic-deep">
                            @if($preference ?? null)
                                {{ __('Tours tailored and sorted based on your travel preferences.') }}
                            @else
                                {{ __('Get personalized tour recommendations tailored for your Bali holiday.') }}
                            @endif
                        </span>
                        <a href="{{ route('preferences.index') }}" class="font-semibold text-tropical-teal hover:underline whitespace-nowrap">
                            {{ ($preference ?? null) ? __('Update Preferences') : __('Set Preferences') }}
                        </a>
                    </div>
                @endif
            @endauth

            <!-- Custom Itinerary CTA Banner -->
            <div class="bg-surface-white rounded-2xl p-4 sm:p-5 border border-tropical-teal/30 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3.5 w-full md:w-auto">
                    <div class="w-10 h-10 rounded-xl bg-tropical-teal/10 text-tropical-teal flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-xl">tune</span>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-tropical-teal uppercase tracking-wider">{{ __('Tailor-Made Bali Tour') }}</span>
                        <h3 class="font-bold text-sm md:text-base text-oceanic-deep">{{ __("Can't find the perfect trip? Request a Custom Itinerary") }}</h3>
                    </div>
                </div>
                <a href="{{ route('custom-itinerary.create') }}" class="w-full md:w-auto inline-flex items-center justify-center gap-2 bg-tropical-teal hover:bg-secondary text-surface-white font-label-bold text-xs sm:text-sm px-6 py-2.5 rounded-full transition-all shadow-sm hover:shadow hover:scale-[1.02] active:scale-95 shrink-0">
                    <span>{{ __('Build Custom Itinerary') }}</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>

            <!-- Result Count & Clear Filter -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-outline-variant/30 pb-4">
                <div class="text-sm text-on-surface-variant">
                    {{ __('Showing') }} <span class="font-bold text-on-surface">{{ $packages->total() }}</span> {{ __('active tour packages') }}
                </div>
                @if(request()->anyFilled(['search', 'region']))
                    <a href="{{ route('packages.index') }}" class="text-xs text-tropical-teal hover:underline flex items-center gap-1 font-semibold">
                        <span class="material-symbols-outlined text-sm">close</span>
                        <span>{{ __('Reset Filters') }}</span>
                    </a>
                @endif
            </div>

            <!-- Packages Cards Grid -->
            @if($packages->isEmpty())
                <div class="text-center py-16 bg-surface-white rounded-3xl border border-outline-variant/30 p-8">
                    <div class="w-16 h-16 bg-surface-container-low rounded-full flex items-center justify-center mx-auto mb-4 text-outline">
                        <span class="material-symbols-outlined text-4xl">travel_explore</span>
                    </div>
                    <h3 class="font-headline-md text-xl font-bold text-oceanic-deep mb-2">{{ __('No Tour Packages Found') }}</h3>
                    <p class="text-sm text-on-surface-variant max-w-md mx-auto mb-6">
                        {{ __('Sorry, we could not find any tour packages matching your search criteria. Please try another keyword or region filter.') }}
                    </p>
                    <a href="{{ route('packages.index') }}" class="inline-flex items-center gap-2 bg-tropical-teal text-white px-6 py-2.5 rounded-full text-sm font-semibold shadow hover:bg-secondary transition-colors">
                        {{ __('View All Packages') }}
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($packages as $pkg)
                        @php
                            $coverUrl = $pkg->cover_image_url;
                            $hasReviews = ($pkg->reviews_count ?? 0) > 0 && (float) $pkg->avg_rating > 0;
                        @endphp

                        <div class="bg-surface-white rounded-2xl border border-outline-variant/30 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden group">
                            <!-- Cover Image Container -->
                            <div class="relative h-56 w-full overflow-hidden bg-surface-container">
                                @if($coverUrl)
                                    <img src="{{ $coverUrl }}" alt="{{ $pkg->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br {{ $pkg->region_gradient }} relative flex flex-col items-center justify-center text-white p-6 text-center select-none group-hover:scale-105 transition-transform duration-500">
                                        <div class="absolute -top-10 -right-10 w-36 h-36 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
                                        <div class="absolute -bottom-10 -left-10 w-32 h-32 rounded-full bg-tropical-teal/25 blur-2xl pointer-events-none"></div>
                                        <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-sm border border-white/25 flex items-center justify-center mb-2.5 shadow-sm">
                                            <span class="material-symbols-outlined text-2xl text-white">{{ $pkg->region_icon }}</span>
                                        </div>
                                        <span class="text-[11px] font-bold uppercase tracking-widest text-white/80">{{ $pkg->region_label }}</span>
                                        <span class="text-xs font-semibold text-white/95 line-clamp-1 mt-0.5 max-w-[85%]">{{ $pkg->name }}</span>
                                    </div>
                                @endif
                                
                                <!-- Badges on top of image -->
                                <div class="absolute top-3 left-3 flex flex-wrap gap-1.5 z-10">
                                    @if(($pkg->match_score ?? 0) >= 3)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-amber-400 to-orange-500 text-white shadow-md">
                                            <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                                            {{ __('Recommended') }}
                                        </span>
                                    @endif
                                    @if(!$pkg->is_active)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500 text-white shadow-md">
                                            {{ __('Inactive') }}
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold shadow-md {{ $pkg->category === 'custom' ? 'bg-purple-600 text-white' : 'bg-oceanic-deep text-white' }}">
                                        {{ $pkg->category === 'custom' ? __('Custom') : __('Regular') }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-white/90 text-oceanic-deep backdrop-blur-sm shadow-md">
                                        {{ $pkg->region_label }}
                                    </span>
                                </div>

                                <!-- Rating badge -->
                                @if($hasReviews)
                                    <div class="absolute top-3 right-3 bg-white/95 backdrop-blur-sm px-2.5 py-1 rounded-full text-xs font-bold text-amber-900 shadow-md flex items-center gap-1 z-10">
                                        <span class="material-symbols-outlined text-amber-500 text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span>{{ number_format($pkg->avg_rating, 1) }}</span>
                                    </div>
                                @else
                                    <div class="absolute top-3 right-3 bg-white/95 backdrop-blur-sm px-2.5 py-1 rounded-full text-xs font-bold text-tropical-teal shadow-md flex items-center gap-1 z-10">
                                        <span class="material-symbols-outlined text-tropical-teal text-sm">new_releases</span>
                                        <span>{{ __('New') }}</span>
                                    </div>
                                @endif
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
                                            <span>{{ $pkg->duration_days }} {{ $pkg->duration_days > 1 ? __('Days Tour') : __('Day Tour') }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-outline text-base">group</span>
                                            <span>{{ __('Max.') }} {{ $pkg->max_guests }} {{ __('Guests') }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5 text-outline">
                                            <span class="material-symbols-outlined text-base">business_center</span>
                                            <span class="truncate max-w-[140px]">{{ $pkg->agent->agent_name ?? __('Official Agent') }}</span>
                                        </div>
                                        @if($pkg->is_active)
                                            <div class="text-emerald-700 font-semibold flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                <span>{{ $pkg->schedules_count }} {{ __('Active Schedules') }}</span>
                                            </div>
                                        @else
                                            <div class="text-amber-700 font-semibold flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                <span>{{ __('Inactive') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Card Footer: Price & CTA -->
                                <div class="pt-3 border-t border-outline-variant/30 flex items-center justify-between">
                                    <div>
                                        <span class="text-[11px] text-outline block leading-none">{{ __('Starting from') }}</span>
                                        <span class="font-display-lg text-lg font-bold text-oceanic-deep">
                                            Rp {{ number_format($pkg->price_per_pax, 0, ',', '.') }}
                                        </span>
                                        <span class="text-[10px] text-outline">/{{ __('pax') }}</span>
                                    </div>
                                    <a href="{{ route('packages.show', $pkg->slug) }}" class="inline-flex items-center gap-1 bg-tropical-teal hover:bg-secondary text-white font-label-bold text-xs px-4 py-2.5 rounded-xl shadow transition-colors">
                                        <span>{{ __('Details') }}</span>
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
