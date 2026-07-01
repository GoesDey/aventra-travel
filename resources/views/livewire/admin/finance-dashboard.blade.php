<div>
    <!-- Metric Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="bg-surface-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,35,102,0.05)] border border-outline-variant/30 flex flex-col justify-between h-40">
            <div class="flex justify-between items-start">
                <div class="w-12 h-12 rounded-xl bg-primary-container/20 flex items-center justify-center text-oceanic-deep">
                    <span class="material-symbols-outlined fill">account_balance_wallet</span>
                </div>
                <div class="px-2 py-1 rounded-md text-xs font-bold flex items-center gap-1 {{ $revenueGrowth >= 0 ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                    <span class="material-symbols-outlined text-[14px]">{{ $revenueGrowth >= 0 ? 'trending_up' : 'trending_down' }}</span>
                    {{ $revenueGrowth > 0 ? '+' : '' }}{{ $revenueGrowth }}%
                </div>
            </div>
            <div>
                <p class="font-label text-sm text-outline mb-1">Total Revenue</p>
                <h3 class="font-display text-3xl font-bold text-on-surface">Rp.{{ number_format($totalRevenue, 2) }}</h3>
            </div>
        </div>

        <!-- Active Bookings -->
        <div class="bg-surface-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,35,102,0.05)] border border-outline-variant/30 flex flex-col justify-between h-40">
            <div class="flex justify-between items-start">
                <div class="w-12 h-12 rounded-xl bg-tropical-teal/10 flex items-center justify-center text-tropical-teal">
                    <span class="material-symbols-outlined fill">confirmation_number</span>
                </div>
                <div class="px-2 py-1 rounded-md text-xs font-bold flex items-center gap-1 {{ $activeBookingsGrowth >= 0 ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                    <span class="material-symbols-outlined text-[14px]">{{ $activeBookingsGrowth >= 0 ? 'trending_up' : 'trending_down' }}</span>
                    {{ $activeBookingsGrowth > 0 ? '+' : '' }}{{ $activeBookingsGrowth }}%
                </div>
            </div>
            <div>
                <p class="font-label text-sm text-outline mb-1">Active Bookings</p>
                <h3 class="font-display text-3xl font-bold text-on-surface">{{ number_format($activeBookings) }}</h3>
            </div>
        </div>

        <!-- Avg Booking Value -->
        <div class="bg-surface-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,35,102,0.05)] border border-outline-variant/30 flex flex-col justify-between h-40">
            <div class="flex justify-between items-start">
                <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500">
                    <span class="material-symbols-outlined fill">monetization_on</span>
                </div>
                <div class="px-2 py-1 rounded-md text-xs font-bold flex items-center gap-1 {{ $avgBookingValueGrowth > 0 ? 'bg-green-50 text-green-600' : ($avgBookingValueGrowth < 0 ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-gray-600') }}">
                    <span class="material-symbols-outlined text-[14px]">{{ $avgBookingValueGrowth > 0 ? 'trending_up' : ($avgBookingValueGrowth < 0 ? 'trending_down' : 'trending_flat') }}</span>
                    {{ $avgBookingValueGrowth > 0 ? '+' : '' }}{{ $avgBookingValueGrowth }}%
                </div>
            </div>
            <div>
                <p class="font-label text-sm text-outline mb-1">Avg. Booking Value</p>
                <h3 class="font-display text-3xl font-bold text-on-surface">Rp{{ number_format($avgBookingValue, 2) }}</h3>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Revenue Trend Chart -->
        <div class="lg:col-span-2 bg-surface-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,35,102,0.05)] border border-outline-variant/30">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="font-headline text-lg text-oceanic-deep">Revenue Trend</h3>
                    <p class="text-xs text-outline">Monthly breakdown for current year</p>
                </div>
                <select class="bg-surface-container-lowest border border-outline-variant/50 rounded-lg px-3 py-1.5 text-sm text-on-surface focus:ring-oceanic-deep focus:border-oceanic-deep">
                    <option>2024</option>
                    <option>2025</option>
                    <option>2026</option>
                </select>
            </div>
            
            <div class="h-72 w-full" x-data="{ monthlyData: @js($monthlyRevenue) }" x-init="
                const ctx = $refs.canvas.getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [{
                            label: 'Revenue',
                            data: monthlyData,
                            backgroundColor: (context) => {
                                const index = context.dataIndex;
                                return (index === 6 || index === 7) ? '#008080' : '#002366'; // Teal for Jul/Aug, Deep Blue for rest
                            },
                            borderRadius: 4,
                            barPercentage: 0.6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                border: { display: false },
                                grid: { color: '#E2E8F0', drawTicks: false },
                                ticks: {
                                    callback: function(value) { return 'Rp' + (value / 1000) + 'K'; },
                                    color: '#94A3B8',
                                    font: { family: 'Inter', size: 11 }
                                }
                            },
                            x: {
                                border: { display: false },
                                grid: { display: false },
                                ticks: {
                                    color: '#94A3B8',
                                    font: { family: 'Inter', size: 11 }
                                }
                            }
                        }
                    }
                });
            ">
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>

        <!-- Package Performance -->
        <div class="bg-surface-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,35,102,0.05)] border border-outline-variant/30">
            <h3 class="font-headline text-lg text-oceanic-deep">Package Performance</h3>
            <p class="text-xs text-outline mb-8">Revenue by destination region</p>

            <div class="space-y-6">
                @foreach($packagePerformance as $index => $package)
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-on-surface font-medium truncate w-2/3">{{ $package['name'] }}</span>
                            <span class="text-oceanic-deep font-bold">{{ $package['percentage'] }}%</span>
                        </div>
                        <div class="w-full bg-surface-container-lowest rounded-full h-2">
                            <div class="h-2 rounded-full {{ $index === 0 ? 'bg-tropical-teal' : ($index === 1 ? 'bg-oceanic-deep' : ($index === 2 ? 'bg-soft-coral' : 'bg-outline-variant')) }}" style="width: {{ $package['percentage'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-surface-white rounded-2xl shadow-[0_4px_20px_rgba(0,35,102,0.05)] border border-outline-variant/30 overflow-hidden">
        <div class="p-6 border-b border-outline-variant/30 flex justify-between items-center">
            <h3 class="font-headline text-lg text-oceanic-deep">Recent Transactions</h3>
            <a href="{{ route('admin.bookings') }}" class="text-sm font-label-bold text-oceanic-deep hover:text-tropical-teal transition-colors">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-outline-variant/30 text-xs font-bold text-outline uppercase tracking-wider bg-surface-container-lowest/50">
                        <th class="px-6 py-4">Transaction ID</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Package</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    @forelse($recentTransactions as $transaction)
                        <tr class="hover:bg-surface-container-lowest/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-body-md text-sm text-outline">#TRX-{{ str_pad($transaction->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-label-bold text-sm text-on-surface">{{ $transaction->user->name ?? 'Guest' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-body-md text-sm text-on-surface-variant">{{ $transaction->package->name ?? 'Deleted Package' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-body-md text-sm text-outline">{{ $transaction->created_at->format('M d, Y') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-label-bold text-sm text-on-surface">Rp{{ number_format($transaction->total_price, 2) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($transaction->status === 'completed' || $transaction->status === 'confirmed')
                                    <span class="px-2.5 py-1 rounded-md bg-tropical-teal/10 text-tropical-teal text-xs font-bold">Completed</span>
                                @elseif($transaction->status === 'pending')
                                    <span class="px-2.5 py-1 rounded-md bg-sunset-gold/20 text-yellow-700 text-xs font-bold">Processing</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-md bg-error-container/50 text-error text-xs font-bold">Refunded</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-outline font-body-md">No recent transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
