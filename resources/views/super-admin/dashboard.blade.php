<x-layouts.admin title="Super Admin Financial Dashboard">
    <div class="space-y-8">
        <!-- Hero Header Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-oceanic-deep via-oceanic-deep/95 to-tropical-teal p-8 text-white shadow-xl">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-white/15 backdrop-blur-md text-tropical-teal border border-white/20">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Fase 3: Financial Settlement & Monitoring Engine</span>
                    </div>
                    <h2 class="font-display-lg text-2xl md:text-3xl font-bold tracking-tight">
                        Dashboard Keuangan Super Admin
                    </h2>
                    <p class="text-sm text-slate-200 max-w-2xl leading-relaxed">
                        Pantau arus kas kotor, pembagian hak mitra agen, dan komisi bersih platform TriHitaTour secara real-time dari seluruh transaksi booking yang sah.
                    </p>
                </div>
                
                <div class="flex items-center gap-3 self-start md:self-auto">
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 text-right">
                        <span class="text-xs text-slate-300 block font-medium">Tarif Komisi Platform</span>
                        <span class="text-xl font-extrabold text-tropical-teal">10%</span>
                        <span class="text-[11px] text-slate-300 block">Sesuai TriHita Config</span>
                    </div>
                </div>
            </div>

            <!-- Background Decorative Shape -->
            <div class="absolute -right-10 -bottom-10 w-72 h-72 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
        </div>

        <!-- 4 Key Financial Metrics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1: Komisi Bersih Platform -->
            <div class="bg-surface-white rounded-2xl p-6 border border-emerald-500/20 shadow-sm hover:shadow-md transition-all duration-200 relative overflow-hidden group">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold text-outline uppercase tracking-wider">Komisi Bersih Platform</span>
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined fill text-2xl">account_balance_wallet</span>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="text-2xl font-black text-oceanic-deep">
                        Rp {{ number_format($totalPlatformCommission, 0, ',', '.') }}
                    </div>
                    <p class="text-xs font-medium text-emerald-700 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">verified</span>
                        <span>Hak Pendapatan TriHitaTour</span>
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-outline-variant/20 text-[11px] text-outline flex justify-between">
                    <span>Basis: 10% komisi per pax</span>
                    <span class="font-bold text-on-surface">Auto-Split</span>
                </div>
            </div>

            <!-- Card 2: Total Pendapatan Kotor (Gross Volume) -->
            <div class="bg-surface-white rounded-2xl p-6 border border-outline-variant/30 shadow-sm hover:shadow-md transition-all duration-200 relative overflow-hidden group">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold text-outline uppercase tracking-wider">Total Transaksi Kotor</span>
                    <div class="w-12 h-12 rounded-xl bg-tropical-teal/10 text-tropical-teal flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined fill text-2xl">trending_up</span>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="text-2xl font-black text-oceanic-deep">
                        Rp {{ number_format($totalGrossRevenue, 0, ',', '.') }}
                    </div>
                    <p class="text-xs font-medium text-tropical-teal flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">receipt_long</span>
                        <span>{{ number_format($totalSuccessfulBookings) }} Pesanan Berhasil</span>
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-outline-variant/20 text-[11px] text-outline flex justify-between">
                    <span>Status: Dibayar/Berjalan/Selesai</span>
                    <span class="font-bold text-on-surface">Midtrans Valid</span>
                </div>
            </div>

            <!-- Card 3: Total Hak Pendapatan Mitra Agen -->
            <div class="bg-surface-white rounded-2xl p-6 border border-outline-variant/30 shadow-sm hover:shadow-md transition-all duration-200 relative overflow-hidden group">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold text-outline uppercase tracking-wider">Hak Pendapatan Mitra</span>
                    <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined fill text-2xl">handshake</span>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="text-2xl font-black text-oceanic-deep">
                        Rp {{ number_format($totalAgentEarnings, 0, ',', '.') }}
                    </div>
                    <p class="text-xs font-medium text-blue-700 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">payments</span>
                        <span>90% dari Gross Transaksi</span>
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-outline-variant/20 text-[11px] text-outline flex justify-between">
                    <span>Siap dicairkan ke Agen</span>
                    <span class="font-bold text-on-surface">Disbursement</span>
                </div>
            </div>

            <!-- Card 4: Mitra Agen Aktif -->
            <div class="bg-surface-white rounded-2xl p-6 border border-outline-variant/30 shadow-sm hover:shadow-md transition-all duration-200 relative overflow-hidden group">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold text-outline uppercase tracking-wider">Mitra Agen Aktif</span>
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined fill text-2xl">storefront</span>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="text-2xl font-black text-oceanic-deep">
                        {{ $activeAgentsCount }} <span class="text-sm font-semibold text-outline">/ {{ $totalAgentsCount }} Agen</span>
                    </div>
                    <p class="text-xs font-medium text-purple-700 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        <span>Status Akun: Active</span>
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-outline-variant/20 text-[11px] text-outline flex justify-between">
                    <span>Mitra Terverifikasi Platform</span>
                    <span class="font-bold text-on-surface">B2B Partner</span>
                </div>
            </div>
        </div>

        <!-- Tabel 5 Agen dengan Transaksi Tertinggi -->
        <div class="bg-surface-white rounded-3xl border border-outline-variant/30 shadow-sm overflow-hidden">
            <div class="p-6 md:p-8 border-b border-outline-variant/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="font-display-md text-lg md:text-xl font-bold text-oceanic-deep flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-500 fill">workspace_premium</span>
                        <span>Top 5 Agen Berkinerja Finansial Tertinggi</span>
                    </h3>
                    <p class="text-xs md:text-sm text-outline mt-1">
                        Peringkat mitra tour operator berdasarkan total volume transaksi sukses dan kontribusi komisi.
                    </p>
                </div>
                <div class="text-xs font-bold text-tropical-teal bg-tropical-teal/10 px-3 py-1.5 rounded-full inline-flex items-center gap-1.5 self-start sm:self-auto">
                    <span class="material-symbols-outlined text-sm">leaderboard</span>
                    <span>Peringkat Berjalan</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low/50 text-xs font-bold text-outline uppercase tracking-wider border-b border-outline-variant/30">
                            <th class="py-4 px-6 text-center w-16">Rank</th>
                            <th class="py-4 px-6">Informasi Mitra Agen</th>
                            <th class="py-4 px-6 text-center">Armada</th>
                            <th class="py-4 px-6 text-center">Pesanan Sukses</th>
                            <th class="py-4 px-6 text-right">Total Omset Agen</th>
                            <th class="py-4 px-6 text-right">Komisi Platform (10%)</th>
                            <th class="py-4 px-6 text-right">Hak Bersih Agen (90%)</th>
                            <th class="py-4 px-6 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/20 text-sm">
                        @forelse($topAgents as $index => $topAgent)
                            @php
                                $turnover = (int) ($topAgent->total_turnover ?? 0);
                                $comm = (int) ($topAgent->total_commission ?? 0);
                                $earning = (int) ($topAgent->total_agent_earning ?? 0);
                                $bookingsCount = (int) ($topAgent->successful_bookings_count ?? 0);
                            @endphp
                            <tr class="hover:bg-surface-container-lowest/80 transition-colors">
                                <!-- Rank Medal -->
                                <td class="py-4 px-6 text-center font-bold">
                                    @if($index === 0)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-700 font-black shadow-sm">
                                            🥇
                                        </span>
                                    @elseif($index === 1)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-200 text-slate-700 font-black shadow-sm">
                                            🥈
                                        </span>
                                    @elseif($index === 2)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-700/20 text-amber-800 font-black shadow-sm">
                                            🥉
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-surface-container-low text-outline font-bold">
                                            #{{ $index + 1 }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Agen Details -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-tropical-teal/15 text-tropical-teal flex items-center justify-center font-bold text-sm shrink-0">
                                            {{ substr($topAgent->agent_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-on-surface hover:text-tropical-teal transition-colors">
                                                {{ $topAgent->agent_name }}
                                            </div>
                                            <div class="text-xs text-outline flex items-center gap-2 mt-0.5">
                                                <span>{{ $topAgent->user->name ?? '-' }}</span>
                                                <span>•</span>
                                                <span>{{ $topAgent->phone_number ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Armada -->
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-lg bg-surface-container-low text-on-surface">
                                        <span class="material-symbols-outlined text-xs text-outline">directions_bus</span>
                                        <span>{{ $topAgent->vehicle_count }} Unit</span>
                                    </span>
                                </td>

                                <!-- Pesanan Sukses -->
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $bookingsCount }} Transaksi
                                    </span>
                                </td>

                                <!-- Total Omset -->
                                <td class="py-4 px-6 text-right font-bold text-oceanic-deep">
                                    Rp {{ number_format($turnover, 0, ',', '.') }}
                                </td>

                                <!-- Komisi Platform -->
                                <td class="py-4 px-6 text-right font-extrabold text-emerald-600">
                                    Rp {{ number_format($comm, 0, ',', '.') }}
                                </td>

                                <!-- Hak Agen -->
                                <td class="py-4 px-6 text-right font-bold text-blue-700">
                                    Rp {{ number_format($earning, 0, ',', '.') }}
                                </td>

                                <!-- Status Agen -->
                                <td class="py-4 px-6 text-center">
                                    @if(($topAgent->user->status ?? '') === 'active')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Aktif</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            <span>{{ ucfirst($topAgent->user->status ?? 'Non-Aktif') }}</span>
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-outline">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-4xl text-outline/50">storefront</span>
                                        <p class="font-medium text-sm">Belum ada data transaksi mitra agen yang tercatat.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel Riwayat Transaksi Sukses Terbaru Lintas Agen -->
        <div class="bg-surface-white rounded-3xl border border-outline-variant/30 shadow-sm overflow-hidden">
            <div class="p-6 md:p-8 border-b border-outline-variant/30 flex items-center justify-between">
                <div>
                    <h3 class="font-display-md text-lg md:text-xl font-bold text-oceanic-deep flex items-center gap-2">
                        <span class="material-symbols-outlined text-tropical-teal fill">history</span>
                        <span>Transaksi Sah Terbaru (Audit Lintas Agen)</span>
                    </h3>
                    <p class="text-xs md:text-sm text-outline mt-1">
                        Daftar 6 pemesanan terakhir yang statusnya telah terkonfirmasi dibayar melalui Midtrans Snap.
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low/50 text-xs font-bold text-outline uppercase tracking-wider border-b border-outline-variant/30">
                            <th class="py-3.5 px-6">Kode Booking</th>
                            <th class="py-3.5 px-6">Paket Wisata & Mitra</th>
                            <th class="py-3.5 px-6">Pelanggan</th>
                            <th class="py-3.5 px-6 text-right">Total Transaksi</th>
                            <th class="py-3.5 px-6 text-right">Komisi (10%)</th>
                            <th class="py-3.5 px-6 text-right">Hak Agen (90%)</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-center">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/20 text-sm">
                        @forelse($recentTransactions as $tx)
                            <tr class="hover:bg-surface-container-lowest/80 transition-colors">
                                <td class="py-3.5 px-6 font-mono font-bold text-tropical-teal">
                                    {{ $tx->booking_code }}
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="font-bold text-on-surface">{{ $tx->package->name ?? '-' }}</div>
                                    <div class="text-xs text-outline">{{ $tx->package->agent->agent_name ?? 'Agen' }}</div>
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="font-medium text-on-surface">{{ $tx->user->name ?? '-' }}</div>
                                    <div class="text-xs text-outline">{{ $tx->user->email ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-6 text-right font-bold text-oceanic-deep">
                                    Rp {{ number_format($tx->total_price, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-6 text-right font-extrabold text-emerald-600">
                                    Rp {{ number_format($tx->commission_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-6 text-right font-semibold text-blue-700">
                                    Rp {{ number_format($tx->agent_earning, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-6 text-center">
                                    @if($tx->status === 'dibayar')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Dibayar</span>
                                        </span>
                                    @elseif($tx->status === 'berjalan')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            <span>Berjalan</span>
                                        </span>
                                    @elseif($tx->status === 'selesai')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                            <span>Selesai</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-center text-xs text-outline">
                                    {{ $tx->created_at->format('d M Y, H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-outline">
                                    Belum ada transaksi terkini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
