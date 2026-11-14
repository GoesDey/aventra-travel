<?php

namespace App\Services;

use App\Models\Booking;
use Exception;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

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

        // Log penanda sementara untuk memastikan kode terbaru dieksekusi
        Log::debug('[MIDTRANS] MidtransService::configure() dieksekusi', [
            'timestamp'           => now()->toIso8601String(),
            'is_production'       => Config::$isProduction,
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
}
