<x-layouts.app :title="__('Booking Details') . ' - ' . $booking->booking_code">
    @php
        $statusColors = [
            'menunggu_pembayaran' => 'bg-amber-100 text-amber-800 border border-amber-300',
            'dibayar'             => 'bg-emerald-100 text-emerald-800 border border-emerald-300',
            'berjalan'            => 'bg-blue-100 text-blue-800 border border-blue-300',
            'selesai'             => 'bg-purple-100 text-purple-800 border border-purple-300',
            'dibatalkan'          => 'bg-rose-100 text-rose-800 border border-rose-300',
            'kedaluwarsa'         => 'bg-slate-100 text-slate-700 border border-slate-300',
            'perlu_refund'        => 'bg-orange-100 text-orange-800 border border-orange-300',
        ];
        $paymentStatusMap = [
            'pending'    => __('Pending'),
            'settlement' => __('Settled'),
            'capture'    => __('Captured'),
            'paid'       => __('Paid'),
            'expire'     => __('Expired'),
            'expired'    => __('Expired'),
            'cancel'     => __('Cancelled'),
            'deny'       => __('Denied'),
            'failure'    => __('Failed'),
            'failed'     => __('Failed'),
        ];
        $paymentStatusRaw = $booking->payment?->payment_status;
        $paymentStatusLabel = $paymentStatusRaw
            ? ($paymentStatusMap[strtolower($paymentStatusRaw)] ?? ucfirst(str_replace('_', ' ', $paymentStatusRaw)))
            : __('Not initiated');
    @endphp

    <div class="bg-surface-container-lowest min-h-screen pt-24 pb-16 px-margin-mobile md:px-margin-desktop">
        <div class="max-w-3xl mx-auto space-y-6">

            <!-- Breadcrumb & Back Link -->
            <div class="flex flex-wrap items-center justify-between gap-3">
                <nav class="flex items-center gap-2 text-xs md:text-sm text-outline">
                    <a href="{{ route('home') }}" class="hover:text-tropical-teal transition-colors">{{ __('Home') }}</a>
                    <span>/</span>
                    <a href="{{ route('history') }}" class="hover:text-tropical-teal transition-colors">{{ __('Booking History') }}</a>
                    <span>/</span>
                    <span class="text-on-surface font-semibold font-mono">{{ $booking->booking_code }}</span>
                </nav>

                <a href="{{ route('history') }}"
                   class="inline-flex items-center gap-1.5 text-xs font-label-bold text-oceanic-deep hover:text-tropical-teal transition-colors">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    <span>{{ __('Back to Booking History') }}</span>
                </a>
            </div>

            <!-- Flash Messages -->
            @if (session()->has('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 rounded-2xl font-label-bold text-sm flex items-center gap-2.5 shadow-sm">
                    <span class="material-symbols-outlined text-emerald-600 text-xl">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-900 p-4 rounded-2xl font-label-bold text-sm flex items-center gap-2.5 shadow-sm">
                    <span class="material-symbols-outlined text-rose-600 text-xl">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Payment Deadline Banner (only when awaiting payment) -->
            @if($booking->status === 'menunggu_pembayaran')
                <div class="bg-amber-50 border border-amber-300 text-amber-950 p-5 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-2xl">timer</span>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-amber-800 block">{{ __('Complete Payment Before:') }}</span>
                            <span class="font-bold text-sm md:text-base text-amber-950">
                                {{ $booking->expires_at ? $booking->expires_at->translatedFormat('d M Y, H:i') : '-' }} WITA
                            </span>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-200/70 text-amber-900 self-start sm:self-auto">
                        <span class="w-2 h-2 rounded-full bg-amber-600 animate-pulse"></span>
                        <span>{{ \App\Models\Booking::statusLabel($booking->status) }}</span>
                    </span>
                </div>
            @endif

            <!-- Main Read-Only Booking Summary Card -->
            <div class="bg-surface-white rounded-3xl border border-outline-variant/30 shadow-xl overflow-hidden">
                <!-- Header: Booking Code & Status -->
                <div class="p-6 md:p-8 border-b border-outline-variant/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-xs text-outline uppercase tracking-wider font-semibold">{{ __('Official Booking Code') }}</span>
                        <h1 class="font-mono text-xl md:text-2xl font-bold text-oceanic-deep mt-0.5">{{ $booking->booking_code }}</h1>
                        <p class="text-xs text-outline mt-1">
                            {{ __('Booked on:') }} {{ $booking->created_at->translatedFormat('d M Y, H:i') }} WITA
                        </p>
                    </div>
                    <div class="flex flex-col sm:items-end gap-1.5">
                        <span class="px-3.5 py-1.5 rounded-full font-caption text-xs font-bold uppercase tracking-wider {{ $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ \App\Models\Booking::statusLabel($booking->status) }}
                        </span>
                        <span class="text-xs text-outline">
                            {{ __('Payment Status:') }} <strong class="text-on-surface">{{ $paymentStatusLabel }}</strong>
                        </span>
                    </div>
                </div>

                <div class="p-6 md:p-8 space-y-6">
                    <!-- Package Info -->
                    <div class="flex flex-col sm:flex-row items-start gap-4 p-4 rounded-2xl bg-surface-container-low border border-outline-variant/20">
                        <div class="w-full sm:w-28 h-40 sm:h-28 rounded-xl overflow-hidden shrink-0 border border-outline-variant/30 bg-surface-container">
                            @if($booking->package && $booking->package->cover_image_url)
                                <img src="{{ $booking->package->cover_image_url }}"
                                     alt="{{ $booking->package->name }}"
                                     class="w-full h-full object-cover">
                            @elseif($booking->package)
                                <div class="w-full h-full bg-gradient-to-br {{ $booking->package->region_gradient }} text-white flex flex-col items-center justify-center p-2 text-center">
                                    <span class="material-symbols-outlined text-2xl">{{ $booking->package->region_icon }}</span>
                                    <span class="text-[9px] font-bold uppercase tracking-wider mt-1 opacity-85">{{ $booking->package->region_label }}</span>
                                </div>
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-oceanic-deep via-tropical-teal to-secondary text-white flex flex-col items-center justify-center p-2 text-center">
                                    <span class="material-symbols-outlined text-2xl">travel_explore</span>
                                    <span class="text-[9px] font-bold uppercase tracking-wider mt-1 opacity-85">Bali Tour</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0 space-y-1.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-oceanic-deep text-white">
                                    {{ $booking->is_custom || ($booking->package?->category === 'custom') ? __('Custom') : __('Regular') }}
                                </span>
                                @if($booking->package)
                                    <span class="text-[11px] font-semibold text-outline">{{ $booking->package->region_label }}</span>
                                @endif
                            </div>

                            <h2 class="font-headline-md text-lg font-bold text-oceanic-deep">
                                {{ $booking->package?->name ?? ($booking->is_custom ? __('Custom Itinerary') : __('Tour Package')) }}
                            </h2>

                            @if($booking->package?->agent)
                                <p class="text-xs text-outline">
                                    {{ __('Operated by:') }} <span class="font-semibold text-on-surface">{{ $booking->package->agent->agent_name }}</span>
                                </p>
                            @endif

                            @if($booking->package)
                                <div class="pt-1">
                                    <a href="{{ route('packages.show', $booking->package->slug) }}"
                                       class="inline-flex items-center gap-1 text-xs font-semibold text-tropical-teal hover:text-oceanic-deep transition-colors">
                                        <span>{{ __('View Package Page') }}</span>
                                        <span class="material-symbols-outlined text-sm">open_in_new</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Trip Details Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="p-4 rounded-2xl bg-surface-container-lowest border border-outline-variant/25">
                            <span class="text-[11px] uppercase tracking-wider text-outline font-semibold block mb-1">{{ __('Tour Date') }}</span>
                            <div class="font-label-bold text-sm text-oceanic-deep flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-tropical-teal">calendar_month</span>
                                <span>
                                    @if($booking->schedule)
                                        {{ $booking->schedule->start_date->translatedFormat('d M Y') }} - {{ $booking->schedule->end_date->translatedFormat('d M Y') }}
                                    @elseif($booking->travel_date)
                                        {{ $booking->travel_date->translatedFormat('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-surface-container-lowest border border-outline-variant/25">
                            <span class="text-[11px] uppercase tracking-wider text-outline font-semibold block mb-1">{{ __('Guests') }}</span>
                            <div class="font-label-bold text-sm text-oceanic-deep flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-tropical-teal">group</span>
                                <span>{{ $booking->guest_count }} {{ __('Pax') }}</span>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-surface-container-lowest border border-outline-variant/25">
                            <span class="text-[11px] uppercase tracking-wider text-outline font-semibold block mb-1">{{ __('Duration') }}</span>
                            <div class="font-label-bold text-sm text-oceanic-deep flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-tropical-teal">schedule</span>
                                <span>
                                    @if($booking->is_custom && $booking->total_hours)
                                        {{ number_format((float) $booking->total_hours, 1) }} {{ __('hrs') }}
                                    @elseif($booking->package)
                                        {{ $booking->package->duration_days }} {{ $booking->package->duration_days > 1 ? __('Days') : __('Day') }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Custom Itinerary Details (if applicable) -->
                    @if($booking->is_custom)
                        <div class="p-4 rounded-2xl bg-surface-container-low border border-outline-variant/25 space-y-2 text-xs">
                            <div class="font-bold text-oceanic-deep text-sm">{{ __('Custom Itinerary Details') }}</div>
                            @if($booking->start_point)
                                <div class="text-on-surface-variant">
                                    <span class="font-semibold text-on-surface">{{ __('Pick-up / Starting Point:') }}</span> {{ $booking->start_point }}
                                </div>
                            @endif
                            @if(!empty($booking->custom_destinations))
                                <div class="text-on-surface-variant">
                                    <span class="font-semibold text-on-surface">{{ __('Selected Destinations:') }}</span>
                                    {{ collect($booking->custom_destinations)->map(fn($d) => is_array($d) ? ($d['name'] ?? '-') : $d)->implode(', ') }}
                                </div>
                            @endif
                            @if($booking->special_requests)
                                <div class="text-on-surface-variant">
                                    <span class="font-semibold text-on-surface">{{ __('Special Requests:') }}</span> {{ $booking->special_requests }}
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Price Breakdown -->
                    <div class="space-y-2.5 pt-4 border-t border-outline-variant/30 text-sm">
                        <h3 class="font-label-bold text-xs uppercase tracking-wider text-outline">{{ __('Price Breakdown') }}</h3>

                        @if($booking->package)
                            <div class="flex justify-between items-center text-on-surface-variant">
                                <span>{{ __('Price per Person') }}</span>
                                <span>Rp {{ number_format($booking->package->price_per_pax, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between items-center text-on-surface-variant">
                            <span>{{ __('Number of Guests') }}</span>
                            <span>{{ $booking->guest_count }} {{ __('Pax') }}</span>
                        </div>

                        @if($booking->is_custom && $booking->overtime_charge > 0)
                            <div class="flex justify-between items-center text-amber-700">
                                <span>{{ __('Overtime Charge') }} ({{ $booking->overtime_hours }} {{ __('hrs') }})</span>
                                <span>Rp {{ number_format($booking->overtime_charge, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between items-center text-base font-bold text-oceanic-deep pt-3 border-t border-dashed border-outline-variant/40">
                            <span>{{ __('Total Payment') }}</span>
                            <span class="font-display-lg text-2xl text-tropical-teal">
                                Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Actions (Pay Now & Check Payment Status if applicable) -->
                    @if(in_array($booking->status, ['menunggu_pembayaran', 'kedaluwarsa']))
                        <div class="pt-4 border-t border-outline-variant/30 flex flex-col sm:flex-row gap-3">
                            @if($booking->status === 'menunggu_pembayaran')
                                <a href="{{ route('payments.show', $booking->booking_code) }}"
                                   class="flex-1 bg-tropical-teal hover:bg-oceanic-deep text-white font-label-bold text-sm py-3.5 px-6 rounded-2xl flex items-center justify-center gap-2 shadow-md transition-colors">
                                    <span class="material-symbols-outlined text-lg">payments</span>
                                    <span>{{ __('Pay Now') }}</span>
                                </a>
                            @endif

                            <form method="POST" action="{{ route('payments.sync', $booking->booking_code) }}" class="flex-1">
                                @csrf
                                <button type="submit"
                                    class="w-full bg-surface-container-low hover:bg-surface-container border border-outline-variant/50 text-oceanic-deep font-label-bold text-sm py-3.5 px-6 rounded-2xl flex items-center justify-center gap-2 transition-colors">
                                    <span class="material-symbols-outlined text-lg">sync</span>
                                    <span>{{ __('Check payment status') }}</span>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

