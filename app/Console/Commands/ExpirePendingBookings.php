<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\PackageSchedule;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpirePendingBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:expire-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membatalkan booking menunggu_pembayaran yang telah melewati batas waktu expires_at dan mengembalikan kuota';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredCandidates = Booking::where('status', 'menunggu_pembayaran')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get(['id', 'package_schedule_id']);

        $processedCount = 0;

        foreach ($expiredCandidates as $candidate) {
            DB::transaction(function () use ($candidate, &$processedCount) {
                // Urutan Lock 1: Schedule
                $schedule = PackageSchedule::where('id', $candidate->package_schedule_id)
                    ->lockForUpdate()
                    ->first();

                // Urutan Lock 2: Booking
                $booking = Booking::where('id', $candidate->id)
                    ->lockForUpdate()
                    ->first();

                // CEK ULANG status di dalam lock (karena webhook bisa saja baru saja masuk dan mengubah status ke dibayar)
                if (!$booking || $booking->status !== 'menunggu_pembayaran') {
                    return;
                }

                // Cek ulang apakah benar-benar sudah expired
                if (!$booking->expires_at || $booking->expires_at->isFuture()) {
                    return;
                }

                // Kembalikan kuota ke jadwal perjalanan terkait
                if ($schedule) {
                    $schedule->increment('quota', $booking->guest_count);
                }

                // Ubah status booking ke 'kedaluwarsa'
                $booking->update([
                    'status' => 'kedaluwarsa',
                ]);

                // Urutan Lock 3: Payment
                $payment = Payment::where('booking_id', $booking->id)
                    ->lockForUpdate()
                    ->first();

                if ($payment && $payment->payment_status === 'pending') {
                    $payment->update([
                        'payment_status' => 'expired',
                    ]);
                }

                $processedCount++;

                Log::info("Auto-Expire: Booking {$booking->booking_code} dibatalkan karena kedaluwarsa. Kuota ({$booking->guest_count}) dikembalikan.");
            }, 3);
        }

        $this->info("Selesai memproses {$processedCount} booking kedaluwarsa.");
        return Command::SUCCESS;
    }
}
