<x-layouts.agent :title="'Dashboard Keuangan Agen - ' . $agent->agent_name">
    <div class="space-y-8">
        <!-- Agent Welcome & Payout Information Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-oceanic-deep via-oceanic-deep/95 to-tropical-teal p-8 text-white shadow-xl">
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-white/15 backdrop-blur-md text-tropical-teal border border-white/20">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Portal Keuangan Mitra Terverifikasi</span>
                    </div>
                    <h2 class="font-display-lg text-2xl md:text-3xl font-bold tracking-tight">
                        {{ $agent->agent_name }}
                    </h2>
                    <p class="text-sm text-slate-200 max-w-2xl leading-relaxed">
                        Pantau hak pendapatan bersih (90%), kelola status perjalanan wisatawan, dan tinjau riwayat pemesanan paket wisata Anda secara transparan.
                    </p>
                </div>

                <!-- Bank Disbursement Account Info Card -->
                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 sm:p-5 text-left shrink-0 max-w-sm">
                    <div class="flex items-center gap-2 text-xs font-bold text-tropical-teal uppercase tracking-wider mb-2">
                        <span class="material-symbols-outlined text-sm">account_balance</span>
                        <span>Rekening Pencairan Dana</span>
                    </div>
                    <div class="text-sm font-bold text-white">
                        {{ $agent->bank_name ?? 'Bank Belum Diatur' }}
                    </div>
                    <div class="font-mono text-base font-extrabold text-tropical-teal tracking-wider my-0.5">
                        {{ $agent->bank_account_number ?? '-' }}
                    </div>
                    <div class="text-xs text-slate-300">
                        a/n {{ $agent->bank_account_name ?? $agent->agent_name }}
                    </div>
                </div>
            </div>

            <!-- Background Decorative Element -->
            <div class="absolute -right-8 -bottom-8 w-64 h-64 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
        </div>

        <!-- 2 Financial & Operational Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Card 1: Total Pendapatan Bersih Agen -->
            <div class="bg-surface-white rounded-2xl p-6 border border-emerald-500/25 shadow-sm hover:shadow-md transition-all duration-200 relative overflow-hidden group">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold text-outline uppercase tracking-wider">Total Pendapatan Bersih</span>
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined fill text-2xl">account_balance_wallet</span>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="text-3xl font-black text-oceanic-deep">
                        Rp {{ number_format($total_earning ?? $totalEarnings, 0, ',', '.') }}
                    </div>
                    <p class="text-xs font-medium text-emerald-700 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">verified</span>
                        <span>Hak bersih agen (90%) dari seluruh booking sah</span>
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-outline-variant/20 text-[11px] text-outline flex justify-between">
                    <span>Terisolasi: Paket Anda Saja</span>
                    <span class="font-bold text-emerald-700">Multi-tenant Aman</span>
                </div>
            </div>

            <!-- Card 2: Pesanan Aktif -->
            <div class="bg-surface-white rounded-2xl p-6 border border-outline-variant/30 shadow-sm hover:shadow-md transition-all duration-200 relative overflow-hidden group">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold text-outline uppercase tracking-wider">Pesanan Aktif</span>
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined fill text-2xl">pending_actions</span>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="text-3xl font-black text-oceanic-deep">
                        {{ number_format($active_bookings_count ?? $activeOrdersCount, 0, ',', '.') }} <span class="text-base font-semibold text-outline">Pesanan</span>
                    </div>
                    <p class="text-xs font-medium text-amber-700 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">schedule</span>
                        <span>Status: Menunggu Pembayaran & Dibayar</span>
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-outline-variant/20 text-[11px] text-outline flex justify-between">
                    <span>Perlu dipantau & dipersiapkan</span>
                    <span class="font-bold text-amber-700">Operasional</span>
                </div>
            </div>
        </div>

        <!-- Tabel Pesanan Terbaru Milik Agen -->
        <div class="bg-surface-white rounded-3xl border border-outline-variant/30 shadow-sm overflow-hidden">
            <div class="p-6 md:p-8 border-b border-outline-variant/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="font-display-md text-lg md:text-xl font-bold text-oceanic-deep flex items-center gap-2">
                        <span class="material-symbols-outlined text-tropical-teal fill">receipt_long</span>
                        <span>Daftar Pesanan & Pendapatan Terbaru</span>
                    </h3>
                    <p class="text-xs md:text-sm text-outline mt-1">
                        Seluruh pemesanan wisatawan pada paket wisata milik {{ $agent->agent_name }}. Ubah status perjalanan sesuai jadwal di lapangan.
                    </p>
                </div>
                <div class="text-xs font-bold text-outline bg-surface-container-low px-3 py-1.5 rounded-full inline-flex items-center gap-1.5 self-start sm:self-auto">
                    <span class="material-symbols-outlined text-sm">lock</span>
                    <span>Akses Finansial Terisolasi</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low/50 text-xs font-bold text-outline uppercase tracking-wider border-b border-outline-variant/30">
                            <th class="py-4 px-6">Kode Booking</th>
                            <th class="py-4 px-6">Paket Wisata & Jadwal</th>
                            <th class="py-4 px-6">Wisatawan</th>
                            <th class="py-4 px-6 text-center">Pax</th>
                            <th class="py-4 px-6 text-right">Pendapatan Bersih (90%)</th>
                            <th class="py-4 px-6 text-center">Status Booking</th>
                            <th class="py-4 px-6 text-center">Aksi Transisi Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/20 text-sm">
                        @forelse(($recent_bookings ?? $recentBookings) as $booking)
                            <tr class="hover:bg-surface-container-lowest/80 transition-colors">
                                <!-- Kode Booking -->
                                <td class="py-4 px-6">
                                    <div class="font-mono font-bold text-tropical-teal">
                                        {{ $booking->booking_code }}
                                    </div>
                                    <div class="text-[11px] text-outline mt-0.5">
                                        {{ $booking->created_at->format('d M Y, H:i') }}
                                    </div>
                                </td>

                                <!-- Paket & Jadwal -->
                                <td class="py-4 px-6">
                                    <div class="font-bold text-on-surface hover:text-tropical-teal transition-colors">
                                        {{ $booking->package->name ?? '-' }}
                                    </div>
                                    <div class="text-xs text-outline flex items-center gap-1.5 mt-0.5">
                                        <span class="material-symbols-outlined text-xs">calendar_month</span>
                                        <span>
                                            {{ $booking->travel_date ? $booking->travel_date->translatedFormat('d M Y') : ($booking->schedule ? \Carbon\Carbon::parse($booking->schedule->start_date)->translatedFormat('d M Y') : '-') }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Wisatawan -->
                                <td class="py-4 px-6">
                                    <div class="font-medium text-on-surface">
                                        {{ $booking->user->name ?? '-' }}
                                    </div>
                                    <div class="text-xs text-outline">
                                        {{ $booking->user->email ?? '-' }}
                                    </div>
                                </td>

                                <!-- Pax -->
                                <td class="py-4 px-6 text-center font-bold text-on-surface">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-surface-container-low text-xs">
                                        {{ $booking->guest_count }} Pax
                                    </span>
                                </td>

                                <!-- Hak Bersih Agen -->
                                <td class="py-4 px-6 text-right">
                                    <div class="font-black text-emerald-700 text-base">
                                        Rp {{ number_format($booking->agent_earning, 0, ',', '.') }}
                                    </div>
                                    <div class="text-[11px] text-outline">
                                        Total: Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                    </div>
                                </td>

                                <!-- Status Booking Badge -->
                                <td class="py-4 px-6 text-center">
                                    @if($booking->status === 'dibayar')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Dibayar</span>
                                        </span>
                                    @elseif($booking->status === 'berjalan')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200 animate-pulse">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            <span>Berjalan</span>
                                        </span>
                                    @elseif($booking->status === 'selesai')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                            <span>Selesai</span>
                                        </span>
                                    @elseif($booking->status === 'menunggu_pembayaran')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            <span>Menunggu Bayar</span>
                                        </span>
                                    @elseif(in_array($booking->status, ['dibatalkan', 'kedaluwarsa']))
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            <span>{{ ucfirst($booking->status) }}</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-800">
                                            <span>{{ ucfirst($booking->status) }}</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi Transisi Status Agen -->
                                <td class="py-4 px-6 text-center">
                                    @if($booking->status === 'dibayar')
                                        <form method="POST" action="{{ route('agent.bookings.update-status', $booking) }}" onsubmit="return confirm('Mulai perjalanan untuk pesanan {{ $booking->booking_code }}?')">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="berjalan">
                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm hover:shadow transition-all duration-150 active:scale-95">
                                                <span class="material-symbols-outlined text-sm">play_arrow</span>
                                                <span>Mulai Trip</span>
                                            </button>
                                        </form>
                                    @elseif($booking->status === 'berjalan')
                                        <form method="POST" action="{{ route('agent.bookings.update-status', $booking) }}" onsubmit="return confirm('Tandai perjalanan {{ $booking->booking_code }} telah selesai?')">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="selesai">
                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm hover:shadow transition-all duration-150 active:scale-95">
                                                <span class="material-symbols-outlined text-sm">task_alt</span>
                                                <span>Selesaikan Trip</span>
                                            </button>
                                        </form>
                                    @elseif($booking->status === 'selesai')
                                        <span class="text-xs font-semibold text-emerald-600 inline-flex items-center gap-1">
                                            <span class="material-symbols-outlined text-sm">check</span>
                                            <span>Trip Rampung</span>
                                        </span>
                                    @else
                                        <span class="text-xs text-outline italic">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-outline">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-4xl text-outline/50">inbox</span>
                                        <p class="font-medium text-sm">Belum ada pemesanan wisatawan yang masuk untuk paket Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination (jika menggunakan Paginator) -->
            @if(method_exists($recent_bookings ?? $recentBookings, 'hasPages') && ($recent_bookings ?? $recentBookings)->hasPages())
                <div class="p-6 border-t border-outline-variant/30 bg-surface-container-lowest">
                    {{ ($recent_bookings ?? $recentBookings)->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.agent>
