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
    public $activeBookings = 0;
    public $avgBookingValue = 0;
    public $refunds = 0;

    public $monthlyRevenue = [];
    public $packagePerformance = [];
    public $recentTransactions = [];

    public function mount()
    {
        $this->loadMetrics();
        $this->loadChartData();
        $this->loadRecentTransactions();
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
    }

    private function loadChartData()
    {
        // Monthly Revenue Trend for current year
        $currentYear = Carbon::now()->year;
        
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
