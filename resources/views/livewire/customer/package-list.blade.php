<div class="flex-grow pt-[100px] pb-16 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto w-full grid grid-cols-1 lg:grid-cols-12 gap-gutter">
    <!-- Sidebar Filters -->
    <aside class="lg:col-span-3">
        <div class="bg-surface-white rounded-xl shadow-[0_4px_30px_rgba(0,35,102,0.05)] p-6 sticky top-[120px] overflow-hidden">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-headline-md text-[20px] leading-7 text-oceanic-deep">Filters</h2>
                <button wire:click="$reset" class="text-tropical-teal font-label-bold text-sm hover:underline">Clear All</button>
            </div>
            
            <!-- Price Range -->
            <div class="mb-6 border-t border-outline-variant/20 pt-6">
                <h3 class="font-label-bold text-on-surface mb-3">Price Range (Rp)</h3>
                <div class="flex flex-col gap-3">
                    <div>
                        <label class="block font-caption text-outline mb-1">Min Price</label>
                        <input wire:model.live.debounce.500ms="minPrice" type="number" class="w-full rounded-lg border-outline-variant/50 focus:border-tropical-teal focus:ring-tropical-teal text-sm text-on-surface p-2">
                    </div>
                    <div>
                        <label class="block font-caption text-outline mb-1">Max Price</label>
                        <input wire:model.live.debounce.500ms="maxPrice" type="number" class="w-full rounded-lg border-outline-variant/50 focus:border-tropical-teal focus:ring-tropical-teal text-sm text-on-surface p-2">
                    </div>
                </div>
            </div>

            <!-- Rating -->
            <div class="mb-6 border-t border-outline-variant/20 pt-6">
                <h3 class="font-label-bold text-on-surface mb-3">Minimum Rating</h3>
                <div class="flex items-center gap-1">
                    @for($i = 1; $i <= 5; $i++)
                        <button wire:click="$set('minRating', {{ $i }})" class="{{ $minRating >= $i ? 'text-soft-coral' : 'text-outline-variant hover:text-soft-coral' }} transition-colors">
                            <span class="material-symbols-outlined" {!! $minRating >= $i ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>star</span>
                        </button>
                    @endfor
                    @if($minRating > 0)
                        <span class="ml-2 font-body-md text-sm text-on-surface-variant">{{ $minRating }}+ stars</span>
                    @endif
                </div>
            </div>

            <!-- Duration -->
            <div class="mb-8 border-t border-outline-variant/20 pt-6">
                <h3 class="font-label-bold text-on-surface mb-3">Duration (Days)</h3>
                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input wire:model.live="durationFilter" value="1" type="radio" name="duration" class="rounded-full border-outline-variant text-tropical-teal focus:ring-tropical-teal w-5 h-5">
                        <span class="font-body-md text-sm text-on-surface">1 Day</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input wire:model.live="durationFilter" value="3" type="radio" name="duration" class="rounded-full border-outline-variant text-tropical-teal focus:ring-tropical-teal w-5 h-5">
                        <span class="font-body-md text-sm text-on-surface">3 Days</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input wire:model.live="durationFilter" value="5" type="radio" name="duration" class="rounded-full border-outline-variant text-tropical-teal focus:ring-tropical-teal w-5 h-5">
                        <span class="font-body-md text-sm text-on-surface">5 Days</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input wire:model.live="durationFilter" value="7" type="radio" name="duration" class="rounded-full border-outline-variant text-tropical-teal focus:ring-tropical-teal w-5 h-5">
                        <span class="font-body-md text-sm text-on-surface">7 Days</span>
                    </label>
                </div>
            </div>
        </div>
    </aside>

    <!-- Results Area -->
    <div class="lg:col-span-9">
        <!-- Header & Search -->
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="font-display-lg text-[32px] leading-10 text-on-primary-fixed mb-2">Discover Packages</h1>
                <p class="font-body-md text-outline">{{ $packages->total() }} packages found</p>
            </div>
            <div class="flex items-center gap-4 w-full md:w-auto">
                <div class="relative flex-grow md:w-64">
                    <input wire:model.live="search" class="w-full rounded-lg border-outline-variant/50 focus:border-tropical-teal focus:ring-tropical-teal text-on-surface py-2 pl-4 pr-12 shadow-sm bg-surface-white" type="text" placeholder="Search by name...">
                    <button class="absolute right-0 top-0 bottom-0 px-3 bg-oceanic-deep text-surface-white rounded-r-lg hover:bg-primary transition-colors flex items-center justify-center">
                        <span class="material-symbols-outlined text-sm">search</span>
                    </button>
                </div>
            </div>
        </div>

        @if($packages->isEmpty())
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-20 px-4 text-center bg-surface-white rounded-xl shadow-[0_4px_30px_rgba(0,35,102,0.03)] border border-outline-variant/10">
                <span class="material-symbols-outlined text-6xl mb-4 text-outline/50">search_off</span>
                <h2 class="font-headline-md text-on-surface mb-2">No results found</h2>
                <p class="font-body-md text-outline max-w-md mx-auto mb-6">Try adjusting your filters or modifying your search terms.</p>
                <button wire:click="$reset" class="bg-tropical-teal text-surface-white px-6 py-2 rounded-full font-label-bold hover:bg-oceanic-deep transition-colors shadow-sm">Clear Search</button>
            </div>
        @else
            <!-- Grid Results -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($packages as $package)
                    <article class="bg-surface-white rounded-2xl overflow-hidden shadow-[0_4px_20px_rgba(0,35,102,0.06)] hover:shadow-[0_10px_30px_rgba(0,35,102,0.12)] transition-shadow duration-300 group border border-outline-variant/10 flex flex-col h-full min-h-full">
                        <a href="{{ route('packages.show', $package->slug) }}" class="block">
                            <div class="relative h-64 overflow-hidden">
                                @if($package->cover_image)
                                    <div class="absolute inset-0 bg-cover bg-center group-hover:scale-105 transition-transform duration-700" style="background-image: url('{{ asset('storage/' . $package->cover_image) }}');"></div>
                                @else
                                    <div class="absolute inset-0 bg-primary-container/20 flex items-center justify-center text-primary group-hover:scale-105 transition-transform duration-700">No Image</div>
                                @endif
                                <div class="absolute top-4 right-4 bg-sand-beige text-on-tertiary-fixed font-caption px-2 py-1 rounded flex items-center gap-1 shadow-sm">
                                    <span class="material-symbols-outlined text-[14px] text-soft-coral" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="font-bold">{{ number_format($package->avg_rating, 1) }}</span>
                                </div>
                            </div>
                        </a>
                        <div class="p-5 flex flex-col flex-grow">
                            <div class="flex-grow">
                                <a href="{{ route('packages.show', $package->slug) }}">
                                    <h3 class="font-headline-md text-[20px] leading-7 text-oceanic-deep mb-1 group-hover:text-tropical-teal transition-colors">{{ $package->name }}</h3>
                                </a>
                                <p class="font-body-md text-sm text-outline-variant flex items-center gap-1 mb-4">
                                    <span class="material-symbols-outlined text-[16px]">schedule</span>
                                    {{ $package->duration_days }} Days
                                </p>
                                <p class="font-body-md text-sm text-on-surface-variant line-clamp-2 mb-4">{{ $package->description }}</p>
                            </div>
                            <div class="mt-auto pt-4 border-t border-outline-variant/20 flex items-end justify-between">
                                <div>
                                    <span class="font-caption text-outline block mb-1">Starting from</span>
                                    <div class="font-price-hero text-on-surface text-lg text-oceanic-deep">Rp {{ number_format($package->price_per_pax, 0, ',', '.') }}</div>
                                </div>
                                <a href="{{ route('packages.show', $package->slug) }}" class="bg-primary hover:bg-primary-container text-white px-4 py-2 rounded-full text-sm font-bold transition-colors">Details</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $packages->links() }}
            </div>
        @endif
    </div>
</div>
