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
            ->with(['package', 'schedule', 'user'])
            ->firstOrFail();

        // 1. Otorisasi: Pastikan booking milik user yang sedang login
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Access denied. This booking does not belong to your account.');
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
            \Illuminate\Support\Facades\Log::error('Failed to create Midtrans Snap Token: ' . $e->getMessage(), [
                'exception_class' => get_class($e),
                'code'            => $e->getCode(),
                'message'         => $e->getMessage(),
                'booking_code'    => $booking_code,
                'trace'           => \Illuminate\Support\Str::limit($e->getTraceAsString(), 500),
            ]);

            return redirect()->route('history')
                ->with('error', 'Failed to process payment via Midtrans. Please try again shortly.');
        }

        if (!$payment) {
            $latest = Booking::where('id', $booking->id)->first();
            if ($latest?->status === 'dibayar') {
                return redirect()->route('history')
                    ->with('success', 'This booking has already been paid successfully!');
            }

            if ($latest?->isExpired() || in_array($latest?->status, ['kedaluwarsa', 'dibatalkan'])) {
                return redirect()->route('history')
                    ->with('error', 'The payment time limit for this booking has expired.');
            }

            return redirect()->route('history')
                ->with('error', "Booking status has changed ({$latest?->status}).");
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
