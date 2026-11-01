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
        $validStatuses  = ['dibayar', 'berjalan', 'selesai'];
        $activeStatuses = ['dibayar', 'berjalan'];

        // 1. Total Pendapatan Bersih (agent_earning) HANYA untuk agen yang sedang login
        $totalEarnings = (int) $agent->bookings()
            ->whereIn('status', $validStatuses)
            ->sum('agent_earning');

        // 2. Total Pesanan Sukses (Lunas/Dijalankan/Selesai)
        $successfulOrdersCount = $agent->bookings()
            ->whereIn('status', $validStatuses)
            ->count();

        // 3. Jumlah Pesanan Aktif (Perlu Dijalankan / Sedang Berlangsung)
        $activeOrdersCount = $agent->bookings()
            ->whereIn('status', $activeStatuses)
            ->count();

        // 4. Jumlah Pesanan Selesai
        $completedOrdersCount = $agent->bookings()
            ->where('status', 'selesai')
            ->count();

        // 5. Total Penjualan Kotor (Gross Sales) Paket Agen
        $totalGrossSales = (int) $agent->bookings()
            ->whereIn('status', $validStatuses)
            ->sum('total_price');

        // 6. Kueri Tabel Pesanan Terbaru (Data 100% terisolasi pada paket milik agen ini)
        $recentBookings = $agent->bookings()
            ->with(['package', 'schedule', 'user'])
            ->latest('bookings.created_at')
            ->paginate(10)
            ->withQueryString();

        return view('agent.dashboard', compact(
            'agent',
            'totalEarnings',
            'successfulOrdersCount',
            'activeOrdersCount',
            'completedOrdersCount',
            'totalGrossSales',
            'recentBookings'
        ));
    }
}
