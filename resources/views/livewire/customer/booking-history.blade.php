<div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-12 flex-grow min-h-[calc(100vh-300px)]">
    <div class="mb-10">
        <h1 class="font-headline-md text-[32px] text-oceanic-deep">My Bookings</h1>
        <p class="font-body-md text-on-surface-variant mt-2">View and manage your travel history.</p>
    </div>

    @if (session()->has('success'))
        <div class="bg-primary-container text-on-primary-container p-4 rounded-xl mb-8 font-label-bold text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
        @forelse($bookings as $booking)
            <div class="bg-surface-white border border-outline-variant/30 rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,35,102,0.04)] hover:shadow-[0_10px_30px_rgba(0,35,102,0.08)] transition-shadow flex flex-col md:flex-row gap-6 items-start md:items-center">
                
                <div class="w-full md:w-48 h-32 rounded-xl overflow-hidden shrink-0 relative">
                    @if($booking->package->cover_image)
                        <img src="{{ asset('storage/' . $booking->package->cover_image) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-surface-container flex items-center justify-center text-outline">No Img</div>
                    @endif
                </div>

                <div class="flex-1 space-y-3 w-full">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h3 class="font-headline-md text-xl text-oceanic-deep">{{ $booking->package->name }}</h3>
                        @php
                            $statusColors = [
                                'pending' => 'bg-surface-variant text-on-surface-variant',
                                'confirmed' => 'bg-secondary-fixed text-on-secondary-fixed',
                                'completed' => 'bg-primary-container text-on-primary-container',
                                'cancelled' => 'bg-error-container text-on-error-container'
                            ];
                        @endphp
                        <span class="px-3 py-1 rounded-full font-caption text-[11px] font-bold uppercase tracking-wider {{ $statusColors[$booking->status] }}">{{ $booking->status }}</span>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-2">
                        <div>
                            <p class="font-caption text-[10px] uppercase text-outline tracking-wider mb-1">Booking Code</p>
                            <p class="font-label-bold text-sm text-on-surface">{{ $booking->booking_code }}</p>
                        </div>
                        <div>
                            <p class="font-caption text-[10px] uppercase text-outline tracking-wider mb-1">Travel Date</p>
                            <p class="font-label-bold text-sm text-on-surface">{{ $booking->travel_date->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="font-caption text-[10px] uppercase text-outline tracking-wider mb-1">Guests</p>
                            <p class="font-label-bold text-sm text-on-surface">{{ $booking->guest_count }} Pax</p>
                        </div>
                        <div>
                            <p class="font-caption text-[10px] uppercase text-outline tracking-wider mb-1">Total Price</p>
                            <p class="font-label-bold text-sm text-tropical-teal">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="w-full md:w-auto flex flex-col gap-3 justify-end h-full mt-4 md:mt-0 shrink-0">
                    <a href="{{ route('packages.show', $booking->package->slug) }}" class="px-6 py-2.5 border border-outline-variant/50 text-oceanic-deep hover:bg-surface-container-low rounded-full font-label-bold text-sm text-center transition-colors">View Package</a>
                    
                    @if($booking->status === 'completed' && !$booking->review)
                        <button x-data @click="$dispatch('open-review', { bookingId: {{ $booking->id }} })" class="px-6 py-2.5 bg-tropical-teal hover:bg-oceanic-deep text-surface-white rounded-full font-label-bold text-sm text-center transition-colors shadow-sm">
                            Leave Review
                        </button>
                    @elseif($booking->review)
                        <div class="px-6 py-2.5 bg-surface-container-low border border-tropical-teal/30 text-tropical-teal rounded-full font-label-bold text-sm text-center flex items-center gap-1 justify-center">
                            <span class="  
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-surface-white border border-outline-variant/30 rounded-2xl p-16 text-center text-on-surface-variant shadow-sm flex flex-col items-center">
                <span class="material-symbols-outlined text-6xl mb-4 text-outline/40">luggage</span>
                <h3 class="font-headline-md text-xl mb-2 text-oceanic-deep">No Bookings Yet</h3>
                <p class="font-body-md mb-6 max-w-sm">You haven't booked any travel packages. Start exploring today!</p>
                <a href="{{ route('packages.index') }}" class="inline-block px-8 py-3 bg-oceanic-deep text-surface-white rounded-full font-label-bold hover:bg-primary transition-colors shadow-sm">Explore Packages</a>
            </div>
        @endforelse
    </div>

    <!-- Inject Review Modal -->
    @livewire('customer.review-modal')
</div>
