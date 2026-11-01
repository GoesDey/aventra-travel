<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\MidtransService;
use Exception;
use Illuminate\Console\Command;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransDiagnose extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'midtrans:diagnose 
                            {--booking= : Kode booking untuk pengujian payload asli}
                            {--server-key= : Override server key untuk pengujian}
                            {--client-key= : Override client key untuk pengujian}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnosa mendalam konektivitas dan konfigurasi Midtrans Snap';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('====================================================');
        $this->info('       MIDTRANS SNAP DIAGNOSTIC REPORT              ');
        $this->info('====================================================');

        // 1. PHP & cURL Extension
        $this->line('');
        $this->comment('[1] Mengecek PHP & Ekstensi cURL:');
        $phpVersion = PHP_VERSION;
        $this->line(" - PHP Version: {$phpVersion}");

        if (extension_loaded('curl')) {
            $curlVer = curl_version();
            $this->info(" - Ekstensi cURL: OK (Versi {$curlVer['version']}, SSL: {$curlVer['ssl_version']})");
        } else {
            $this->error(" - Ekstensi cURL: TIDAK AKTIF! Aktifkan extension=curl di php.ini");
            return Command::FAILURE;
        }

        // 2. Kredensial & Konfigurasi (Tanpa mencetak isi key)
        $this->line('');
        $this->comment('[2] Mengecek Kredensial & Konfigurasi:');
        
        $serverKey = $this->option('server-key') ?: config('midtrans.server_key');
        $clientKey = $this->option('client-key') ?: config('midtrans.client_key');
        $isProduction = (bool) config('midtrans.is_production', false);

        Config::$serverKey = $serverKey;
        Config::$clientKey = $clientKey;
        Config::$isProduction = $isProduction;
        Config::$isSanitized = (bool) config('midtrans.is_sanitized', true);
        Config::$is3ds = (bool) config('midtrans.is_3ds', true);
        Config::$curlOptions = [];

        $this->line(' - Mode midtrans.is_production: ' . ($isProduction ? 'PRODUCTION (true)' : 'SANDBOX (false)'));

        // Cek Server Key
        $serverKeyOk = true;
        if (empty($serverKey)) {
            $this->error(' - Server Key: KOSONG! Atur MIDTRANS_SERVER_KEY di file .env');
            $serverKeyOk = false;
        } else {
            $serverKeyLen = strlen($serverKey);
            $serverPrefix = substr($serverKey, 0, 14);
            $hasWhitespace = (bool) preg_match('/\s/', $serverKey);
            $hasQuotes = (bool) preg_match('/[\'"`]/', $serverKey);
            $hasCrLf = str_contains($serverKey, "\r") || str_contains($serverKey, "\n");
            $hasNonPrintable = (bool) preg_match('/[^\x20-\x7E]/', $serverKey);

            $this->line(" - Server Key: Panjang={$serverKeyLen} karakter, Prefix=\"{$serverPrefix}...\"");
            if ($hasWhitespace) {
                $this->error('   [PERINGATAN] Server Key mengandung spasi/karakter whitespace!');
                $serverKeyOk = false;
            }
            if ($hasCrLf) {
                $this->error('   [PERINGATAN] Server Key mengandung newline (\\r atau \\n)!');
                $serverKeyOk = false;
            }
            if ($hasNonPrintable) {
                $this->error('   [PERINGATAN] Server Key mengandung karakter non-printable / non-ASCII!');
                $serverKeyOk = false;
            }
            if ($hasQuotes) {
                $this->error('   [PERINGATAN] Server Key mengandung karakter tanda kutip (\' atau ") di .env!');
                $serverKeyOk = false;
            }

            if ($isProduction && str_starts_with($serverKey, 'SB-')) {
                $this->error('   [PERINGATAN] is_production=true tetapi server_key menggunakan prefix Sandbox (SB-)!');
                $serverKeyOk = false;
            } elseif (!$isProduction && !str_starts_with($serverKey, 'SB-')) {
                $this->error('   [PERINGATAN] is_production=false tetapi server_key TIDAK menggunakan prefix Sandbox (SB-)!');
                $serverKeyOk = false;
            }
        }

        // Cek Client Key
        $clientKeyOk = true;
        if (empty($clientKey)) {
            $this->error(' - Client Key: KOSONG! Atur MIDTRANS_CLIENT_KEY di file .env');
            $clientKeyOk = false;
        } else {
            $clientKeyLen = strlen($clientKey);
            $clientPrefix = substr($clientKey, 0, 14);
            $hasWhitespace = (bool) preg_match('/\s/', $clientKey);
            $hasQuotes = (bool) preg_match('/[\'"`]/', $clientKey);
            $hasCrLf = str_contains($clientKey, "\r") || str_contains($clientKey, "\n");
            $hasNonPrintable = (bool) preg_match('/[^\x20-\x7E]/', $clientKey);

            $this->line(" - Client Key: Panjang={$clientKeyLen} karakter, Prefix=\"{$clientPrefix}...\"");
            if ($hasWhitespace) {
                $this->error('   [PERINGATAN] Client Key mengandung spasi/karakter whitespace!');
                $clientKeyOk = false;
            }
            if ($hasCrLf) {
                $this->error('   [PERINGATAN] Client Key mengandung newline (\\r atau \\n)!');
                $clientKeyOk = false;
            }
            if ($hasNonPrintable) {
                $this->error('   [PERINGATAN] Client Key mengandung karakter non-printable / non-ASCII!');
                $clientKeyOk = false;
            }
            if ($hasQuotes) {
                $this->error('   [PERINGATAN] Client Key mengandung karakter tanda kutip (\' atau ") di .env!');
                $clientKeyOk = false;
            }

            if ($isProduction && str_starts_with($clientKey, 'SB-')) {
                $this->error('   [PERINGATAN] is_production=true tetapi client_key menggunakan prefix Sandbox (SB-)!');
                $clientKeyOk = false;
            } elseif (!$isProduction && !str_starts_with($clientKey, 'SB-')) {
                $this->error('   [PERINGATAN] is_production=false tetapi client_key TIDAK menggunakan prefix Sandbox (SB-)!');
                $clientKeyOk = false;
            }
        }

        // Cek apakah server key dan client key satu tipe (sandbox vs production)
        if ($serverKey && $clientKey) {
            $sIsSb = str_starts_with($serverKey, 'SB-');
            $cIsSb = str_starts_with($clientKey, 'SB-');
            if ($sIsSb !== $cIsSb) {
                $this->error('   [PERINGATAN] Server Key dan Client Key BUKAN pasangan yang cocok (satu Sandbox dan satu Production)!');
            } else {
                $this->info(' - Pasangan Key: Lingkungan konsisten (' . ($sIsSb ? 'Sandbox' : 'Production') . ')');
            }
        }

        // Cek status curlOptions di Midtrans\Config
        $this->line(' - Midtrans\\Config::$curlOptions saat ini: ' . (empty(Config::$curlOptions) ? 'KOSONG (default OK)' : json_encode(Config::$curlOptions)));

        // 3. Tes Konektivitas HTTP/SSL langsung ke endpoint Midtrans Snap
        $this->line('');
        $this->comment('[3] Mengecek Koneksi ke Server Midtrans Snap:');
        $endpoint = $isProduction ? 'https://app.midtrans.com/snap/v1' : 'https://app.sandbox.midtrans.com/snap/v1';
        $this->line(" - Endpoint: {$endpoint}");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $curlResult = curl_exec($ch);
        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curlErrno !== 0) {
            $this->error(" - Koneksi cURL GAGAL [errno {$curlErrno}]: {$curlError}");
            if ($curlErrno === 60 || str_contains($curlError, 'certificate')) {
                $this->error("   [PENYEBAB SSL] Sertifikat CA lokal tidak ditemukan atau tidak valid di Windows.");
                $this->line("   Solusi: Unduh cacert.pem, tambahkan curl.cainfo dan openssl.cafile di php.ini.");
            } elseif ($curlErrno === 6 || str_contains($curlError, 'resolve')) {
                $this->error("   [PENYEBAB DNS] Tidak dapat resolve host. Periksa koneksi internet / DNS.");
            } elseif ($curlErrno === 28) {
                $this->error("   [PENYEBAB TIMEOUT] Koneksi timeout. Periksa firewall/jaringan.");
            }
            return Command::FAILURE;
        } else {
            $this->info(" - Koneksi cURL & SSL Berhasil (HTTP Code {$httpCode})");
        }

        // Inisialisasi MidtransService
        $midtrans = app(MidtransService::class);

        // 4. Tes Pembuatan Snap Token dengan Payload MINIMAL
        $this->line('');
        $this->comment('[4] Menguji Snap::getSnapToken dengan Payload MINIMAL:');
        $testOrderId = 'DIAG-MIN-' . time() . '-' . rand(100, 999);
        $minimalPayload = [
            'transaction_details' => [
                'order_id'     => $testOrderId,
                'gross_amount' => 10000,
            ],
        ];

        try {
            // Pastikan MidtransService configure dipanggil
            $token = Snap::getSnapToken($minimalPayload);
            $this->info(" - BERHASIL! Snap Token minimal terbentuk: {$token}");
        } catch (\Throwable $e) {
            $this->error(" - GAGAL membuat Snap Token!");
            $this->error("   Exception Class : " . get_class($e));
            $this->error("   Exception Code  : " . $e->getCode());
            $this->error("   Exception Msg   : " . $e->getMessage());

            if (str_contains($e->getMessage(), '10023')) {
                $this->error("   [DIAGNOSA] Error 10023 terdeteksi: CURLOPT_HTTPHEADER tidak ada di Config::\$curlOptions saat count > 0!");
            }
            if (str_contains($e->getMessage(), '401') || str_contains($e->getMessage(), 'Unauthorized')) {
                $this->error("   [DIAGNOSA] HTTP 401 Unauthorized: Server Key ditolak Midtrans.");
                
                // Coba direct curl untuk memeriksa response header detail
                $snapUrl = ($isProduction ? 'https://app.midtrans.com/snap/v1' : 'https://app.sandbox.midtrans.com/snap/v1') . '/transactions';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $snapUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($minimalPayload));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Authorization: Basic ' . base64_encode(trim($serverKey) . ':'),
                ]);
                $resp = curl_exec($ch);
                $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                $headerStr = substr($resp, 0, $headerSize);
                $bodyStr = substr($resp, $headerSize);
                $this->line("   [RAW HTTP CODE]: {$httpCode}");
                $this->line("   [RAW BODY]     : {$bodyStr}");
            }

            return Command::FAILURE;
        }

        // 5. Tes dengan Payload Booking Asli
        $this->line('');
        $this->comment('[5] Menguji dengan Payload Booking Asli:');
        $bookingCode = $this->option('booking');

        $booking = null;
        if ($bookingCode) {
            $booking = Booking::where('booking_code', $bookingCode)->with(['package', 'user'])->first();
            if (!$booking) {
                $this->warn(" - Booking dengan kode '{$bookingCode}' tidak ditemukan di database.");
            }
        }

        if (!$booking) {
            $booking = Booking::where('status', 'menunggu_pembayaran')->with(['package', 'user'])->latest()->first();
            if ($booking) {
                $this->line(" - Menggunakan booking menunggu_pembayaran terbaru: {$booking->booking_code}");
            } else {
                $booking = Booking::with(['package', 'user'])->latest()->first();
                if ($booking) {
                    $this->line(" - Menggunakan booking terbaru: {$booking->booking_code} (status: {$booking->status})");
                }
            }
        }

        if (!$booking) {
            $this->warn(' - Tidak ada data booking di database untuk diuji.');
            return Command::SUCCESS;
        }

        $this->line(" - Data Booking: ID={$booking->id}, Code={$booking->booking_code}, Gross={$booking->total_price}");
        $this->line(" - User         : " . ($booking->user ? $booking->user->name . " ({$booking->user->email})" : 'NULL!'));
        $this->line(" - Package      : " . ($booking->package ? $booking->package->name . " (Price: {$booking->package->price_per_pax})" : 'NULL!'));

        // Cek kelengkapan relasi
        if (!$booking->user) {
            $this->error('   [PERINGATAN] $booking->user bernilai null!');
            return Command::FAILURE;
        }
        if (!$booking->package) {
            $this->error('   [PERINGATAN] $booking->package bernilai null!');
            return Command::FAILURE;
        }

        // Verifikasi keselarasan gross_amount dengan item_details
        $calcItemTotal = ((int) $booking->package->price_per_pax) * ((int) $booking->guest_count);
        $totalPrice = (int) $booking->total_price;
        if ($calcItemTotal !== $totalPrice) {
            $this->error("   [MISMATCH] gross_amount ({$totalPrice}) != item total ({$calcItemTotal})");
        } else {
            $this->info(" - Validasi Harga: gross_amount matches item_details sum ({$totalPrice})");
        }

        // Pengujian bertahap menambahkan field satu per satu
        $this->line('');
        $this->line(' - Menjalankan pengujian payload bertahap (incremental field test):');

        // Step 1: Base order_id (randomized so we don't duplicate utilized order_id)
        $simulatedOrderId = $booking->booking_code . '-DIAG-' . rand(10, 99);
        $testPayload = [
            'transaction_details' => [
                'order_id'     => $simulatedOrderId,
                'gross_amount' => $totalPrice,
            ],
        ];

        try {
            $t1 = Snap::getSnapToken($testPayload);
            $this->info("   [Step 1 - transaction_details] OK (Token: {$t1})");
        } catch (\Throwable $e) {
            $this->error("   [Step 1 GAGAL] " . $e->getMessage());
            return Command::FAILURE;
        }

        // Step 2: Customer Details
        $testPayload['customer_details'] = [
            'first_name' => $booking->user->name,
            'email'      => $booking->user->email,
        ];
        $testPayload['transaction_details']['order_id'] = $booking->booking_code . '-DIAG-' . rand(10, 99);

        try {
            $t2 = Snap::getSnapToken($testPayload);
            $this->info("   [Step 2 - customer_details] OK (Token: {$t2})");
        } catch (\Throwable $e) {
            $this->error("   [Step 2 GAGAL] Field customer_details ditolak: " . $e->getMessage());
            return Command::FAILURE;
        }

        // Step 3: Item Details
        $testPayload['item_details'] = [
            [
                'id'       => (string) $booking->package_id,
                'price'    => (int) $booking->package->price_per_pax,
                'quantity' => (int) $booking->guest_count,
                'name'     => mb_substr($booking->package->name, 0, 50),
            ]
        ];
        $testPayload['transaction_details']['order_id'] = $booking->booking_code . '-DIAG-' . rand(10, 99);

        try {
            $t3 = Snap::getSnapToken($testPayload);
            $this->info("   [Step 3 - item_details] OK (Token: {$t3})");
        } catch (\Throwable $e) {
            $this->error("   [Step 3 GAGAL] Field item_details ditolak: " . $e->getMessage());
            return Command::FAILURE;
        }

        // Step 4: Expiry Details
        $durationMinutes = config('midtrans.expiry_duration', 60);
        if ($booking->expires_at) {
            $diff = (int) now()->diffInMinutes($booking->expires_at, false);
            $durationMinutes = max(1, $diff);
        }
        $testPayload['expiry'] = [
            'start_time' => now()->format('Y-m-d H:i:s O'),
            'unit'       => 'minute',
            'duration'   => $durationMinutes,
        ];
        $testPayload['transaction_details']['order_id'] = $booking->booking_code . '-DIAG-' . rand(10, 99);

        try {
            $t4 = Snap::getSnapToken($testPayload);
            $this->info("   [Step 4 - expiry] OK (Token: {$t4})");
        } catch (\Throwable $e) {
            $this->error("   [Step 4 GAGAL] Field expiry ditolak: " . $e->getMessage());
            return Command::FAILURE;
        }

        $this->line('');
        $this->info('====================================================');
        $this->info('       SEMUA TES DIAGNOSTIK MIDTRANS SELESAI        ');
        $this->info('====================================================');

        return Command::SUCCESS;
    }
}
