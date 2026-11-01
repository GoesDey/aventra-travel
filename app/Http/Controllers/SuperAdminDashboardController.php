<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Booking;
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

        // 1. Total Pendapatan Kotor Seluruh Transaksi Sah
        $totalGrossRevenue = (int) Booking::whereIn('status', $validStatuses)->sum('total_price');

        // 2. Total Komisi Bersih Platform TriHitaTour
        $totalPlatformCommission = (int) Booking::whereIn('status', $validStatuses)->sum('commission_amount');

        // 3. Total Hak Pendapatan Seluruh Mitra Agen
        $totalAgentEarnings = (int) Booking::whereIn('status', $validStatuses)->sum('agent_earning');

        // 4. Total Volume Transaksi Sukses
        $totalSuccessfulBookings = Booking::whereIn('status', $validStatuses)->count();

        // 5. Jumlah Mitra Agen Aktif
        $activeAgentsCount = Agent::whereHas('user', function ($query) {
            $query->where('status', 'active');
        })->count();

        $totalAgentsCount = Agent::count();

        // 6. Kueri Top 5 Agen dengan Total Transaksi / Omset Tertinggi
        $topAgents = Agent::with('user')
            ->withSum(['bookings as total_turnover' => function ($query) use ($validStatuses) {
                $query->whereIn('status', $validStatuses);
            }], 'total_price')
            ->withSum(['bookings as total_commission' => function ($query) use ($validStatuses) {
                $query->whereIn('status', $validStatuses);
            }], 'commission_amount')
            ->withSum(['bookings as total_agent_earning' => function ($query) use ($validStatuses) {
                $query->whereIn('status', $validStatuses);
            }], 'agent_earning')
            ->withCount(['bookings as successful_bookings_count' => function ($query) use ($validStatuses) {
                $query->whereIn('status', $validStatuses);
            }])
            ->orderByDesc('total_turnover')
            ->take(5)
            ->get();

        // 7. Riwayat Transaksi Sukses Terbaru Lintas Agen (Ringkasan Cepat)
        $recentTransactions = Booking::with(['user', 'package.agent'])
            ->whereIn('status', $validStatuses)
            ->latest()
            ->take(6)
            ->get();

        return view('super-admin.dashboard', compact(
            'totalGrossRevenue',
            'totalPlatformCommission',
            'totalAgentEarnings',
            'totalSuccessfulBookings',
            'activeAgentsCount',
            'totalAgentsCount',
            'topAgents',
            'recentTransactions'
        ));
    }
}
