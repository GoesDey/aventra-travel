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
                    Travel Memories You'll Never Forget
                </h1>
                <p class="font-body-lg text-body-lg text-surface-white/90 mb-10 max-w-xl text-shadow-subtle">
                    From local escapes to far-flung adventures, find what makes you happy anytime, anywhere.
                </p>
                <a href="{{ route('packages.index') }}" class="bg-oceanic-deep hover:bg-primary text-surface-white font-label-bold text-label-bold px-8 py-4 rounded-full transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 active:translate-y-0 inline-flex items-center group w-max">
                    Discover Now
                    <span class="material-symbols-outlined ml-2 group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>
    
    <!-- Value Proposition Section -->
    <section class="py-20 md:py-32 bg-surface-container-low">
        <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">
            <div class="text-center mb-16">
                <h2 class="font-headline-md text-headline-md text-oceanic-deep mb-4">Why Choose Aventra?</h2>
                <div class="w-24 h-1 bg-tropical-teal mx-auto rounded-full"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
                <!-- Value Card 1 -->
                <div class="bg-surface-white p-8 rounded-xl shadow-sm border border-surface-container hover:shadow-md transition-shadow flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-full bg-primary-fixed flex items-center justify-center text-primary mb-6 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-symbols-outlined" data-weight="fill" style="font-size: 32px;">verified_user</span>
                    </div>
                    <h3 class="font-label-bold text-label-bold text-on-surface mb-3 text-lg">Terpercaya</h3>
                    <p class="font-body-md text-body-md text-on-surface-variant">Ribuan pelanggan telah mempercayakan perjalanan mereka kepada kami karena standar keamanan dan transparansi kami.</p>
                </div>
                <!-- Value Card 2 -->
                <div class="bg-surface-white p-8 rounded-xl shadow-sm border border-surface-container hover:shadow-md transition-shadow flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-full bg-primary-fixed flex items-center justify-center text-primary mb-6 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-symbols-outlined" data-weight="fill" style="font-size: 32px;">sell</span>
                    </div>
                    <h3 class="font-label-bold text-label-bold text-on-surface mb-3 text-lg">Harga Terbaik</h3>
                    <p class="font-body-md text-body-md text-on-surface-variant">Kami menawarkan paket wisata dengan harga yang kompetitif tanpa mengorbankan kualitas fasilitas dan kenyamanan Anda.</p>
                </div>
                <!-- Value Card 3 -->
                <div class="bg-surface-white p-8 rounded-xl shadow-sm border border-surface-container hover:shadow-md transition-shadow flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-full bg-primary-fixed flex items-center justify-center text-primary mb-6 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-symbols-outlined" data-weight="fill" style="font-size: 32px;">support_agent</span>
                    </div>
                    <h3 class="font-label-bold text-label-bold text-on-surface mb-3 text-lg">Layanan 24/7</h3>
                    <p class="font-body-md text-body-md text-on-surface-variant">Tim kami siap membantu kebutuhan perjalanan Anda kapan saja dan di mana saja, memberikan rasa tenang selama perjalanan.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Popular Packages Section -->
    <section class="py-20 md:py-32 bg-surface">
        <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">
            <div class="text-center mb-16">
                <h2 class="font-headline-md text-headline-md text-oceanic-deep mb-4">Popular Package</h2>
                <div class="w-24 h-1 bg-tropical-teal mx-auto rounded-full"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter mb-12">
                @php
                    $popularPackages = \App\Models\Package::withAvg('reviews', 'rating')->take(4)->get();
                @endphp
                
                @foreach($popularPackages as $package)
                <a href="{{ route('packages.show', $package->slug) }}" class="bg-surface-white rounded-xl overflow-hidden shadow-sm hover:shadow-[0_10px_30px_rgba(0,35,102,0.1)] transition-all duration-300 border border-surface-container group cursor-pointer flex flex-col h-full block">
                    <div class="relative h-64 overflow-hidden">
                        @if($package->cover_image)
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ asset('storage/' . $package->cover_image) }}">
                        @else
                            <div class="w-full h-full bg-primary-container flex items-center justify-center text-white">No Image</div>
                        @endif
                        <div class="absolute top-4 right-4 bg-surface-white/90 backdrop-blur-sm px-2 py-1 rounded-md flex items-center space-x-1 shadow-sm">
                            <span class="material-symbols-outlined text-yellow-400 text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                            <span class="font-caption text-caption text-on-surface">{{ number_format($package->avg_rating, 0) }}</span>
                        </div>
                    </div>
                    <div class="p-6 flex-grow flex flex-col">
                        <h3 class="font-label-bold text-label-bold text-lg text-oceanic-deep mb-1 group-hover:text-tropical-teal transition-colors">{{ $package->name }}</h3>
                        <p class="font-body-md text-body-md text-on-surface-variant text-sm mb-6 flex-grow">{{ $package->duration_days }} Days Trip</p>
                        <div class="pt-4 border-t border-surface-container-highest">
                            <p class="font-caption text-caption text-outline mb-1">Mulai dari</p>
                            <p class="font-price-hero text-price-hero text-oceanic-deep">Rp {{ number_format($package->price_per_pax, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            
            <div class="text-center">
                <a href="{{ route('packages.index') }}" class="bg-oceanic-deep hover:bg-primary text-surface-white font-label-bold text-label-bold px-8 py-3 rounded-full transition-colors inline-flex items-center space-x-2">
                    <span class="">More Package</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>
</x-layouts.app>
