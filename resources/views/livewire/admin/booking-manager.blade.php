<div>
    <!-- Actions Bar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="font-headline-md text-xl text-oceanic-deep">Recent Bookings</h2>
            <p class="font-body-md text-sm text-on-surface-variant">Track and manage customer bookings</p>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-auto flex items-center gap-2 bg-surface-white px-4 py-2 rounded-lg border border-outline-variant/30 shadow-[0_4px_20px_rgba(0,35,102,0.04)]">
                <span class="font-label-bold text-label-bold text-on-surface-variant whitespace-nowrap">Filter Status:</span>
                <select wire:model.live="statusFilter" class="bg-transparent border-none focus:ring-0 text-body-md font-body-md text-oceanic-deep font-medium cursor-pointer p-0 pr-6">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-primary-container text-on-primary-container p-4 rounded-xl mb-6 font-label-bold text-sm shadow-sm border border-primary-container/20">
            {{ session('success') }}
        </div>
    @endif

    <!-- Data Table Container -->
    <div class="bg-surface-white rounded-2xl border border-outline-variant/30 shadow-[0_10px_30px_rgba(0,35,102,0.05)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-surface-container-lowest border-b border-outline-variant/30 text-on-surface-variant">
                        <th class="py-4 px-6 font-label-bold text-label-bold">Booking ID</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold">Customer Info</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold">Package</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold">Travel Date</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold text-right">Amount</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold text-center">Status</th>
                        <th class="py-4 px-6 font-label-bold text-label-bold text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20 font-body-md text-sm">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-surface-container-low/50 transition-colors group">
                        <td class="py-4 px-6 font-label-bold text-oceanic-deep">{{ $booking->booking_code }}</td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-surface-container-high text-on-surface-variant flex items-center justify-center font-bold shrink-0">
                                    {{ substr($booking->user->name ?? 'C', 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-label-bold text-on-surface">{{ $booking->user->name }}</div>
                                    <div class="font-caption text-[11px] text-outline">{{ $booking->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-on-surface-variant">{{ $booking->package->name }}</td>
                        <td class="py-4 px-6 text-on-surface">
                            {{ $booking->travel_date->format('M d, Y') }}<br>
                            <span class="font-caption text-[11px] text-outline">{{ $booking->guest_count }} Pax</span>
                        </td>
                        <td class="py-4 px-6 font-label-bold text-oceanic-deep text-right">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        <td class="py-4 px-6 text-center">
                            @php
                                $colors = [
                                    'pending' => 'bg-surface-variant text-on-surface-variant border-surface-variant',
                                    'confirmed' => 'bg-secondary-fixed text-on-secondary-fixed border-secondary-fixed/50',
                                    'completed' => 'bg-primary-container text-on-primary-container border-primary-container/50',
                                    'cancelled' => 'bg-error-container text-on-error-container border-error-container/50'
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full font-caption text-[11px] font-bold uppercase tracking-wider border {{ $colors[$booking->status] }}">
                                {{ $booking->status }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <div class="inline-block relative" x-data="{ open: false }">
                                <select @change="$wire.updateStatus({{ $booking->id }}, $event.target.value)" class="text-xs font-label-bold rounded-lg border border-outline-variant/50 bg-surface-container-lowest px-3 py-1.5 focus:ring-1 focus:ring-tropical-teal text-oceanic-deep hover:bg-surface-white cursor-pointer shadow-sm">
                                    <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirm</option>
                                    <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Complete</option>
                                    <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancel</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl mb-2 text-outline/50">inbox</span>
                            <p class="font-label-bold">No bookings found.</p>
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
</div>
