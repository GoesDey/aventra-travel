<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Platform Commission Rate (%)
    |--------------------------------------------------------------------------
    |
    | Persentase komisi platform dari total transaksi pemesanan wisata.
    | Default bernilai 10 (artinya 10%).
    |
    */
    'commission_rate' => (float) env('TRIHITA_COMMISSION_RATE', 10),

    /*
    |--------------------------------------------------------------------------
    | Booking Expiration Duration (Minutes)
    |--------------------------------------------------------------------------
    |
    | Durasi batas waktu pembayaran dalam menit sebelum booking kedaluwarsa.
    |
    */
    'booking_expiry_minutes' => (int) env('TRIHITA_BOOKING_EXPIRY_MINUTES', 60),

    /*
    |--------------------------------------------------------------------------
    | Maximum Guest Count per Booking
    |--------------------------------------------------------------------------
    |
    | Batas maksimal jumlah peserta dalam satu kali pemesanan (booking).
    |
    */
    'max_guest_per_booking' => (int) env('TRIHITA_MAX_GUEST_PER_BOOKING', 20),
];
