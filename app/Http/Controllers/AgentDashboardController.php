<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AgentDashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Keuangan & Monitoring Pesanan Agen (Terisolasi Per Agen).
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $agent = $user->agent;

        // Keamanan: Pastikan profil agen tersedia
        if (!$agent) {
            abort(403, 'Akses ditolak. Akun Anda belum memiliki profil agen yang aktif.');
        }

        // Definisi status transaksi
        $validStatuses = ['dibayar', 'berjalan', 'selesai'];

        // 1. Total Pendapatan Bersih (agent_earning) HANYA untuk booking paket milik agen ini
        $total_earning = (int) $agent->bookings()
            ->whereIn('status', $validStatuses)
            ->sum('agent_earning');

        // 2. Jumlah Pesanan Aktif (status: menunggu_pembayaran atau dibayar)
        $active_bookings_count = $agent->bookings()
            ->whereIn('status', ['menunggu_pembayaran', 'dibayar'])
            ->count();

        // 3. 10 Pesanan Terbaru milik agen ini beserta relasi package, schedule, dan user
        $recent_bookings = $agent->bookings()
            ->with(['package', 'schedule', 'user'])
            ->latest('bookings.created_at')
            ->take(10)
            ->get();

        // Aliases untuk kompatibilitas data view & test
        $totalEarnings = $total_earning;
        $activeOrdersCount = $active_bookings_count;
        $recentBookings = $recent_bookings;
        $successfulOrdersCount = $agent->bookings()->whereIn('status', $validStatuses)->count();
        $completedOrdersCount = $agent->bookings()->where('status', 'selesai')->count();
        $totalGrossSales = (int) $agent->bookings()->whereIn('status', $validStatuses)->sum('total_price');

        return view('agent.dashboard', compact(
            'agent',
            'total_earning',
            'active_bookings_count',
            'recent_bookings',
            'totalEarnings',
            'activeOrdersCount',
            'recentBookings',
            'successfulOrdersCount',
            'completedOrdersCount',
            'totalGrossSales'
        ));
    }
}
