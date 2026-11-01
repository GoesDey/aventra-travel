<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Menampilkan halaman pembayaran Midtrans Snap untuk booking tertentu.
     */
    public function show(string $booking_code, MidtransService $midtrans)
    {
        $booking = Booking::where('booking_code', $booking_code)
            ->with(['package', 'schedule'])
            ->firstOrFail();

        // 1. Otorisasi: Pastikan booking milik user yang sedang login
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak. Pemesanan ini bukan milik akun Anda.');
        }

        // 2. Ambil atau buat record Payment dengan lock cepat, panggilan API Snap di luar lock
        try {
            $payment = DB::transaction(function () use ($booking) {
                $lockedBooking = Booking::where('id', $booking->id)->lockForUpdate()->first();

                // Cek status dan masa kedaluwarsa di dalam lock
                if (!$lockedBooking || $lockedBooking->status !== 'menunggu_pembayaran' || $lockedBooking->isExpired()) {
                    return null;
                }

                return Payment::firstOrCreate(
                    ['booking_id' => $lockedBooking->id],
                    [
                        'order_id'       => $lockedBooking->booking_code,
                        'amount'         => (int) $lockedBooking->total_price,
                        'payment_status' => 'pending',
                    ]
                );
            }, 3);

            if ($payment && $payment->payment_status === 'pending') {
                // Jika snap_token sudah ada, PAKAI ULANG (mencegah HTTP 406/409 duplicate order_id)
                if (empty($payment->snap_token)) {
                    $snapToken = $midtrans->createSnapToken($booking);
                    $payment->update([
                        'snap_token' => $snapToken,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal membuat Snap Token Midtrans: ' . $e->getMessage(), [
                'exception_class' => get_class($e),
                'code'            => $e->getCode(),
                'message'         => $e->getMessage(),
                'booking_code'    => $booking_code,
                'trace'           => \Illuminate\Support\Str::limit($e->getTraceAsString(), 500),
            ]);

            return redirect()->route('history')
                ->with('error', 'Gagal memproses pembayaran melalui Midtrans. Silakan coba beberapa saat lagi.');
        }

        if (!$payment) {
            $latest = Booking::where('id', $booking->id)->first();
            if ($latest?->status === 'dibayar') {
                return redirect()->route('history')
                    ->with('success', 'Pemesanan ini sudah berhasil dibayar!');
            }

            if ($latest?->isExpired() || in_array($latest?->status, ['kedaluwarsa', 'dibatalkan'])) {
                return redirect()->route('history')
                    ->with('error', 'Batas waktu pembayaran untuk pemesanan ini telah kedaluwarsa.');
            }

            return redirect()->route('history')
                ->with('error', "Status pemesanan sudah berubah ({$latest?->status}).");
        }

        $clientKey = config('midtrans.client_key');
        $snapUrl   = config('midtrans.snap_url');

        return view('payments.show', compact('booking', 'payment', 'clientKey', 'snapUrl'));
    }

    /**
     * Endpoint polling ringan untuk memeriksa status pembayaran booking secara real-time.
     */
    public function checkStatus(string $booking_code)
    {
        $booking = Booking::where('booking_code', $booking_code)
            ->with('payment')
            ->firstOrFail();

        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        return response()->json([
            'booking_code'   => $booking->booking_code,
            'booking_status' => $booking->status,
            'payment_status' => $booking->payment?->payment_status ?? 'pending',
            'is_paid'        => $booking->status === 'dibayar',
            'is_expired'     => $booking->isExpired() || in_array($booking->status, ['kedaluwarsa', 'dibatalkan']),
        ]);
    }
}
