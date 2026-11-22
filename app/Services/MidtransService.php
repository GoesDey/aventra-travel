<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\PackageSchedule;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        $this->configure();
    }

    /**
     * Set konfigurasi Midtrans dari file config.
     */
    protected function configure(): void
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$clientKey    = config('midtrans.client_key');
        Config::$isProduction = (bool) config('midtrans.is_production', false);
        Config::$isSanitized  = (bool) config('midtrans.is_sanitized', true);
        Config::$is3ds        = (bool) config('midtrans.is_3ds', true);
        // Biarkan Config::$curlOptions default (array kosong) agar tidak memicu bug 10023 / kehilangan header di ApiRequestor
        Config::$curlOptions  = [];

        // Override Notification URL per transaksi jika diisi di .env (misal URL ngrok)
        $overrideNotifUrl = trim((string) config('midtrans.notification_url', ''));
        if ($overrideNotifUrl !== '') {
            Config::$overrideNotifUrl = $overrideNotifUrl;
        }

        // Log penanda sementara untuk memastikan kode terbaru dieksekusi
        Log::debug('[MIDTRANS] MidtransService::configure() dieksekusi', [
            'timestamp'           => now()->toIso8601String(),
            'is_production'       => Config::$isProduction,
            'override_notif_url'  => $overrideNotifUrl !== '' ? $overrideNotifUrl : null,
            'curl_options_count'  => count(Config::$curlOptions),
        ]);
    }

    /**
     * Generate Midtrans Snap Token untuk sebuah Booking.
     */
    public function createSnapToken(Booking $booking): string
    {
        $this->configure();

        $grossAmount = (int) $booking->total_price;

        // Hitung sisa menit kedaluwarsa sesuai expires_at booking
        $durationMinutes = config('midtrans.expiry_duration', 60);
        if ($booking->expires_at) {
            $diff = (int) now()->diffInMinutes($booking->expires_at, false);
            $durationMinutes = max(1, $diff);
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $booking->booking_code,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $booking->user?->name ?? 'Traveler',
                'email'      => $booking->user?->email ?? 'traveler@example.com',
            ],
            'item_details' => (function () use ($booking, $grossAmount) {
                $items = [
                    [
                        'id'       => (string) ($booking->package_id ?? 'pkg-' . $booking->id),
                        'price'    => (int) ($booking->package?->price_per_pax ?? (int) $booking->total_price),
                        'quantity' => (int) ($booking->guest_count ?? 1),
                        'name'     => mb_substr($booking->package?->name ?? 'Tour Package', 0, 50),
                    ]
                ];

                // Hanya sertakan overtime item jika booking bertipe custom dan memiliki overtime_charge
                if (!empty($booking->is_custom) && !empty($booking->overtime_charge) && (int) $booking->overtime_charge > 0) {
                    $items[] = [
                        'id'       => 'overtime-charge',
                        'price'    => (int) $booking->overtime_charge,
                        'quantity' => 1,
                        'name'     => 'Custom Overtime (' . (int) ($booking->overtime_hours ?? 0) . ' hrs)',
                    ];
                }

                // Midtrans strictly requires sum(price * quantity) === gross_amount
                $sum = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
                if ($sum !== $grossAmount) {
                    return [
                        [
                            'id'       => (string) ($booking->package_id ?? 'pkg-' . $booking->id),
                            'price'    => $grossAmount,
                            'quantity' => 1,
                            'name'     => mb_substr($booking->package?->name ?? 'Tour Package', 0, 50),
                        ]
                    ];
                }

                return $items;
            })(),
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit'       => 'minute',
                'duration'   => $durationMinutes,
            ],
        ];

        // CATATAN: Jangan kirim enabled_payments agar semua metode aktif (QRIS, VA, e-Wallet)

        try {
            return Snap::getSnapToken($params);
        } catch (\Throwable $e) {
            Log::error('Gagal generate Midtrans Snap Token: ' . $e->getMessage(), [
                'exception_class' => get_class($e),
                'code'            => $e->getCode(),
                'message'         => $e->getMessage(),
                'booking_id'      => $booking->id,
                'booking_code'    => $booking->booking_code,
                'params'          => $params,
                'trace'           => \Illuminate\Support\Str::limit($e->getTraceAsString(), 500),
            ]);
            throw $e;
        }
    }

    /**
     * Memproses payload status transaksi Midtrans (dipakai bersama oleh Webhook & Status API Sync).
     * Menggunakan state machine yang aman, bebas race-condition, dan idempoten.
     *
     * @param  array<string, mixed>  $payload
     * @return array{http_status: int, message: string, extra?: array<string, mixed>}
     */
    public function processTransactionStatus(Payment $payment, array $payload): array
    {
        $grossAmountRaw    = $payload['gross_amount'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus       = $payload['fraud_status'] ?? null;
        $paymentType       = $payload['payment_type'] ?? null;
        $transactionId     = $payload['transaction_id'] ?? null;

        $bookingInfo = Booking::where('id', $payment->booking_id)->first();
        if (!$bookingInfo) {
            Log::error("Midtrans Webhook: Booking untuk payment {$payment->order_id} tidak ditemukan.");
            return ['http_status' => 404, 'message' => 'Booking not found'];
        }

        // Eksekusi dalam DB::transaction dengan lock berurutan: Schedule -> Booking -> Payment
        return DB::transaction(function () use (
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
            $lockedSchedule = $bookingInfo->package_schedule_id
                ? PackageSchedule::where('id', $bookingInfo->package_schedule_id)->lockForUpdate()->first()
                : null;

            // Urutan Lock 2: Booking
            $lockedBooking = Booking::where('id', $bookingInfo->id)
                ->lockForUpdate()
                ->first();

            // Urutan Lock 3: Payment
            $lockedPayment = Payment::where('id', $payment->id)
                ->lockForUpdate()
                ->first();

            // Cocokkan gross_amount dengan payments.amount
            $receivedAmount = (int) round((float) $grossAmountRaw);
            if ($receivedAmount !== (int) $lockedPayment->amount) {
                Log::error('Midtrans Webhook: Mismatch gross_amount.', [
                    'order_id'        => $lockedPayment->order_id,
                    'expected_amount' => $lockedPayment->amount,
                    'received_amount' => $receivedAmount,
                ]);
                return ['http_status' => 400, 'message' => 'Gross amount mismatch'];
            }

            // State Machine & Idempotensi
            // Aturan 1: Status success bersifat final dan tidak boleh berubah lagi
            if ($lockedPayment->payment_status === 'success') {
                return ['http_status' => 200, 'message' => 'Payment already settled previously (idempotent)'];
            }

            // Simpan payment_type saat notifikasi pending (misal: user memilih VA / QRIS)
            if ($transactionStatus === 'pending') {
                $lockedPayment->update([
                    'payment_type'     => $paymentType,
                    'transaction_id'   => $transactionId,
                    'raw_notification' => $payload,
                ]);

                return [
                    'http_status' => 200,
                    'message'     => 'Pending payment recorded',
                    'extra'       => ['payment_type' => $paymentType],
                ];
            }

            $isSuccess = ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraudStatus === 'accept'));
            $isFailed  = in_array($transactionStatus, ['deny', 'cancel', 'failure'], true);
            $isExpired = ($transactionStatus === 'expire');

            // Penanganan Status SUCCESS (Settlement / Capture Accept)
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

                return ['http_status' => 200, 'message' => 'Payment settled successfully'];
            }

            // Penanganan Status FAILED / EXPIRED
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

                    return ['http_status' => 200, 'message' => "Payment marked as {$targetPaymentStatus} and quota restored"];
                }

                // Jika sudah pernah expired/failed sebelumnya, kembalikan 200 tanpa efek samping
                return ['http_status' => 200, 'message' => "Payment already in final state: {$lockedPayment->payment_status}"];
            }

            // Status lainnya
            $lockedPayment->update([
                'payment_type'     => $paymentType,
                'transaction_id'   => $transactionId,
                'raw_notification' => $payload,
            ]);

            return ['http_status' => 200, 'message' => 'Notification recorded'];
        }, 3);
    }

    /**
     * Memeriksa status transaksi langsung ke Midtrans Status API (\Midtrans\Transaction::status)
     * lalu memproses hasilnya dengan state machine yang sama seperti webhook.
     *
     * @return array{http_status: int, message: string, booking: Booking, transaction_status?: ?string}
     */
    public function syncTransactionStatus(Booking $booking): array
    {
        $this->configure();

        $payment = Payment::firstOrCreate(
            ['booking_id' => $booking->id],
            [
                'order_id'       => $booking->booking_code,
                'amount'         => (int) $booking->total_price,
                'payment_status' => 'pending',
            ]
        );

        $statusResponse = Transaction::status($booking->booking_code);
        $payload = json_decode(json_encode($statusResponse), true) ?: [];

        $result = $this->processTransactionStatus($payment, $payload);
        $booking->refresh();

        return array_merge($result, [
            'booking'            => $booking,
            'transaction_status' => $payload['transaction_status'] ?? null,
        ]);
    }
}
