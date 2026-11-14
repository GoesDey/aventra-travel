<x-layouts.admin title="Super Admin Financial Dashboard">
    <div class="space-y-8">
        <!-- Hero Header Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-oceanic-deep via-oceanic-deep/95 to-tropical-teal p-8 text-white shadow-xl">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-white/15 backdrop-blur-md text-tropical-teal border border-white/20">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Financial Settlement &amp; Monitoring Engine</span>
                    </div>
                    <h2 class="font-display-lg text-2xl md:text-3xl font-bold tracking-tight">
                        Super Admin Financial Dashboard
                    </h2>
                    <p class="text-sm text-slate-200 max-w-2xl leading-relaxed">
                        Monitor gross revenue, partner agent disbursements, and net platform commissions in real time from all verified bookings.
                    </p>
                </div>
                
                <div class="flex items-center gap-3 self-start md:self-auto">
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 text-right">
                        <span class="text-xs text-slate-300 block font-medium">Platform Commission</span>
                        <span class="text-xl font-extrabold text-tropical-teal">10%</span>
                        <span class="text-[11px] text-slate-300 block">TriHita Standard Rate</span>
                    </div>
                </div>
            </div>

            <!-- Background Decorative Shape -->
            <div class="absolute -right-10 -bottom-10 w-72 h-72 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
        </div>

        <!-- 3 Key Financial Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: Total Platform Commission -->
            <div class="bg-surface-white rounded-2xl p-6 border border-emerald-500/25 shadow-sm hover:shadow-md transition-all duration-200 relative overflow-hidden group">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold text-outline uppercase tracking-wider">Total Platform Revenue</span>
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined fill text-2xl">account_balance_wallet</span>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="text-3xl font-black text-oceanic-deep">
                        Rp {{ number_format($total_commission ?? $totalPlatformCommission, 0, ',', '.') }}
                    </div>
                    <p class="text-xs font-medium text-emerald-700 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">verified</span>
                        <span>Net platform commission (10%) from all verified bookings</span>
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-outline-variant/20 text-[11px] text-outline flex justify-between">
                    <span>Basis: 10% Platform Commission</span>
                    <span class="font-bold text-emerald-700">Platform Revenue</span>
                </div>
            </div>

            <!-- Card 2: Total Successful Bookings -->
            <div class="bg-surface-white rounded-2xl p-6 border border-outline-variant/30 shadow-sm hover:shadow-md transition-all duration-200 relative overflow-hidden group">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold text-outline uppercase tracking-wider">Total Successful Bookings</span>
                    <div class="w-12 h-12 rounded-xl bg-tropical-teal/10 text-tropical-teal flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined fill text-2xl">receipt_long</span>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="text-3xl font-black text-oceanic-deep">
                        {{ number_format($total_successful_transactions ?? $totalSuccessfulBookings, 0, ',', '.') }} <span class="text-base font-semibold text-outline">Bookings</span>
                    </div>
                    <p class="text-xs font-medium text-tropical-teal flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        <span>Status: Paid, Ongoing &amp; Completed</span>
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-outline-variant/20 text-[11px] text-outline flex justify-between">
                    <span>Gross Turnover: Rp {{ number_format($totalGrossRevenue, 0, ',', '.') }}</span>
                    <span class="font-bold text-on-surface">Verified Transactions</span>
                </div>
            </div>

            <!-- Card 3: Active Partner Agents -->
            <div class="bg-surface-white rounded-2xl p-6 border border-outline-variant/30 shadow-sm hover:shadow-md transition-all duration-200 relative overflow-hidden group">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold text-outline uppercase tracking-wider">Active Partner Agents</span>
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined fill text-2xl">storefront</span>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="text-3xl font-black text-oceanic-deep">
                        {{ number_format($total_active_agents ?? $activeAgentsCount, 0, ',', '.') }} <span class="text-base font-semibold text-outline">Partners</span>
                    </div>
                    <p class="text-xs font-medium text-purple-700 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">verified_user</span>
                        <span>Verified agents with active account status</span>
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-outline-variant/20 text-[11px] text-outline flex justify-between">
                    <span>Verified Platform Operators</span>
                    <span class="font-bold text-purple-700">B2B Partners</span>
                </div>
            </div>
        </div>

        <!-- Tabel Performa Agen Teratas -->
        <div class="bg-surface-white rounded-3xl border border-outline-variant/30 shadow-sm overflow-hidden">
            <div class="p-6 md:p-8 border-b border-outline-variant/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="font-display-md text-lg md:text-xl font-bold text-oceanic-deep flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-500 fill">workspace_premium</span>
                        <span>Top Performing Travel Agents</span>
                    </h3>
                    <p class="text-xs md:text-sm text-outline mt-1">
                        Ranking of tour operators based on platform revenue contribution and completed volume.
                    </p>
                </div>
                <div class="text-xs font-bold text-tropical-teal bg-tropical-teal/10 px-3 py-1.5 rounded-full inline-flex items-center gap-1.5 self-start sm:self-auto">
                    <span class="material-symbols-outlined text-sm">leaderboard</span>
                    <span>Top 5 Contributors</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low/50 text-xs font-bold text-outline uppercase tracking-wider border-b border-outline-variant/30">
                            <th class="py-4 px-6 text-center w-16">Rank</th>
                            <th class="py-4 px-6">Partner Agent</th>
                            <th class="py-4 px-6 text-center">Fleet</th>
                            <th class="py-4 px-6 text-center">Completed Bookings</th>
                            <th class="py-4 px-6 text-right">Gross Sales</th>
                            <th class="py-4 px-6 text-right">Platform Share (10%)</th>
                            <th class="py-4 px-6 text-right">Agent Payout (90%)</th>
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
                                        <span>{{ $topAgent->vehicle_count }} Units</span>
                                    </span>
                                </td>

                                <!-- Successful Bookings -->
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $bookingsCount }} Bookings
                                    </span>
                                </td>

                                <!-- Total Turnover -->
                                <td class="py-4 px-6 text-right font-bold text-oceanic-deep">
                                    Rp {{ number_format($turnover, 0, ',', '.') }}
                                </td>

                                <!-- Platform Commission -->
                                <td class="py-4 px-6 text-right font-extrabold text-emerald-600">
                                    Rp {{ number_format($comm, 0, ',', '.') }}
                                </td>

                                <!-- Agent Earning -->
                                <td class="py-4 px-6 text-right font-bold text-blue-700">
                                    Rp {{ number_format($earning, 0, ',', '.') }}
                                </td>

                                <!-- Agent Status -->
                                <td class="py-4 px-6 text-center">
                                    @if(($topAgent->user->status ?? '') === 'active')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Active</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            <span>{{ ucfirst($topAgent->user->status ?? 'Inactive') }}</span>
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-outline">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-4xl text-outline/50">storefront</span>
                                        <p class="font-medium text-sm">No partner agent transactions recorded yet.</p>
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
                        <span>Recent Verified Transactions (Cross-Agent Audit)</span>
                    </h3>
                    <p class="text-xs md:text-sm text-outline mt-1">
                        List of the latest customer bookings with verified payment status via Midtrans Snap.
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low/50 text-xs font-bold text-outline uppercase tracking-wider border-b border-outline-variant/30">
                            <th class="py-3.5 px-6">Booking Code</th>
                            <th class="py-3.5 px-6">Tour Package &amp; Operator</th>
                            <th class="py-3.5 px-6">Traveler</th>
                            <th class="py-3.5 px-6 text-right">Total Amount</th>
                            <th class="py-3.5 px-6 text-right">Platform Fee (10%)</th>
                            <th class="py-3.5 px-6 text-right">Agent Earning (90%)</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-center">Timestamp</th>
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
                                    <div class="text-xs text-outline">{{ $tx->package->agent->agent_name ?? 'Official Agent' }}</div>
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
                                            <span>Paid</span>
                                        </span>
                                    @elseif($tx->status === 'berjalan')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            <span>Ongoing</span>
                                        </span>
                                    @elseif($tx->status === 'selesai')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                            <span>Completed</span>
                                        </span>
                                    @elseif($tx->status === 'menunggu_pembayaran')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            <span>Awaiting Payment</span>
                                        </span>
                                    @elseif($tx->status === 'dibatalkan')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            <span>Cancelled</span>
                                        </span>
                                    @elseif($tx->status === 'kedaluwarsa')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                            <span>Expired</span>
                                        </span>
                                    @elseif($tx->status === 'perlu_refund')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            <span>Refund Needed</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800">
                                            <span>{{ ucfirst(str_replace('_', ' ', $tx->status)) }}</span>
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
                                    No recent transactions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
