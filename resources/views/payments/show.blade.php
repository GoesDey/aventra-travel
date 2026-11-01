<x-layouts.app title="Pembayaran - {{ $booking->booking_code }}">
    <div class="bg-surface-container-lowest min-h-screen pt-24 pb-16 px-margin-mobile md:px-margin-desktop">
        <div class="max-w-3xl mx-auto space-y-6">

            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-2 text-xs md:text-sm text-outline">
                <a href="{{ route('home') }}" class="hover:text-tropical-teal transition-colors">Beranda</a>
                <span>/</span>
                <a href="{{ route('packages.index') }}" class="hover:text-tropical-teal transition-colors">Paket Wisata</a>
                <span>/</span>
                <span class="text-on-surface font-semibold">Pembayaran ({{ $booking->booking_code }})</span>
            </nav>

            <!-- Status Pembayaran Berhasil (Success Modal / Banner) -->
            <div id="paymentSuccessBanner" class="hidden bg-emerald-50 border-2 border-emerald-400 text-emerald-900 p-6 rounded-3xl shadow-lg space-y-3 animate-fade-in">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-emerald-600 text-3xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                    <div>
                        <h3 class="font-bold text-base md:text-lg">Pembayaran Berhasil Dikonfirmasi!</h3>
                        <p class="text-xs md:text-sm text-emerald-800">Status pesanan Anda telah diperbarui menjadi <span class="font-bold underline">Dibayar</span>. Mengalihkan ke riwayat pesanan...</p>
                    </div>
                </div>
            </div>

            <!-- Countdown Timer Card -->
            <div class="bg-surface-white p-6 rounded-3xl border border-outline-variant/30 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">timer</span>
                    </div>
                    <div>
                        <span class="text-xs text-outline block">Selesaikan Pembayaran Sebelum:</span>
                        <span class="font-bold text-sm md:text-base text-oceanic-deep">
                            {{ $booking->expires_at ? $booking->expires_at->format('d M Y, H:i') : '-' }} WIB
                        </span>
                    </div>
                </div>

                <div class="text-center sm:text-right bg-amber-50 sm:bg-transparent px-4 py-2 sm:p-0 rounded-2xl w-full sm:w-auto">
                    <span class="text-[11px] text-amber-800 font-semibold block uppercase tracking-wider">Sisa Waktu Pembayaran</span>
                    <span id="countdownTimer" class="font-mono text-2xl font-bold text-amber-900">
                        --:--:--
                    </span>
                </div>
            </div>

            <!-- Ringkasan Pemesanan Card -->
            <div class="bg-surface-white p-6 md:p-8 rounded-3xl border border-outline-variant/30 shadow-xl space-y-6">
                <!-- Header Card -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-outline-variant/30 pb-5">
                    <div>
                        <span class="text-xs text-outline uppercase tracking-wider font-semibold">Kode Booking Resmi</span>
                        <div class="font-mono text-xl font-bold text-oceanic-deep mt-0.5">{{ $booking->booking_code }}</div>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900" id="bookingStatusBadge">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>Menunggu Pembayaran</span>
                        </span>
                    </div>
                </div>

                <!-- Detail Paket & Jadwal -->
                <div class="flex flex-col sm:flex-row items-start gap-4 p-4 rounded-2xl bg-surface-container-low border border-outline-variant/20">
                    @php
                        $coverUrl = 'https://placehold.co/400x300/00327D/ffffff?text=' . urlencode($booking->package->name);
                        if ($booking->package->cover_image) {
                            $coverUrl = str_starts_with($booking->package->cover_image, 'http')
                                ? $booking->package->cover_image
                                : asset('storage/' . $booking->package->cover_image);
                        }
                    @endphp
                    <img src="{{ $coverUrl }}" alt="{{ $booking->package->name }}" class="w-24 h-24 rounded-xl object-cover shrink-0 border border-outline-variant/30">
                    <div class="space-y-1.5 min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-oceanic-deep text-white">{{ ucfirst($booking->package->category) }}</span>
                            <span class="text-[11px] font-semibold text-outline">Bali {{ ucfirst($booking->package->region) }}</span>
                        </div>
                        <h3 class="font-headline-md text-base font-bold text-oceanic-deep truncate">{{ $booking->package->name }}</h3>
                        <div class="text-xs text-on-surface-variant flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm text-tropical-teal">calendar_month</span>
                            <span>
                                {{ $booking->schedule->start_date->format('d M Y') }} - {{ $booking->schedule->end_date->format('d M Y') }}
                                ({{ $booking->package->duration_days }} Hari)
                            </span>
                        </div>
                        <div class="text-xs text-outline flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm">group</span>
                            <span>Jumlah Peserta: <strong class="text-on-surface">{{ $booking->guest_count }} Orang</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Rincian Biaya -->
                <div class="space-y-2 pt-2 border-t border-outline-variant/30 text-sm">
                    <div class="flex justify-between items-center text-on-surface-variant">
                        <span>Harga per Orang</span>
                        <span>Rp {{ number_format($booking->package->price_per_pax, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-on-surface-variant">
                        <span>Jumlah Peserta</span>
                        <span>{{ $booking->guest_count }} Pax</span>
                    </div>
                    <div class="flex justify-between items-center text-base font-bold text-oceanic-deep pt-3 border-t border-dashed border-outline-variant/40">
                        <span>Total Pembayaran</span>
                        <span class="font-display-lg text-2xl text-tropical-teal">
                            Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Tombol Bayar Sekarang -->
                <div class="pt-4 space-y-3">
                    <button type="button" id="payButton"
                        class="w-full bg-tropical-teal hover:bg-secondary text-white font-label-bold text-base py-4 rounded-2xl flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transition-all duration-200 active:scale-[0.98]">
                        <span class="material-symbols-outlined text-xl">payments</span>
                        <span id="payButtonText">Bayar Sekarang dengan Midtrans</span>
                    </button>

                    <p class="text-center text-xs text-outline">
                        Metode pembayaran aman otomatis: QRIS, BCA/Mandiri/BNI/BRI Virtual Account, Gopay, ShopeePay, dll.
                    </p>
                </div>
            </div>

            <!-- Catatan Keamanan -->
            <div class="p-4 bg-surface-container-low rounded-2xl border border-outline-variant/30 text-xs text-outline space-y-1">
                <div class="font-bold text-on-surface flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm text-tropical-teal">security</span>
                    <span>Informasi Pembayaran Aman</span>
                </div>
                <p>Status transaksi diproses secara real-time melalui webhook resmi Midtrans. Jika jendela pembayaran tertutup, Anda dapat membuka kembali jendela pembayaran selama waktu pemesanan belum habis.</p>
            </div>
        </div>
    </div>

    <!-- Load Midtrans Snap JS Sandbox/Production -->
    <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const snapToken = "{{ $payment->snap_token }}";
            const payButton = document.getElementById('payButton');
            const payButtonText = document.getElementById('payButtonText');
            const bookingCode = "{{ $booking->booking_code }}";
            const expiresAtTimestamp = {{ $booking->expires_at ? $booking->expires_at->timestamp * 1000 : 0 }};

            // 1. Countdown Timer
            const countdownEl = document.getElementById('countdownTimer');
            function updateCountdown() {
                if (!expiresAtTimestamp) return;
                const now = new Date().getTime();
                const distance = expiresAtTimestamp - now;

                if (distance <= 0) {
                    countdownEl.textContent = "00:00:00 (Kedaluwarsa)";
                    countdownEl.classList.add('text-error');
                    if (payButton) {
                        payButton.disabled = true;
                        payButton.classList.add('opacity-50', 'cursor-not-allowed');
                        payButtonText.textContent = "Batas Waktu Pembayaran Habis";
                    }
                    return;
                }

                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                const pad = (n) => String(n).padStart(2, '0');
                countdownEl.textContent = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
            }

            setInterval(updateCountdown, 1000);
            updateCountdown();

            // 2. Trigger Midtrans Snap Popup
            payButton.addEventListener('click', function () {
                if (!snapToken) {
                    alert('Token pembayaran tidak ditemukan. Silakan muat ulang halaman.');
                    return;
                }

                // Nonaktifkan tombol untuk mencegah double click
                payButton.disabled = true;
                payButtonText.textContent = "Membuka Midtrans Snap...";

                window.snap.pay(snapToken, {
                    onSuccess: function (result) {
                        payButtonText.textContent = "Pembayaran Berhasil! Memverifikasi...";
                        checkPaymentStatusOnce();
                    },
                    onPending: function (result) {
                        payButtonText.textContent = "Menunggu Pembayaran Selesai";
                        payButton.disabled = false;
                        alert('Silakan selesaikan pembayaran sesuai instruksi Virtual Account / QRIS yang tertera.');
                    },
                    onError: function (result) {
                        payButtonText.textContent = "Bayar Sekarang dengan Midtrans";
                        payButton.disabled = false;
                        alert('Terjadi kesalahan pada transaksi pembayaran. Silakan coba kembali.');
                    },
                    onClose: function () {
                        payButtonText.textContent = "Bayar Sekarang dengan Midtrans";
                        payButton.disabled = false;
                    }
                });
            });

            // 3. Polling Ringan Status Pembayaran (Setiap 5 detik)
            function checkPaymentStatusOnce() {
                fetch(`/payments/${bookingCode}/status`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.is_paid) {
                        document.getElementById('paymentSuccessBanner').classList.remove('hidden');
                        document.getElementById('bookingStatusBadge').className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800';
                        document.getElementById('bookingStatusBadge').innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-500"></span><span>Dibayar</span>';
                        if (payButton) {
                            payButton.disabled = true;
                            payButton.className = 'w-full bg-emerald-600 text-white font-bold text-base py-4 rounded-2xl flex items-center justify-center gap-2 cursor-default';
                            payButtonText.textContent = "Lunas (Terverifikasi)";
                        }

                        // Redirect ke halaman riwayat setelah 2 detik
                        setTimeout(() => {
                            window.location.href = "{{ route('history') }}";
                        }, 2000);
                    } else if (data.is_expired) {
                        countdownEl.textContent = "Kedaluwarsa";
                        payButton.disabled = true;
                        payButtonText.textContent = "Pemesanan Telah Kedaluwarsa";
                    }
                })
                .catch(err => console.log('Polling status check:', err));
            }

            // Jalankan polling tiap 5 detik
            const pollInterval = setInterval(checkPaymentStatusOnce, 5000);
        });
    </script>
</x-layouts.app>
