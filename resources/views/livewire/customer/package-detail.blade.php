<div>
    <!-- Hero Gallery -->
    <section class="relative w-full mb-12" style="height: 440px;">
        @if($package->cover_image)
            <div class="bg-cover bg-center w-full h-full relative" style="background-image: url('{{ asset('storage/' . $package->cover_image) }}')">
        @else
            <div class="bg-primary-container/20 w-full h-full relative flex items-center justify-center text-primary text-xl">
                No Image Available
        @endif
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
            
            <!-- Back Button Glass -->
            <div class="absolute top-6 left-margin-mobile md:left-margin-desktop">
                <a href="{{ route('packages.index') }}" class="glass-panel text-oceanic-deep px-4 py-2 rounded-full flex items-center space-x-2 hover:bg-white transition-colors shadow-sm inline-flex">
                    <span class="material-symbols-outlined">arrow_back</span>
                    <span class="font-label-bold text-label-bold">Back</span>
                </a>
            </div>

            <!-- Gallery Indicators (Static for now as we have 1 cover image) -->
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 glass-panel rounded-full px-4 py-2 flex items-center space-x-3">
                <div class="w-2 h-2 rounded-full bg-oceanic-deep"></div>
            </div>
        </div>
    </section>

    <!-- Main Content Area Container -->
    <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop grid grid-cols-1 lg:grid-cols-12 gap-gutter mb-16">
        
        <!-- Left Column: Details (Spans 8 cols) -->
        <div class="lg:col-span-8 space-y-12">
            
            <!-- Title & Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h1 class="font-headline-md text-headline-md text-oceanic-deep">{{ $package->name }}</h1>
                <div class="flex items-center space-x-3">
                    <span class="text-on-surface-variant">{{ $package->reviews->count() }} reviews</span>
                    <div class="bg-tertiary-container text-on-tertiary-container px-3 py-1 rounded flex items-center space-x-1">
                        <span class="material-symbols-outlined fill text-sm">star</span>
                        <span class="font-label-bold text-label-bold">{{ number_format($package->avg_rating, 0) }}</span>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <section class="space-y-4">
                <h2 class="font-headline-md text-xl font-semibold text-oceanic-deep">Description</h2>
                <p class="text-on-surface-variant font-body-lg text-body-lg leading-relaxed whitespace-pre-line">
                    {{ $package->description }}
                </p>
            </section>

            <!-- Itinerary / Destination List -->
            <section class="space-y-6">
                <h2 class="font-headline-md text-xl font-semibold text-oceanic-deep">Destination</h2>
                <div class="space-y-4">
                    @forelse($package->destinations as $index => $dest)
                        <!-- Itinerary Item -->
                        <div class="bg-surface-white rounded-xl p-4 flex items-center justify-between border border-outline-variant/30 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full bg-primary-fixed text-on-primary-fixed flex items-center justify-center font-label-bold text-label-bold">{{ $index + 1 }}</div>
                                <div>
                                    <h3 class="font-label-bold text-label-bold text-oceanic-deep">{{ $dest->name }}</h3>
                                    <p class="text-xs text-outline">{{ $dest->location }}</p>
                                </div>
                            </div>
                            @if($dest->image_path)
                                <img src="{{ asset('storage/' . $dest->image_path) }}" class="w-12 h-12 rounded object-cover">
                            @endif
                        </div>
                    @empty
                        <p class="text-on-surface-variant font-body-md">No destinations added to this package yet.</p>
                    @endforelse
                </div>
            </section>

            <!-- Reviews -->
            <section class="space-y-6">
                <h2 class="font-headline-md text-xl font-semibold text-oceanic-deep">Reviews</h2>
                @if($package->reviews->isEmpty())
                    <p class="text-on-surface-variant font-body-md">No reviews yet for this package.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($package->reviews as $review)
                            <!-- Review Card -->
                            <div class="bg-surface-white rounded-xl p-6 border border-outline-variant/30 shadow-sm">
                                <div class="flex items-center space-x-3 mb-4">
                                    <div class="w-10 h-10 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center font-bold">
                                        {{ substr($review->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-label-bold text-label-bold text-oceanic-deep">{{ $review->user->name }}</h4>
                                        <span class="font-caption text-caption text-outline">{{ $review->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                                <div class="flex space-x-1 mb-3 text-tertiary-container">
                                    @for($i=1; $i<=5; $i++)
                                        <span class="material-symbols-outlined text-sm {{ $i <= $review->rating ? 'text-yellow-500' : 'opacity-30' }}"
                                            {!! $i <= $review->rating ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>
                                            star
                                        </span>
                                    @endfor
                                </div>
                                <p class="font-body-md text-sm text-on-surface-variant line-clamp-3">
                                    "{{ $review->comment }}"
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        <!-- Right Column: Booking Widget (Spans 4 cols) -->
        <div class="lg:col-span-4 relative">
            <div class="sticky top-28">
                @livewire('customer.booking-widget', ['package' => $package])
            </div>
        </div>
    </div>
</div>
