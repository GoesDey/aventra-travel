<x-layouts.app title="Payment - {{ $booking->booking_code }}">
    <div class="bg-surface-container-lowest min-h-screen pt-24 pb-16 px-margin-mobile md:px-margin-desktop">
        <div class="max-w-3xl mx-auto space-y-6">

            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-2 text-xs md:text-sm text-outline">
                <a href="{{ route('home') }}" class="hover:text-tropical-teal transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('packages.index') }}" class="hover:text-tropical-teal transition-colors">Tour Packages</a>
                <span>/</span>
                <span class="text-on-surface font-semibold">Payment ({{ $booking->booking_code }})</span>
            </nav>

            <!-- Payment Success Banner -->
            <div id="paymentSuccessBanner" class="hidden bg-emerald-50 border-2 border-emerald-400 text-emerald-900 p-6 rounded-3xl shadow-lg space-y-3 animate-fade-in">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-emerald-600 text-3xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                    <div>
                        <h3 class="font-bold text-base md:text-lg">Payment Confirmed Successfully!</h3>
                        <p class="text-xs md:text-sm text-emerald-800">Your booking status has been updated to <span class="font-bold underline">Paid</span>. Redirecting to booking history...</p>
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
                        <span class="text-xs text-outline block">Complete Payment Before:</span>
                        <span class="font-bold text-sm md:text-base text-oceanic-deep">
                            {{ $booking->expires_at ? $booking->expires_at->format('d M Y, H:i') : '-' }} WITA
                        </span>
                    </div>
                </div>

                <div class="text-center sm:text-right bg-amber-50 sm:bg-transparent px-4 py-2 sm:p-0 rounded-2xl w-full sm:w-auto">
                    <span class="text-[11px] text-amber-800 font-semibold block uppercase tracking-wider">Time Remaining</span>
                    <span id="countdownTimer" class="font-mono text-2xl font-bold text-amber-900">
                        --:--:--
                    </span>
                </div>
            </div>

            <!-- Booking Summary Card -->
            <div class="bg-surface-white p-6 md:p-8 rounded-3xl border border-outline-variant/30 shadow-xl space-y-6">
                <!-- Header Card -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-outline-variant/30 pb-5">
                    <div>
                        <span class="text-xs text-outline uppercase tracking-wider font-semibold">Official Booking Code</span>
                        <div class="font-mono text-xl font-bold text-oceanic-deep mt-0.5">{{ $booking->booking_code }}</div>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900" id="bookingStatusBadge">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>Awaiting Payment</span>
                        </span>
                    </div>
                </div>

                <!-- Package & Schedule Details -->
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
                            <span class="text-[11px] font-semibold text-outline">{{ \App\Models\Package::regionLabel($booking->package->region) }}</span>
                        </div>
                        <h3 class="font-headline-md text-base font-bold text-oceanic-deep truncate">{{ $booking->package->name }}</h3>
                        <div class="text-xs text-on-surface-variant flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm text-tropical-teal">calendar_month</span>
                            <span>
                                @if($booking->schedule)
                                    {{ $booking->schedule->start_date->format('d M Y') }} - {{ $booking->schedule->end_date->format('d M Y') }}
                                    ({{ $booking->package->duration_days }} {{ Str::plural('Day', $booking->package->duration_days) }})
                                @elseif($booking->travel_date)
                                    {{ $booking->travel_date->format('d M Y') }}
                                @endif
                            </span>
                        </div>
                        <div class="text-xs text-outline flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm">group</span>
                            <span>Total Guests: <strong class="text-on-surface">{{ $booking->guest_count }} Pax</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Price Breakdown -->
                <div class="space-y-2 pt-2 border-t border-outline-variant/30 text-sm">
                    <div class="flex justify-between items-center text-on-surface-variant">
                        <span>Price per Person</span>
                        <span>Rp {{ number_format($booking->package->price_per_pax, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-on-surface-variant">
                        <span>Number of Guests</span>
                        <span>{{ $booking->guest_count }} Pax</span>
                    </div>
                    @if($booking->is_custom && $booking->overtime_charge > 0)
                        <div class="flex justify-between items-center text-amber-700">
                            <span>Overtime Charge ({{ $booking->overtime_hours }} hrs)</span>
                            <span>Rp {{ number_format($booking->overtime_charge, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center text-base font-bold text-oceanic-deep pt-3 border-t border-dashed border-outline-variant/40">
                        <span>Total Payment</span>
                        <span class="font-display-lg text-2xl text-tropical-teal">
                            Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Pay Button -->
                <div class="pt-4 space-y-3">
                    <button type="button" id="payButton"
                        class="w-full bg-tropical-teal hover:bg-secondary text-white font-label-bold text-base py-4 rounded-2xl flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transition-all duration-200 active:scale-[0.98]">
                        <span class="material-symbols-outlined text-xl">payments</span>
                        <span id="payButtonText">Pay Now with Midtrans</span>
                    </button>

                    <p class="text-center text-xs text-outline">
                        Secure automated payment methods: QRIS, Virtual Accounts (BCA, Mandiri, BNI, BRI), GoPay, ShopeePay, Credit Card, etc.
                    </p>
                </div>
            </div>

            <!-- Security Note -->
            <div class="p-4 bg-surface-container-low rounded-2xl border border-outline-variant/30 text-xs text-outline space-y-1">
                <div class="font-bold text-on-surface flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm text-tropical-teal">security</span>
                    <span>Secure Payment Information</span>
                </div>
                <p>Transaction status is processed in real-time via official Midtrans webhooks. If the payment popup closes, you can reopen it as long as the payment window has not expired.</p>
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
                    countdownEl.textContent = "00:00:00 (Expired)";
                    countdownEl.classList.add('text-error');
                    if (payButton) {
                        payButton.disabled = true;
                        payButton.classList.add('opacity-50', 'cursor-not-allowed');
                        payButtonText.textContent = "Payment Time Expired";
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
                    alert('Payment token not found. Please reload the page.');
                    return;
                }

                payButton.disabled = true;
                payButtonText.textContent = "Opening Midtrans Snap...";

                window.snap.pay(snapToken, {
                    onSuccess: function (result) {
                        window.location.reload();
                    },
                    onPending: function (result) {
                        window.location.reload();
                    },
                    onError: function (result) {
                        alert('An error occurred during payment transaction. Please try again.');
                        window.location.reload();
                    },
                    onClose: function () {
                        window.location.reload();
                    }
                });
            });

            // 3. Polling Payment Status (Every 5 seconds)
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
                        document.getElementById('bookingStatusBadge').innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-500"></span><span>Paid</span>';
                        if (payButton) {
                            payButton.disabled = true;
                            payButton.className = 'w-full bg-emerald-600 text-white font-bold text-base py-4 rounded-2xl flex items-center justify-center gap-2 cursor-default';
                            payButtonText.textContent = "Paid (Verified)";
                        }

                        // Redirect to history after 2 seconds
                        setTimeout(() => {
                            window.location.href = "{{ route('history') }}";
                        }, 2000);
                    } else if (data.is_expired) {
                        countdownEl.textContent = "Expired";
                        payButton.disabled = true;
                        payButtonText.textContent = "Booking Has Expired";
                    }
                })
                .catch(err => console.log('Polling status check:', err));
            }

            const pollInterval = setInterval(checkPaymentStatusOnce, 5000);
        });
    </script>
</x-layouts.app>
