<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncTripStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:sync-trip-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically transitions paid bookings to ongoing on start date and ongoing bookings to completed after end date.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $timezone = config('app.timezone', 'Asia/Makassar');
        $now = now($timezone);

        $startedCount = 0;
        $completedCount = 0;

        // 1. Paid (dibayar) -> Ongoing (berjalan) saat tanggal mulai tiba
        $paidBookings = Booking::with(['schedule', 'package'])
            ->where('status', 'dibayar')
            ->get();

        foreach ($paidBookings as $candidate) {
            $startAt = $this->resolveStartDateTime($candidate, $timezone);
            if (!$startAt || $now->lt($startAt)) {
                continue;
            }

            DB::transaction(function () use ($candidate, &$startedCount) {
                $booking = Booking::where('id', $candidate->id)->lockForUpdate()->first();
                if (!$booking || $booking->status !== 'dibayar') {
                    return;
                }

                $booking->update(['status' => 'berjalan']);
                $startedCount++;

                Log::info("Auto-Trip-Status: Booking {$booking->booking_code} transitioned from dibayar to berjalan.");
            }, 3);
        }

        // 2. Ongoing (berjalan) -> Completed (selesai) setelah tanggal/durasi akhir lewat
        $ongoingBookings = Booking::with(['schedule', 'package'])
            ->where('status', 'berjalan')
            ->get();

        foreach ($ongoingBookings as $candidate) {
            $endAt = $this->resolveEndDateTime($candidate, $timezone);
            if (!$endAt || $now->lte($endAt)) {
                continue;
            }

            DB::transaction(function () use ($candidate, &$completedCount) {
                $booking = Booking::where('id', $candidate->id)->lockForUpdate()->first();
                if (!$booking || $booking->status !== 'berjalan') {
                    return;
                }

                $booking->update(['status' => 'selesai']);
                $completedCount++;

                Log::info("Auto-Trip-Status: Booking {$booking->booking_code} transitioned from berjalan to selesai.");
            }, 3);
        }

        $this->info("Trip status sync complete ({$timezone}): {$startedCount} started (ongoing), {$completedCount} completed.");

        return Command::SUCCESS;
    }

    /**
     * Tentukan waktu mulai perjalanan berdasarkan jadwal paket atau travel_date.
     */
    private function resolveStartDateTime(Booking $booking, string $timezone): ?Carbon
    {
        if ($booking->schedule && $booking->schedule->start_date) {
            return Carbon::parse($booking->schedule->start_date->toDateString(), $timezone)->startOfDay();
        }

        if ($booking->travel_date) {
            return Carbon::parse($booking->travel_date->toDateString(), $timezone)->startOfDay();
        }

        return null;
    }

    /**
     * Tentukan waktu berakhir perjalanan:
     * - Reguler: akhir hari end_date jadwal (atau travel_date + duration_days - 1).
     * - Custom itinerary: tanggal keberangkatan (mulai pukul 08:00) + total_hours (fallback base hours).
     */
    private function resolveEndDateTime(Booking $booking, string $timezone): ?Carbon
    {
        if ($booking->is_custom) {
            if (!$booking->travel_date) {
                return null;
            }

            $hours = (float) ($booking->total_hours ?: config('trihita.custom_base_hours', 8));
            $durationMinutes = max(60, (int) round($hours * 60));

            return Carbon::parse($booking->travel_date->toDateString(), $timezone)
                ->setTime(8, 0, 0)
                ->addMinutes($durationMinutes);
        }

        if ($booking->schedule && $booking->schedule->end_date) {
            return Carbon::parse($booking->schedule->end_date->toDateString(), $timezone)->endOfDay();
        }

        if ($booking->travel_date) {
            $durationDays = max(1, (int) ($booking->package?->duration_days ?? 1));

            return Carbon::parse($booking->travel_date->toDateString(), $timezone)
                ->addDays($durationDays - 1)
                ->endOfDay();
        }

        return null;
    }
}
