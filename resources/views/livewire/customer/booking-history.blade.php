<div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-12 flex-grow min-h-[calc(100vh-300px)]">
    <div class="mb-10">
        <h1 class="font-headline-md text-[32px] text-oceanic-deep">{{ __('Booking History') }}</h1>
        <p class="font-body-md text-on-surface-variant mt-2">{{ __('Track your transaction status and manage your travel bookings.') }}</p>
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
                    @if($booking->package && $booking->package->cover_image_url)
                        <img src="{{ $booking->package->cover_image_url }}" 
                             alt="{{ $booking->package->name }}" 
                             class="w-full h-full object-cover">
                    @elseif($booking->package)
                        <div class="w-full h-full bg-gradient-to-br {{ $booking->package->region_gradient }} text-white flex flex-col items-center justify-center p-3 text-center">
                            <span class="material-symbols-outlined text-2xl">{{ $booking->package->region_icon }}</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider mt-1 opacity-85">{{ $booking->package->region_label }}</span>
                        </div>
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-oceanic-deep via-tropical-teal to-secondary text-white flex flex-col items-center justify-center p-3 text-center">
                            <span class="material-symbols-outlined text-2xl">travel_explore</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider mt-1 opacity-85">Bali Tour</span>
                        </div>
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
                            {{ \App\Models\Booking::statusLabel($booking->status) }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-2">
                        <div>
                            <p class="font-caption text-[10px] uppercase text-outline tracking-wider mb-1">{{ __('Booking Code') }}</p>
                            <p class="font-label-bold text-sm text-on-surface font-mono">{{ $booking->booking_code }}</p>
                        </div>
                        <div>
                            <p class="font-caption text-[10px] uppercase text-outline tracking-wider mb-1">{{ __('Tour Date') }}</p>
                            <p class="font-label-bold text-sm text-on-surface">{{ $booking->travel_date ? $booking->travel_date->translatedFormat('d M Y') : '-' }}</p>
                        </div>
                        <div>
                            <p class="font-caption text-[10px] uppercase text-outline tracking-wider mb-1">{{ __('Guests') }}</p>
                            <p class="font-label-bold text-sm text-on-surface">{{ $booking->guest_count }} {{ __('Pax') }}</p>
                        </div>
                        <div>
                            <p class="font-caption text-[10px] uppercase text-outline tracking-wider mb-1">{{ __('Total Price') }}</p>
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
                            {{ __('Pay Now') }}
                        </a>
                    @endif

                    {{-- Fallback Action: Check payment status via Midtrans Status API --}}
                    @if(in_array($booking->status, ['menunggu_pembayaran', 'kedaluwarsa']))
                        <form method="POST" action="{{ route('payments.sync', $booking->booking_code) }}">
                            @csrf
                            <button type="submit"
                                class="w-full px-6 py-2.5 bg-surface-container-low hover:bg-surface-container border border-outline-variant/50 text-oceanic-deep rounded-full font-label-bold text-sm text-center transition-colors flex items-center justify-center gap-1.5">
                                <span class="material-symbols-outlined text-base">sync</span>
                                {{ __('Check payment status') }}
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('history.show', $booking->booking_code) }}" 
                       class="px-6 py-2.5 border border-outline-variant/50 text-oceanic-deep hover:bg-surface-container-low rounded-full font-label-bold text-sm text-center transition-colors flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-base">receipt_long</span>
                        {{ __('View Details') }}
                    </a>
                    
                    {{-- Review Action: Only for 'selesai' status --}}
                    @if($booking->status === 'selesai' && !$booking->review)
                        <button x-data @click="$dispatch('open-review', { bookingId: {{ $booking->id }} })" 
                                class="px-6 py-2.5 bg-tropical-teal hover:bg-oceanic-deep text-surface-white rounded-full font-label-bold text-sm text-center transition-colors shadow-sm">
                            {{ __('Write Review') }}
                        </button>
                    @elseif($booking->review)
                        <div class="px-6 py-2.5 bg-surface-container-low border border-tropical-teal/30 text-tropical-teal rounded-full font-label-bold text-sm text-center flex items-center gap-1 justify-center">
                            <span class="material-symbols-outlined text-sm">star</span> {{ __('Reviewed') }}
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-surface-white border border-outline-variant/30 rounded-2xl p-16 text-center text-on-surface-variant shadow-sm flex flex-col items-center">
                <span class="material-symbols-outlined text-6xl mb-4 text-outline/40">luggage</span>
                <h3 class="font-headline-md text-xl mb-2 text-oceanic-deep">{{ __('No Bookings Yet') }}</h3>
                <p class="font-body-md mb-6 max-w-sm">{{ __("You haven't made any tour bookings yet. Explore your dream vacation packages now!") }}</p>
                <a href="{{ route('packages.index') }}" class="inline-block px-8 py-3 bg-oceanic-deep text-surface-white rounded-full font-label-bold hover:bg-primary transition-colors shadow-sm">{{ __('Explore Tour Packages') }}</a>
            </div>
        @endforelse
    </div>

    <!-- Inject Review Modal -->
    @livewire('customer.review-modal')
</div>
