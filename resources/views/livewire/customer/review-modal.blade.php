<div x-data="{ show: @entangle('show') }" 
     x-show="show" 
     @open-review.window="show = true; $wire.set('bookingId', $event.detail.bookingId)"
     style="display: none;" 
     class="fixed inset-0 z-50 flex items-center justify-center">
    
    <div @click="show = false" class="absolute inset-0 bg-oceanic-deep/40 backdrop-blur-sm transition-opacity"></div>

    <div class="bg-surface-white w-full max-w-lg rounded-2xl shadow-[0_10px_40px_rgba(0,35,102,0.1)] relative z-10 flex flex-col p-8 border border-outline-variant/30 transform transition-transform">
        <button @click="show = false" class="absolute top-6 right-6 text-outline hover:text-error transition-colors bg-surface-container-low rounded-full w-8 h-8 flex items-center justify-center">
            <span class="material-symbols-outlined text-[20px]">close</span>
        </button>

        <h2 class="font-headline-md text-2xl text-oceanic-deep mb-2">How was your trip?</h2>
        <p class="font-body-md text-on-surface-variant text-sm mb-8">Share your experience to help other travelers.</p>

        <form wire:submit.prevent="submitReview" class="space-y-6">
            
            <!-- Rating Stars -->
            <div class="flex items-center justify-center gap-4 mb-4 py-4 border border-outline-variant/20 rounded-xl bg-surface-container-low/50">
                @for($i = 1; $i <= 5; $i++)
                    <button type="button" wire:click="$set('rating', {{ $i }})" class="focus:outline-none transition-transform hover:scale-110">
                        <span class="material-symbols-outlined text-4xl {{ $rating >= $i ? 'text-yellow-500' : 'text-outline' }}" {!! $rating >= $i ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>star</span>
                    </button>
                @endfor
            </div>
            @error('rating') <span class="text-error font-caption text-xs block text-center -mt-4">{{ $message }}</span> @enderror

            <!-- Review Text -->
            <div>
                <label class="block font-label-bold text-sm mb-2 text-oceanic-deep">Your Review</label>
                <textarea wire:model="comment" rows="4" class="w-full bg-surface-container-low border-none rounded-xl p-4 focus:ring-1 focus:ring-tropical-teal text-on-surface resize-none font-body-md text-sm placeholder:text-outline/70" placeholder="What did you love about this package? The accommodations? The tour guides?" required></textarea>
                @error('comment') <span class="text-error font-caption text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Submit -->
            <button type="submit" class="w-full bg-tropical-teal hover:bg-oceanic-deep text-surface-white font-label-bold text-label-bold py-3.5 rounded-full transition-colors shadow-sm mt-4">
                Submit Review
            </button>
        </form>
    </div>
</div>
