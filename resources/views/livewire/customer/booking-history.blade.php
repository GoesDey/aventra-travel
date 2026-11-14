<div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-12 flex-grow min-h-[calc(100vh-300px)]">
    <div class="mb-10">
        <h1 class="font-headline-md text-[32px] text-oceanic-deep">Booking History</h1>
        <p class="font-body-md text-on-surface-variant mt-2">Track your transaction status and manage your travel bookings.</p>
    </div>

    @if (session()->has('success'))
        <div class="bg-primary-container text-on-primary-container p-4 rounded-xl mb-8 font-label-bold text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-xl">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-error-container text-on-error-container p-4 rounded-xl mb-8 font-label-bold text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-xl">error</span>
            {{ session('error') }}
        </div>
    @endif

    <div class="space-y-6">
        @forelse($bookings as $booking)
            <div class="bg-surface-white border border-outline-variant/30 rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,35,102,0.04)] hover:shadow-[0_10px_30px_rgba(0,35,102,0.08)] transition-shadow flex flex-col md:flex-row gap-6 items-start md:items-center">
                
                <div class="w-full md:w-48 h-32 rounded-xl overflow-hidden shrink-0 relative bg-surface-container">
                    @if($booking->package && $booking->package->cover_image)
                        <img src="{{ str_starts_with($booking->package->cover_image, 'http') ? $booking->package->cover_image : asset('storage/' . $booking->package->cover_image) }}" 
                             alt="{{ $booking->package->name }}" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-surface-container flex items-center justify-center text-outline text-xs">No Image</div>
                    @endif
                </div>

                <div class="flex-1 space-y-3 w-full">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h3 class="font-headline-md text-xl text-oceanic-deep">{{ $booking->package?->name ?? ($booking->is_custom ? 'Custom Itinerary' : 'Tour Package') }}</h3>
                        
                        @php
                            $statusColors = [
                                'menunggu_pembayaran' => 'bg-amber-100 text-amber-800 border border-amber-300',
                                'dibayar'             => 'bg-blue-100 text-blue-800 border border-blue-300',
                                'berjalan'            => 'bg-purple-100 text-purple-800 border border-purple-300',
                                'selesai'             => 'bg-emerald-100 text-emerald-800 border border-emerald-300',
                                'dibatalkan'          => 'bg-rose-100 text-rose-800 border border-rose-300',
                                'kedaluwarsa'         => 'bg-slate-100 text-slate-700 border border-slate-300',
                                'perlu_refund'        => 'bg-orange-100 text-orange-800 border border-orange-300',
                            ];
                            $statusLabels = [
                                'menunggu_pembayaran' => 'Awaiting Payment',
                                'dibayar'             => 'Paid',
                                'berjalan'            => 'Ongoing',
                                'selesai'             => 'Completed',
                                'dibatalkan'          => 'Cancelled',
                                'kedaluwarsa'         => 'Expired',
                                'perlu_refund'        => 'Refund Needed',
                            ];
                        @endphp
                        
                        <span class="px-3 py-1 rounded-full font-caption text-[11px] font-bold uppercase tracking-wider {{ $statusColors[$booking->status] ?? 'bg-gray-200 text-gray-800' }}">
                            {{ $statusLabels[$booking->status] ?? str_replace('_', ' ', $booking->status) }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-2">
                        <div>
                            <p class="font-caption text-[10px] uppercase text-outline tracking-wider mb-1">Booking Code</p>
                            <p class="font-label-bold text-sm text-on-surface font-mono">{{ $booking->booking_code }}</p>
                        </div>
                        <div>
                            <p class="font-caption text-[10px] uppercase text-outline tracking-wider mb-1">Tour Date</p>
                            <p class="font-label-bold text-sm text-on-surface">{{ $booking->travel_date ? $booking->travel_date->format('d M Y') : '-' }}</p>
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

                <div class="w-full md:w-auto flex flex-col gap-2.5 justify-end h-full mt-4 md:mt-0 shrink-0">
                    {{-- Action: Pay Now if status menunggu_pembayaran --}}
                    @if($booking->status === 'menunggu_pembayaran')
                        <a href="{{ route('payments.show', $booking->booking_code) }}" 
                           class="px-6 py-2.5 bg-tropical-teal hover:bg-oceanic-deep text-surface-white rounded-full font-label-bold text-sm text-center transition-colors shadow-sm flex items-center justify-center gap-1.5">
                            <span class="material-symbols-outlined text-base">payments</span>
                            Pay Now
                        </a>
                    @endif

                    @if($booking->package)
                        <a href="{{ route('packages.show', $booking->package->slug) }}" 
                           class="px-6 py-2.5 border border-outline-variant/50 text-oceanic-deep hover:bg-surface-container-low rounded-full font-label-bold text-sm text-center transition-colors">
                            View Package
                        </a>
                    @endif
                    
                    {{-- Review Action: Only for 'selesai' status --}}
                    @if($booking->status === 'selesai' && !$booking->review)
                        <button x-data @click="$dispatch('open-review', { bookingId: {{ $booking->id }} })" 
                                class="px-6 py-2.5 bg-tropical-teal hover:bg-oceanic-deep text-surface-white rounded-full font-label-bold text-sm text-center transition-colors shadow-sm">
                            Write Review
                        </button>
                    @elseif($booking->review)
                        <div class="px-6 py-2.5 bg-surface-container-low border border-tropical-teal/30 text-tropical-teal rounded-full font-label-bold text-sm text-center flex items-center gap-1 justify-center">
                            <span class="material-symbols-outlined text-sm">star</span> Reviewed
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-surface-white border border-outline-variant/30 rounded-2xl p-16 text-center text-on-surface-variant shadow-sm flex flex-col items-center">
                <span class="material-symbols-outlined text-6xl mb-4 text-outline/40">luggage</span>
                <h3 class="font-headline-md text-xl mb-2 text-oceanic-deep">No Bookings Yet</h3>
                <p class="font-body-md mb-6 max-w-sm">You haven't made any tour bookings yet. Explore your dream vacation packages now!</p>
                <a href="{{ route('packages.index') }}" class="inline-block px-8 py-3 bg-oceanic-deep text-surface-white rounded-full font-label-bold hover:bg-primary transition-colors shadow-sm">Explore Tour Packages</a>
            </div>
        @endforelse
    </div>

    <!-- Inject Review Modal -->
    @livewire('customer.review-modal')
</div>
