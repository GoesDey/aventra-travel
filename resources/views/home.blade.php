<x-layouts.app>
    <!-- Hero Section -->
    <section class="relative w-full flex items-center justify-center pt-20 h-[720px]">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <div class="w-full h-full bg-cover bg-center absolute inset-0" data-alt="A breathtaking, panoramic view of a lush, mountainous landscape in Bali at sunrise. A solitary hiker stands atop a rugged crag in the mid-ground, gazing out over deep, verdant valleys shrouded in light morning mist. The lighting is golden hour, casting long dramatic shadows and highlighting the rich greens and earthy browns of the terrain. The aesthetic is epic, adventurous, and inspiring, conveying a sense of vast discovery and premium travel." style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCp8kyZOEtzZYznXeMmqkKs8eN81o1YRzs8mVpYYFMrDnvhaHDh5giBc8mJEmEslBsNZzXkB0tcHXSimd7T2sZ79yTrCm25o3FDzHK2ZQbVNRehBmOn1LH-oMyoYj19WuDI4vY5m6sJmgC7hlGQFCsETfLz23LtuAiDosSJgNZQ-WTMggDyqHdCDHWUtHh80h2-67Q4RuUlLVqJ-aSXeg1R61m0nN7QdUQVU-T8GwQcVB_xEfQUK7pr5JxZoS7-Zk9bIbryOlCZxtc')"></div>
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-r from-oceanic-deep/70 via-oceanic-deep/30 to-transparent"></div>
        </div>
        <!-- Hero Content -->
        <div class="relative z-10 w-full max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop mt-12 md:mt-0">
            <div class="max-w-2xl text-left">
                <h1 class="font-display-lg-mobile md:font-display-lg text-display-lg-mobile md:text-display-lg text-surface-white mb-6 text-shadow-subtle leading-tight">
                    {{ __("Travel Memories You'll Never Forget") }}
                </h1>
                <p class="font-body-lg text-body-lg text-surface-white/90 mb-10 max-w-xl text-shadow-subtle">
                    {{ __('From local escapes to far-flung adventures, find what makes you happy anytime, anywhere.') }}
                </p>
                <a href="{{ route('packages.index') }}" class="bg-oceanic-deep hover:bg-primary text-surface-white font-label-bold text-label-bold px-8 py-4 rounded-full transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 active:translate-y-0 inline-flex items-center group w-max">
                    {{ __('Discover Now') }}
                    <span class="material-symbols-outlined ml-2 group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>
    
    <!-- Value Proposition Section -->
    <section class="py-20 md:py-32 bg-surface-container-low">
        <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">
            <div class="text-center mb-16">
                <h2 class="font-headline-md text-headline-md text-oceanic-deep mb-4">{{ __('Why Choose TriHitaTour?') }}</h2>
                <div class="w-24 h-1 bg-tropical-teal mx-auto rounded-full"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
                <!-- Value Card 1 -->
                <div class="bg-surface-white p-8 rounded-xl shadow-sm border border-surface-container hover:shadow-md transition-shadow flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-full bg-primary-fixed flex items-center justify-center text-primary mb-6 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-symbols-outlined" data-weight="fill" style="font-size: 32px;">verified_user</span>
                    </div>
                    <h3 class="font-label-bold text-label-bold text-on-surface mb-3 text-lg">{{ __('Verified Local Partners') }}</h3>
                    <p class="font-body-md text-body-md text-on-surface-variant">{{ __('Explore Bali with verified local travel agencies, transparent pricing, and secure online payment.') }}</p>
                </div>
                <!-- Value Card 2 -->
                <div class="bg-surface-white p-8 rounded-xl shadow-sm border border-surface-container hover:shadow-md transition-shadow flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-full bg-primary-fixed flex items-center justify-center text-primary mb-6 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-symbols-outlined" data-weight="fill" style="font-size: 32px;">sell</span>
                    </div>
                    <h3 class="font-label-bold text-label-bold text-on-surface mb-3 text-lg">{{ __('Best Value') }}</h3>
                    <p class="font-body-md text-body-md text-on-surface-variant">{{ __('We offer tour packages at competitive prices without ever compromising on quality, vehicle comfort, or itinerary richness.') }}</p>
                </div>
                <!-- Value Card 3 -->
                <div class="bg-surface-white p-8 rounded-xl shadow-sm border border-surface-container hover:shadow-md transition-shadow flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-full bg-primary-fixed flex items-center justify-center text-primary mb-6 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-symbols-outlined" data-weight="fill" style="font-size: 32px;">support_agent</span>
                    </div>
                    <h3 class="font-label-bold text-label-bold text-on-surface mb-3 text-lg">{{ __('24/7 Support') }}</h3>
                    <p class="font-body-md text-body-md text-on-surface-variant">{{ __('Our support team and partner travel agents are ready to assist your journey at every step, ensuring complete peace of mind.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Travel Options Section: Regular Route vs Custom Itinerary -->
    <section class="py-20 bg-surface">
        <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">
            <div class="text-center mb-16">
                <h2 class="font-headline-md text-headline-md text-oceanic-deep mb-4">{{ __('Choose Your Way to Explore') }}</h2>
                <div class="w-24 h-1 bg-tropical-teal mx-auto rounded-full"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
                <!-- Option 1: Regular Route -->
                <div class="bg-surface-white p-8 rounded-xl shadow-sm border border-surface-container hover:shadow-md transition-shadow flex flex-col justify-between group">
                    <div>
                        <div class="w-16 h-16 rounded-full bg-primary-fixed flex items-center justify-center text-primary mb-6 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined" data-weight="fill" style="font-size: 32px;">map</span>
                        </div>
                        <h3 class="font-label-bold text-label-bold text-on-surface mb-3 text-xl">{{ __('Regular Route') }}</h3>
                        <p class="font-body-md text-body-md text-on-surface-variant mb-6">
                            {{ __('Practical, pre-arranged tour packages curated directly by licensed travel agents. Enjoy fixed departures, guaranteed schedules, and popular sightseeing routes with clear per-pax pricing.') }}
                        </p>
                    </div>
                    <div class="pt-4 border-t border-surface-container-highest flex items-center justify-between">
                        <span class="text-xs font-semibold text-outline">{{ __('Curated by local agents') }}</span>
                        <a href="{{ route('packages.index') }}" class="bg-oceanic-deep hover:bg-primary text-surface-white font-label-bold text-xs px-6 py-2.5 rounded-full transition-colors inline-flex items-center gap-1.5 shadow-sm">
                            <span>{{ __('Browse Packages') }}</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>

                <!-- Option 2: Custom Itinerary -->
                <div class="bg-surface-white p-8 rounded-xl shadow-sm border border-tropical-teal/30 hover:border-tropical-teal hover:shadow-md transition-all flex flex-col justify-between group">
                    <div>
                        <div class="w-16 h-16 rounded-full bg-tropical-teal/10 flex items-center justify-center text-tropical-teal mb-6 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined" data-weight="fill" style="font-size: 32px;">tune</span>
                        </div>
                        <div class="flex items-center gap-2 mb-3">
                            <h3 class="font-label-bold text-label-bold text-on-surface text-xl">{{ __('Custom Itinerary') }}</h3>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-tropical-teal/10 text-tropical-teal uppercase tracking-wider">{{ __('Tailor-Made') }}</span>
                        </div>
                        <p class="font-body-md text-body-md text-on-surface-variant mb-6">
                            {{ __('Design your own dream day across Bali. Select destinations from our catalog and customize the duration per stop. Includes a standard base of 8 hours, with transparent overtime rates (Rp 75,000/hr rounded up) if your itinerary exceeds the limit.') }}
                        </p>
                    </div>
                    <div class="pt-4 border-t border-surface-container-highest flex items-center justify-between">
                        <span class="text-xs font-semibold text-outline">{{ __('Up to 8 hours included') }}</span>
                        <a href="{{ route('custom-itinerary.create') }}" class="bg-tropical-teal hover:bg-secondary text-surface-white font-label-bold text-xs px-6 py-2.5 rounded-full transition-colors inline-flex items-center gap-1.5 shadow-sm">
                            <span>{{ __('Build Custom Itinerary') }}</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Packages Section -->
    <section class="py-20 md:py-32 bg-surface-container-low">
        <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">
            <div class="text-center mb-16">
                <h2 class="font-headline-md text-headline-md text-oceanic-deep mb-4">{{ __('Popular Packages') }}</h2>
                <div class="w-24 h-1 bg-tropical-teal mx-auto rounded-full"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter mb-12">
                @php
                    $popularPackages = \App\Models\Package::where('is_active', true)->withCount('reviews')->take(4)->get();
                @endphp
                
                @foreach($popularPackages as $package)
                @php
                    $coverUrl = $package->cover_image_url;
                    $hasReviews = ($package->reviews_count ?? 0) > 0 && (float) $package->avg_rating > 0;
                @endphp
                <a href="{{ route('packages.show', $package->slug) }}" class="bg-surface-white rounded-xl overflow-hidden shadow-sm hover:shadow-[0_10px_30px_rgba(0,35,102,0.1)] transition-all duration-300 border border-surface-container group cursor-pointer flex flex-col h-full block">
                    <div class="relative h-64 overflow-hidden bg-surface-container">
                        @if($coverUrl)
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ $coverUrl }}" alt="{{ $package->name }}">
                        @else
                            <div class="w-full h-full bg-gradient-to-br {{ $package->region_gradient }} relative flex flex-col items-center justify-center text-white p-6 text-center select-none group-hover:scale-105 transition-transform duration-500">
                                <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-sm border border-white/25 flex items-center justify-center mb-2.5 shadow-sm">
                                    <span class="material-symbols-outlined text-2xl text-white">{{ $package->region_icon }}</span>
                                </div>
                                <span class="text-[11px] font-bold uppercase tracking-widest text-white/80">{{ $package->region_label }}</span>
                            </div>
                        @endif
                        @if(!$package->is_active)
                            <div class="absolute top-4 left-4 bg-amber-500 text-white font-caption text-xs font-bold px-2.5 py-1 rounded-md shadow-md">
                                {{ __('Inactive') }}
                            </div>
                        @endif
                        @if($hasReviews)
                            <div class="absolute top-4 right-4 bg-surface-white/90 backdrop-blur-sm px-2 py-1 rounded-md flex items-center space-x-1 shadow-sm">
                                <span class="material-symbols-outlined text-yellow-400 text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="font-caption text-caption text-on-surface">{{ number_format($package->avg_rating, 1) }}</span>
                            </div>
                        @else
                            <div class="absolute top-4 right-4 bg-surface-white/90 backdrop-blur-sm px-2.5 py-1 rounded-md flex items-center space-x-1 shadow-sm">
                                <span class="font-caption text-caption font-bold text-tropical-teal">{{ __('New') }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="p-6 flex-grow flex flex-col">
                        <h3 class="font-label-bold text-label-bold text-lg text-oceanic-deep mb-1 group-hover:text-tropical-teal transition-colors">{{ $package->name }}</h3>
                        <p class="font-body-md text-body-md text-on-surface-variant text-sm mb-6 flex-grow">{{ $package->duration_days }} {{ $package->duration_days > 1 ? __('Days Trip') : __('Day Trip') }}</p>
                        <div class="pt-4 border-t border-surface-container-highest">
                            <p class="font-caption text-caption text-outline mb-1">{{ __('Starting from') }}</p>
                            <p class="font-price-hero text-price-hero text-oceanic-deep">Rp {{ number_format($package->price_per_pax, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            
            <div class="text-center">
                <a href="{{ route('packages.index') }}" class="bg-oceanic-deep hover:bg-primary text-surface-white font-label-bold text-label-bold px-8 py-3 rounded-full transition-colors inline-flex items-center space-x-2">
                    <span class="">{{ __('View All Packages') }}</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>
</x-layouts.app>
