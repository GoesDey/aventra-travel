<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    /**
     * Menangani callback webhook notifikasi pembayaran dari Midtrans.
     * Verifikasi signature dilakukan di sini, lalu state machine dijalankan lewat MidtransService.
     */
    public function handle(Request $request, MidtransService $midtrans)
    {
        $payload = $request->all();

        $orderId        = $payload['order_id'] ?? null;
        $statusCode     = $payload['status_code'] ?? null;
        $grossAmountRaw = $payload['gross_amount'] ?? null;
        $signatureKey   = $payload['signature_key'] ?? null;

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

        // 2. Cari record payment berdasarkan order_id
        $payment = Payment::where('order_id', $orderId)->first();
        if (!$payment) {
            Log::error("Midtrans Webhook: Payment dengan order_id {$orderId} tidak ditemukan.");
            return response()->json(['message' => 'Payment not found'], 404);
        }

        // 3. Proses transisi status menggunakan logika bersama di MidtransService
        $result = $midtrans->processTransactionStatus($payment, $payload);

        $body = array_merge(['message' => $result['message']], $result['extra'] ?? []);

        return response()->json($body, $result['http_status']);
    }
}
