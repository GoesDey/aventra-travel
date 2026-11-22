@php
    $isSuperAdmin = auth()->user()?->isSuperAdmin();
@endphp

<div>
    <!-- Actions Bar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h2 class="font-headline-md text-xl text-oceanic-deep">{{ __('Recent Bookings') }}</h2>
                @if($isSuperAdmin)
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-300">
                        {{ __('Read-only') }}
                    </span>
                @endif
            </div>
            <p class="font-body-md text-sm text-on-surface-variant">
                {{ $isSuperAdmin ? __('Monitor customer bookings across all partner agents (read-only).') : __('Track and manage customer bookings') }}
            </p>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-auto flex items-center gap-2 bg-surface-white px-4 py-2 rounded-lg border border-outline-variant/30 shadow-[0_4px_20px_rgba(0,35,102,0.04)]">
                <span class="font-label-bold text-label-bold text-on-surface-variant whitespace-nowrap">{{ __('Filter Status:') }}</span>
                <select wire:model.live="statusFilter" class="bg-transparent border-none focus:ring-0 text-body-md font-body-md text-oceanic-deep font-medium cursor-pointer p-0 pr-6">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="menunggu_pembayaran">{{ __('Awaiting Payment') }}</option>
                    <option value="dibayar">{{ __('Paid') }}</option>
                    <option value="berjalan">{{ __('Ongoing') }}</option>
                    <option value="selesai">{{ __('Completed') }}</option>
                    <option value="dibatalkan">{{ __('Cancelled') }}</option>
                    <option value="kedaluwarsa">{{ __('Expired') }}</option>
                    <option value="perlu_refund">{{ __('Refund Needed') }}</option>
                </select>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-primary-container text-on-primary-container p-4 rounded-xl mb-6 font-label-bold text-sm shadow-sm border border-primary-container/20">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-error-container text-on-error-container p-4 rounded-xl mb-6 font-label-bold text-sm shadow-sm border border-error-container/20">
            {{ session('error') }}
        </div>
    @endif

    <!-- Data Table Container -->
    <div class="bg-surface-white rounded-2xl border border-outline-variant/30 shadow-[0_10px_30px_rgba(0,35,102,0.05)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-surface-container-lowest border-b border-outline-variant/30 text-on-surface-variant">
                        <th class="py-4 px-6 font-label-bold text-label-bold">{{ __('Booking ID') }}</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold">{{ __('Customer Info') }}</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold">{{ __('Package') }}</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold">{{ __('Travel Date') }}</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold text-right">{{ __('Amount') }}</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold text-center">{{ __('Status') }}</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold text-center">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20 font-body-md text-sm">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-surface-container-low/50 transition-colors group">
                        <td class="py-4 px-6 font-label-bold text-oceanic-deep font-mono">{{ $booking->booking_code }}</td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-surface-container-high text-on-surface-variant flex items-center justify-center font-bold shrink-0">
                                    {{ substr($booking->user->name ?? 'C', 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-label-bold text-on-surface">{{ $booking->user->name ?? '-' }}</div>
                                    <div class="font-caption text-[11px] text-outline">{{ $booking->user->email ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-on-surface-variant">
                            <div class="font-medium text-on-surface">{{ $booking->package->name ?? '-' }}</div>
                            @if($booking->package?->agent)
                                <div class="font-caption text-[11px] text-outline">{{ $booking->package->agent->agent_name }}</div>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-on-surface">
                            {{ $booking->travel_date ? $booking->travel_date->translatedFormat('d M Y') : ($booking->schedule?->start_date ? $booking->schedule->start_date->translatedFormat('d M Y') : '-') }}<br>
                            <span class="font-caption text-[11px] text-outline">{{ $booking->guest_count }} {{ __('Pax') }}</span>
                        </td>
                        <td class="py-4 px-6 font-label-bold text-oceanic-deep text-right">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        <td class="py-4 px-6 text-center">
                            @php
                                $colors = [
                                    'menunggu_pembayaran' => 'bg-amber-100 text-amber-800 border-amber-300',
                                    'dibayar'             => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                    'berjalan'            => 'bg-blue-100 text-blue-800 border-blue-300',
                                    'selesai'             => 'bg-purple-100 text-purple-800 border-purple-300',
                                    'dibatalkan'          => 'bg-red-100 text-red-800 border-red-300',
                                    'kedaluwarsa'         => 'bg-slate-100 text-slate-800 border-slate-300',
                                    'perlu_refund'        => 'bg-rose-100 text-rose-800 border-rose-300',
                                ];
                                $statusClass = $colors[$booking->status] ?? 'bg-surface-container-high text-on-surface border-outline-variant';
                            @endphp
                            <span class="px-3 py-1 rounded-full font-caption text-[11px] font-bold uppercase tracking-wider border {{ $statusClass }}">
                                {{ \App\Models\Booking::statusLabel($booking->status) }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            @if($isSuperAdmin)
                                <button type="button"
                                    wire:click="showDetail({{ $booking->id }})"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg border border-outline-variant/50 bg-surface-container-lowest hover:bg-surface-container-low text-oceanic-deep font-label-bold text-xs transition-colors">
                                    <span class="material-symbols-outlined text-sm">visibility</span>
                                    <span>{{ __('Details') }}</span>
                                </button>
                            @else
                                <div class="inline-block relative" x-data="{ open: false }">
                                    <select @change="$wire.updateStatus({{ $booking->id }}, $event.target.value)" class="text-xs font-label-bold rounded-lg border border-outline-variant/50 bg-surface-container-lowest px-3 py-1.5 focus:ring-1 focus:ring-tropical-teal text-oceanic-deep hover:bg-surface-white cursor-pointer shadow-sm">
                                        <option value="menunggu_pembayaran" {{ $booking->status == 'menunggu_pembayaran' ? 'selected' : '' }}>{{ __('Awaiting Payment') }}</option>
                                        <option value="dibayar" {{ $booking->status == 'dibayar' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                                        <option value="berjalan" {{ $booking->status == 'berjalan' ? 'selected' : '' }}>{{ __('Ongoing') }}</option>
                                        <option value="selesai" {{ $booking->status == 'selesai' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                        <option value="dibatalkan" {{ $booking->status == 'dibatalkan' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                                        <option value="kedaluwarsa" {{ $booking->status == 'kedaluwarsa' ? 'selected' : '' }}>{{ __('Expired') }}</option>
                                        <option value="perlu_refund" {{ $booking->status == 'perlu_refund' ? 'selected' : '' }}>{{ __('Refund Needed') }}</option>
                                    </select>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl mb-2 text-outline/50">inbox</span>
                            <p class="font-label-bold">{{ __('No bookings found.') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-outline-variant/30 bg-surface-container-lowest">
            {{ $bookings->links() }}
        </div>
    </div>

    <!-- Read-Only Booking Detail Modal (Super Admin) -->
    @if($selectedBooking)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4" wire:click.self="closeDetail">
            <div class="bg-surface-white rounded-3xl border border-outline-variant/30 shadow-2xl max-w-xl w-full overflow-hidden">
                <div class="px-6 py-5 border-b border-outline-variant/30 flex items-center justify-between gap-4">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-outline">{{ __('Booking Details') }} ({{ __('Read-only') }})</span>
                        <h3 class="font-mono text-lg font-bold text-oceanic-deep">{{ $selectedBooking->booking_code }}</h3>
                    </div>
                    <button type="button" wire:click="closeDetail" class="p-1.5 rounded-lg text-outline hover:text-oceanic-deep hover:bg-surface-container-low transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="p-6 space-y-4 text-sm max-h-[75vh] overflow-y-auto">
                    <div class="flex flex-wrap items-center justify-between gap-2 p-3.5 rounded-2xl bg-surface-container-low border border-outline-variant/20">
                        <span class="text-xs font-semibold text-outline">{{ __('Booking Status') }}</span>
                        <span class="px-3 py-1 rounded-full font-caption text-xs font-bold uppercase tracking-wider bg-oceanic-deep/10 text-oceanic-deep">
                            {{ \App\Models\Booking::statusLabel($selectedBooking->status) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-3.5 rounded-xl border border-outline-variant/25">
                            <span class="text-[11px] uppercase tracking-wider text-outline font-semibold block mb-1">{{ __('Customer Info') }}</span>
                            <div class="font-bold text-on-surface">{{ $selectedBooking->user->name ?? '-' }}</div>
                            <div class="text-xs text-outline">{{ $selectedBooking->user->email ?? '-' }}</div>
                        </div>

                        <div class="p-3.5 rounded-xl border border-outline-variant/25">
                            <span class="text-[11px] uppercase tracking-wider text-outline font-semibold block mb-1">{{ __('Package') }}</span>
                            <div class="font-bold text-on-surface">{{ $selectedBooking->package->name ?? '-' }}</div>
                            <div class="text-xs text-outline">{{ $selectedBooking->package->agent->agent_name ?? '-' }}</div>
                        </div>

                        <div class="p-3.5 rounded-xl border border-outline-variant/25">
                            <span class="text-[11px] uppercase tracking-wider text-outline font-semibold block mb-1">{{ __('Tour Date') }}</span>
                            <div class="font-bold text-on-surface">
                                {{ $selectedBooking->travel_date ? $selectedBooking->travel_date->translatedFormat('d M Y') : '-' }}
                            </div>
                            <div class="text-xs text-outline">{{ $selectedBooking->guest_count }} {{ __('Pax') }}</div>
                        </div>

                        <div class="p-3.5 rounded-xl border border-outline-variant/25">
                            <span class="text-[11px] uppercase tracking-wider text-outline font-semibold block mb-1">{{ __('Payment Status:') }}</span>
                            <div class="font-bold text-on-surface">
                                {{ $selectedBooking->payment?->payment_status ? ucfirst(str_replace('_', ' ', $selectedBooking->payment->payment_status)) : __('Not initiated') }}
                            </div>
                            @if($selectedBooking->status === 'menunggu_pembayaran' && $selectedBooking->expires_at)
                                <div class="text-xs text-amber-700">
                                    {{ __('Complete Payment Before:') }} {{ $selectedBooking->expires_at->translatedFormat('d M Y, H:i') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-surface-container-lowest border border-outline-variant/25 space-y-2">
                        <div class="flex justify-between text-on-surface-variant">
                            <span>{{ __('Total Price') }}</span>
                            <span class="font-bold text-oceanic-deep">Rp {{ number_format($selectedBooking->total_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-emerald-700 text-xs">
                            <span>{{ __('Platform Commission') }} ({{ number_format((float) $selectedBooking->commission_rate, 0) }}%)</span>
                            <span class="font-bold">Rp {{ number_format($selectedBooking->commission_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-blue-700 text-xs">
                            <span>{{ __('Agent Payout (90%)') }}</span>
                            <span class="font-bold">Rp {{ number_format($selectedBooking->agent_earning, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-outline-variant/30 bg-surface-container-lowest flex justify-end">
                    <button type="button" wire:click="closeDetail"
                        class="px-5 py-2 rounded-xl bg-oceanic-deep hover:bg-primary text-white font-label-bold text-xs transition-colors">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
