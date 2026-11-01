<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PackageSchedule;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    /**
     * Menangani callback webhook notifikasi pembayaran dari Midtrans.
     * Menggunakan state machine yang aman, bebas race-condition, dan idempoten.
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        $orderId           = $payload['order_id'] ?? null;
        $statusCode        = $payload['status_code'] ?? null;
        $grossAmountRaw    = $payload['gross_amount'] ?? null;
        $signatureKey      = $payload['signature_key'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus       = $payload['fraud_status'] ?? null;
        $paymentType       = $payload['payment_type'] ?? null;
        $transactionId     = $payload['transaction_id'] ?? null;

        // 1. Verifikasi Signature Midtrans: hash('sha512', order_id + status_code + gross_amount + server_key)
        $serverKey = config('midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmountRaw . $serverKey);

        if (!hash_equals($expectedSignature, (string) $signatureKey)) {
            Log::warning('Midtrans Webhook: Signature tidak valid.', [
                'order_id'           => $orderId,
                'received_signature' => $signatureKey,
            ]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // 2. Cari record payment dan booking berdasarkan order_id
        $payment = Payment::where('order_id', $orderId)->first();
        if (!$payment) {
            Log::error("Midtrans Webhook: Payment dengan order_id {$orderId} tidak ditemukan.");
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $bookingInfo = Booking::where('id', $payment->booking_id)->first();
        if (!$bookingInfo) {
            Log::error("Midtrans Webhook: Booking untuk payment {$orderId} tidak ditemukan.");
            return response()->json(['message' => 'Booking not found'], 404);
        }

        // 3. Eksekusi dalam DB::transaction dengan lock berurutan: Schedule -> Booking -> Payment
        $result = DB::transaction(function () use (
            $payment,
            $bookingInfo,
            $grossAmountRaw,
            $transactionStatus,
            $fraudStatus,
            $paymentType,
            $transactionId,
            $payload
        ) {
            // Urutan Lock 1: Schedule
            $lockedSchedule = PackageSchedule::where('id', $bookingInfo->package_schedule_id)
                ->lockForUpdate()
                ->first();

            // Urutan Lock 2: Booking
            $lockedBooking = Booking::where('id', $bookingInfo->id)
                ->lockForUpdate()
                ->first();

            // Urutan Lock 3: Payment
            $lockedPayment = Payment::where('id', $payment->id)
                ->lockForUpdate()
                ->first();

            // 4. Cocokkan gross_amount dengan payments.amount
            $receivedAmount = (int) round((float) $grossAmountRaw);
            if ($receivedAmount !== (int) $lockedPayment->amount) {
                Log::error('Midtrans Webhook: Mismatch gross_amount.', [
                    'order_id'        => $lockedPayment->order_id,
                    'expected_amount' => $lockedPayment->amount,
                    'received_amount' => $receivedAmount,
                ]);
                return response()->json(['message' => 'Gross amount mismatch'], 400);
            }

            // 5. State Machine & Idempotensi
            // Aturan 1: Status success bersifat final dan tidak boleh berubah lagi
            if ($lockedPayment->payment_status === 'success') {
                return response()->json(['message' => 'Payment already settled previously (idempotent)'], 200);
            }

            // Simpan payment_type saat notifikasi pending (misal: user memilih VA / QRIS)
            if ($transactionStatus === 'pending') {
                $lockedPayment->update([
                    'payment_type'     => $paymentType,
                    'transaction_id'   => $transactionId,
                    'raw_notification' => $payload,
                ]);

                return response()->json([
                    'message'      => 'Pending payment recorded',
                    'payment_type' => $paymentType,
                ], 200);
            }

            $isSuccess = ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraudStatus === 'accept'));
            $isFailed  = in_array($transactionStatus, ['deny', 'cancel', 'failure'], true);
            $isExpired = ($transactionStatus === 'expire');

            // 6. Penanganan Status SUCCESS (Settlement / Capture Accept)
            if ($isSuccess) {
                // Kasus Tepi: Settlement datang untuk booking yang sudah expired di sistem kita
                if (in_array($lockedBooking->status, ['kedaluwarsa', 'dibatalkan'], true) || $lockedPayment->payment_status === 'expired') {
                    if ($lockedSchedule && $lockedSchedule->quota >= $lockedBooking->guest_count) {
                        // Kuota masih cukup: kurangi kuota lagi dan aktifkan kembali booking
                        $lockedSchedule->decrement('quota', $lockedBooking->guest_count);
                        $lockedBooking->update(['status' => 'dibayar']);

                        $lockedPayment->update([
                            'payment_status'   => 'success',
                            'paid_at'          => now(),
                            'payment_type'     => $paymentType,
                            'transaction_id'   => $transactionId,
                            'raw_notification' => $payload,
                        ]);

                        Log::info("Midtrans Webhook: Settlement untuk booking kedaluwarsa {$lockedBooking->booking_code} berhasil diaktifkan kembali.");
                    } else {
                        // Kuota sudah habis: tandai booking sebagai 'perlu_refund' untuk ditangani admin
                        $lockedBooking->update(['status' => 'perlu_refund']);

                        $lockedPayment->update([
                            'payment_status'   => 'success',
                            'paid_at'          => now(),
                            'payment_type'     => $paymentType,
                            'transaction_id'   => $transactionId,
                            'raw_notification' => $payload,
                        ]);

                        Log::warning("Midtrans Webhook: Settlement untuk booking kedaluwarsa {$lockedBooking->booking_code} diterima namun kuota penuh. Ditandai perlu_refund.");
                    }
                } else {
                    // Transisi normal: pending -> success
                    $lockedBooking->update(['status' => 'dibayar']);

                    $lockedPayment->update([
                        'payment_status'   => 'success',
                        'paid_at'          => now(),
                        'payment_type'     => $paymentType,
                        'transaction_id'   => $transactionId,
                        'raw_notification' => $payload,
                    ]);
                }

                // Pekerjaan berat seperti pengiriman email diletakkan di afterCommit
                DB::afterCommit(function () use ($lockedBooking) {
                    // event(new BookingPaid($lockedBooking));
                });

                return response()->json(['message' => 'Payment settled successfully'], 200);
            }

            // 7. Penanganan Status FAILED / EXPIRED
            if ($isFailed || $isExpired) {
                $targetPaymentStatus = $isExpired ? 'expired' : 'failed';
                $targetBookingStatus = $isExpired ? 'kedaluwarsa' : 'dibatalkan';

                // Idempotensi: Hanya kembalikan kuota jika transisi berasal dari 'pending'
                if ($lockedPayment->payment_status === 'pending') {
                    if ($lockedSchedule) {
                        $lockedSchedule->increment('quota', $lockedBooking->guest_count);
                    }

                    $lockedBooking->update(['status' => $targetBookingStatus]);

                    $lockedPayment->update([
                        'payment_status'   => $targetPaymentStatus,
                        'payment_type'     => $paymentType,
                        'transaction_id'   => $transactionId,
                        'raw_notification' => $payload,
                    ]);

                    return response()->json(['message' => "Payment marked as {$targetPaymentStatus} and quota restored"], 200);
                }

                // Jika sudah pernah expired/failed sebelumnya, kembalikan 200 tanpa efek samping
                return response()->json(['message' => "Payment already in final state: {$lockedPayment->payment_status}"], 200);
            }

            // Status lainnya
            $lockedPayment->update([
                'payment_type'     => $paymentType,
                'transaction_id'   => $transactionId,
                'raw_notification' => $payload,
            ]);

            return response()->json(['message' => 'Notification recorded'], 200);
        }, 3);

        return $result;
    }
}
