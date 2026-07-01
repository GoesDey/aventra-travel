<div class="bg-surface-white rounded-2xl p-6 border border-outline-variant/30 shadow-[0_10px_30px_rgba(0,35,102,0.05)]">
    <div class="flex justify-between items-start mb-2">
        <span class="font-caption text-caption text-outline">Start from</span>
        @if($package->max_guests - $guestCount < 5 && $package->max_guests - $guestCount > 0)
            <span class="bg-secondary-fixed text-on-secondary-fixed px-2 py-1 rounded font-caption text-caption">{{ $package->max_guests - $guestCount }} Slots left</span>
        @endif
    </div>
    
    <div class="mb-6 border-b border-outline-variant/30 pb-6">
        <span class="font-price-hero text-price-hero text-oceanic-deep">Rp {{ number_format($package->price_per_pax, 0, ',', '.') }}</span>
        <span class="font-caption text-caption text-outline">/ per person</span>
    </div>

    @if (session()->has('success'))
        <div class="bg-primary-container text-on-primary-container p-4 rounded-lg mb-6 font-label-bold text-sm text-center">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="book" class="space-y-4">
        <div class="space-y-3 mb-6">
            <!-- Date Selection -->
            <div class="bg-surface-container-low rounded-xl p-3 flex items-center space-x-3 border border-outline-variant/20 hover:border-tropical-teal/50 transition-colors relative">
                <span class="material-symbols-outlined text-outline">calendar_month</span>
                <div class="flex-grow">
                    <div class="font-caption text-[10px] text-outline uppercase tracking-wider">Travel Date</div>
                    <input wire:model="travelDate" type="date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full bg-transparent border-none p-0 focus:ring-0 font-label-bold text-sm text-oceanic-deep" required>
                </div>
            </div>
            @error('travelDate') <span class="text-error font-caption text-xs px-2">{{ $message }}</span> @enderror

            <!-- Guests Selection -->
            <div class="bg-surface-container-low rounded-xl p-3 flex items-center space-x-3 border border-outline-variant/20 relative">
                <span class="material-symbols-outlined text-outline">group</span>
                <div class="flex-grow">
                    <div class="font-caption text-[10px] text-outline uppercase tracking-wider flex justify-between">
                        <span>Guests</span>
                        <span>Max {{ $package->max_guests }}</span>
                    </div>
                    <div class="flex items-center mt-1">
                        <button type="button" wire:click="$set('guestCount', {{ max(1, $guestCount - 1) }})" class="w-6 h-6 rounded bg-surface-white shadow-sm flex items-center justify-center hover:text-tropical-teal transition">
                            <span class="material-symbols-outlined text-[16px]">remove</span>
                        </button>
                        <input wire:model="guestCount" type="number" min="1" max="{{ $package->max_guests }}" class="flex-1 bg-transparent border-none text-center font-label-bold text-sm p-0 focus:ring-0 text-oceanic-deep pointer-events-none" readonly>
                        <button type="button" wire:click="$set('guestCount', {{ min($package->max_guests, $guestCount + 1) }})" class="w-6 h-6 rounded bg-surface-white shadow-sm flex items-center justify-center hover:text-tropical-teal transition">
                            <span class="material-symbols-outlined text-[16px]">add</span>
                        </button>
                    </div>
                </div>
            </div>
            @error('guestCount') <span class="text-error font-caption text-xs px-2">{{ $message }}</span> @enderror
            
            <!-- Summary -->
            <div class="pt-4 mt-4 border-t border-outline-variant/30">
                <div class="flex justify-between items-center font-label-bold text-sm text-oceanic-deep">
                    <span>Total ({{ $guestCount }} Guests)</span>
                    <span class="text-lg">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-oceanic-deep text-surface-white py-3 rounded-full font-label-bold text-label-bold hover:bg-primary transition-colors shadow-sm">
            Book Now
        </button>
        
        <p class="text-center font-caption text-caption text-outline mt-4">
            No booking fees &amp; free cancellation
        </p>
    </form>
</div>
