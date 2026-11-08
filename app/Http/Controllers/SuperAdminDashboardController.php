<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminDashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Keuangan & Monitoring Eksekutif Super Admin.
     */
    public function index(Request $request)
    {
        // Status booking yang diakui sebagai transaksi sah/keuangan riil
        $validStatuses = ['dibayar', 'berjalan', 'selesai'];

        // 1. Total Komisi Bersih Platform
        $total_commission = (int) Booking::whereIn('status', $validStatuses)->sum('commission_amount');

        // 2. Total Volume Transaksi Sukses
        $total_successful_transactions = Booking::whereIn('status', $validStatuses)->count();

        // 3. Jumlah Mitra Agen Aktif
        $total_active_agents = User::where('role', 'agent')->where('status', 'active')->count();

        // 4. Kueri Top 5 Agen berdasarkan kontribusi pendapatan/komisi
        $top_agents = Agent::with('user')
            ->withCount(['bookings as total_transactions' => function ($query) use ($validStatuses) {
                $query->whereIn('status', $validStatuses);
            }])
            ->withSum(['bookings as total_commission' => function ($query) use ($validStatuses) {
                $query->whereIn('status', $validStatuses);
            }], 'commission_amount')
            ->withSum(['bookings as total_turnover' => function ($query) use ($validStatuses) {
                $query->whereIn('status', $validStatuses);
            }], 'total_price')
            ->withSum(['bookings as total_agent_earning' => function ($query) use ($validStatuses) {
                $query->whereIn('status', $validStatuses);
            }], 'agent_earning')
            ->orderByDesc('total_commission')
            ->take(5)
            ->get();

        // 5. Riwayat Transaksi Sukses Terbaru Lintas Agen (Ringkasan Cepat)
        $recentTransactions = Booking::with(['user', 'package.agent'])
            ->whereIn('status', $validStatuses)
            ->latest()
            ->take(6)
            ->get();

        // Aliases untuk kompatibilitas data view & test
        $totalPlatformCommission = $total_commission;
        $totalGrossRevenue = (int) Booking::whereIn('status', $validStatuses)->sum('total_price');
        $totalAgentEarnings = (int) Booking::whereIn('status', $validStatuses)->sum('agent_earning');
        $totalSuccessfulBookings = $total_successful_transactions;
        $activeAgentsCount = $total_active_agents;
        $totalAgentsCount = Agent::count();
        $topAgents = $top_agents;

        return view('super-admin.dashboard', compact(
            'total_commission',
            'total_successful_transactions',
            'total_active_agents',
            'top_agents',
            'totalPlatformCommission',
            'totalGrossRevenue',
            'totalAgentEarnings',
            'totalSuccessfulBookings',
            'activeAgentsCount',
            'totalAgentsCount',
            'topAgents',
            'recentTransactions'
        ));
    }
}
