<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Booking;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceDashboard extends Component
{
    public $totalRevenue = 0;
    public $revenueGrowth = 0;
    public $activeBookings = 0;
    public $activeBookingsGrowth = 0;
    public $avgBookingValue = 0;
    public $avgBookingValueGrowth = 0;
    public $refunds = 0;

    public $selectedYear;
    public $availableYears = [];

    public $monthlyRevenue = [];
    public $packagePerformance = [];
    public $recentTransactions = [];

    public function mount()
    {
        $this->selectedYear = Carbon::now()->year;
        $this->availableYears = Booking::selectRaw('YEAR(created_at) as year')->distinct()->orderBy('year', 'desc')->pluck('year')->toArray();
        if (empty($this->availableYears)) {
            $this->availableYears = [$this->selectedYear];
        }
        if (!in_array($this->selectedYear, $this->availableYears)) {
            $this->availableYears[] = $this->selectedYear;
            rsort($this->availableYears);
        }

        $this->loadMetrics();
        $this->loadChartData();
        $this->loadRecentTransactions();
    }

    public function updatedSelectedYear()
    {
        $this->loadChartData();
    }

    private function loadMetrics()
    {
        // Total Revenue: sum of total_price where status is completed
        $this->totalRevenue = Booking::whereIn('status', ['completed', 'confirmed'])->sum('total_price');

        // Active Bookings: count of pending/confirmed
        $this->activeBookings = Booking::whereIn('status', ['pending', 'confirmed'])->count();

        // Avg Booking Value
        $revenueCount = Booking::whereIn('status', ['completed', 'confirmed'])->count();
        $this->avgBookingValue = $revenueCount > 0 ? $this->totalRevenue / $revenueCount : 0;

        // Refunds (Canceled bookings)
        $this->refunds = Booking::where('status', 'cancelled')->sum('total_price');

        // Growth Metrics (Month over Month)
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $currentMonthRevenue = Booking::whereIn('status', ['completed', 'confirmed'])
            ->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->sum('total_price');
        $previousMonthRevenue = Booking::whereIn('status', ['completed', 'confirmed'])
            ->whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->sum('total_price');
        $this->revenueGrowth = $previousMonthRevenue > 0 ? round((($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 1) : ($currentMonthRevenue > 0 ? 100 : 0);

        $currentMonthActive = Booking::whereIn('status', ['pending', 'confirmed'])
            ->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();
        $previousMonthActive = Booking::whereIn('status', ['pending', 'confirmed'])
            ->whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->count();
        $this->activeBookingsGrowth = $previousMonthActive > 0 ? round((($currentMonthActive - $previousMonthActive) / $previousMonthActive) * 100, 1) : ($currentMonthActive > 0 ? 100 : 0);

        $currentMonthRevCount = Booking::whereIn('status', ['completed', 'confirmed'])
            ->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();
        $previousMonthRevCount = Booking::whereIn('status', ['completed', 'confirmed'])
            ->whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->count();
        
        $currentMonthAvg = $currentMonthRevCount > 0 ? $currentMonthRevenue / $currentMonthRevCount : 0;
        $previousMonthAvg = $previousMonthRevCount > 0 ? $previousMonthRevenue / $previousMonthRevCount : 0;
        $this->avgBookingValueGrowth = $previousMonthAvg > 0 ? round((($currentMonthAvg - $previousMonthAvg) / $previousMonthAvg) * 100, 1) : ($currentMonthAvg > 0 ? 100 : 0);

    }

    private function loadChartData()
    {
        // Monthly Revenue Trend for selected year
        $currentYear = $this->selectedYear ?? Carbon::now()->year;
        
        $monthlyData = Booking::whereIn('status', ['completed', 'confirmed'])
            ->whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month, SUM(total_price) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Fill in missing months with 0
        $this->monthlyRevenue = [];
        for ($i = 1; $i <= 12; $i++) {
            $this->monthlyRevenue[] = $monthlyData[$i] ?? 0;
        }

        // Package Performance
        $totalRev = $this->totalRevenue > 0 ? $this->totalRevenue : 1; // avoid division by zero
        
        $packageData = Booking::whereIn('status', ['completed', 'confirmed'])
            ->selectRaw('package_id, SUM(total_price) as total')
            ->groupBy('package_id')
            ->with('package') // Just need the package name
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $this->packagePerformance = $packageData->map(function($data) use ($totalRev) {
            return [
                'name' => $data->package->name ?? 'Unknown',
                'percentage' => round(($data->total / $totalRev) * 100, 1),
                'total' => $data->total
            ];
        })->toArray();
    }

    private function loadRecentTransactions()
    {
        $this->recentTransactions = Booking::with(['user', 'package'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.finance-dashboard')->layout('components.layouts.admin');
    }
}
