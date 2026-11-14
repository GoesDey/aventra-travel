<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\Booking;
use App\Models\Package;
use App\Models\PackageSchedule;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class MidtransIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $customer;
    protected User $agentUser;
    protected Agent $agent;
    protected Package $package;
    protected PackageSchedule $schedule;
    protected string $serverKey = 'SB-Mid-server-TESTKEY_XYZ123';

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('midtrans.server_key', $this->serverKey);
        Config::set('midtrans.client_key', 'SB-Mid-client-TESTKEY_XYZ123');
        Config::set('trihita.commission_rate', 10);
        Config::set('trihita.booking_expiry_minutes', 60);

        // Setup Agent
        $this->agentUser = User::create([
            'name'     => 'Agent Test',
            'email'    => 'agent@test.com',
            'password' => Hash::make('password'),
            'role'     => 'agent',
            'status'   => 'active',
        ]);

        $this->agent = Agent::create([
            'user_id'       => $this->agentUser->id,
            'agent_name'    => 'PT Bali Tour Operator',
            'address'       => 'Denpasar, Bali',
            'phone_number'  => '08123456789',
            'vehicle_count' => 4,
        ]);

        // Setup Customer
        $this->customer = User::create([
            'name'     => 'Customer Test',
            'email'    => 'customer@test.com',
            'password' => Hash::make('password'),
            'role'     => 'user',
            'status'   => 'active',
        ]);

        // Setup Package & Schedule (H-5)
        $this->package = Package::create([
            'agent_id'      => $this->agent->id,
            'name'          => 'Paket Tour Nusa Penida',
            'slug'          => 'paket-tour-nusa-penida',
            'description'   => 'Deskripsi paket Nusa Penida.',
            'category'      => 'reguler',
            'region'        => 'timur',
            'price_per_pax' => 500000,
            'duration_days' => 1,
            'max_guests'    => 10,
            'is_active'     => true,
        ]);

        $this->schedule = $this->package->schedules()->create([
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date'   => now()->addDays(5)->toDateString(),
            'quota'      => 10,
        ]);
    }

    protected function createTestBookingAndPayment(int $guestCount = 2, string $status = 'menunggu_pembayaran'): array
    {
        $total = $guestCount * 500000;
        $bookingCode = 'TRIP-' . strtoupper(Str::random(4)) . '-' . date('Ymd') . rand(10, 99);

        $booking = Booking::create([
            'booking_code'        => $bookingCode,
            'user_id'             => $this->customer->id,
            'package_id'          => $this->package->id,
            'package_schedule_id' => $this->schedule->id,
            'travel_date'         => $this->schedule->start_date,
            'guest_count'         => $guestCount,
            'total_price'         => $total,
            'commission_rate'     => 10.00,
            'commission_amount'   => (int) round($total * 0.10),
            'agent_earning'       => $total - (int) round($total * 0.10),
            'expires_at'          => now()->addMinutes(60),
            'status'              => $status,
        ]);

        $payment = Payment::create([
            'booking_id'     => $booking->id,
            'order_id'       => $bookingCode,
            'amount'         => $total,
            'payment_status' => 'pending',
            'snap_token'     => 'dummy_snap_token_' . Str::random(10),
        ]);

        return [$booking, $payment];
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        [$booking, $payment] = $this->createTestBookingAndPayment();

        $payload = [
            'order_id'           => $booking->booking_code,
            'status_code'        => '200',
            'gross_amount'       => (string) $payment->amount,
            'signature_key'      => 'INVALID_FAKE_SIGNATURE_KEY',
            'transaction_status' => 'settlement',
        ];

        $response = $this->postJson('/api/midtrans/webhook', $payload);

        $response->assertStatus(403);
        $this->assertDatabaseHas('bookings', [
            'id'     => $booking->id,
            'status' => 'menunggu_pembayaran',
        ]);
    }

    public function test_webhook_rejects_gross_amount_mismatch(): void
    {
        [$booking, $payment] = $this->createTestBookingAndPayment();

        $wrongAmount = (string) ($payment->amount + 50000);
        $signature = hash('sha512', $booking->booking_code . '200' . $wrongAmount . $this->serverKey);

        $payload = [
            'order_id'           => $booking->booking_code,
            'status_code'        => '200',
            'gross_amount'       => $wrongAmount,
            'signature_key'      => $signature,
            'transaction_status' => 'settlement',
        ];

        $response = $this->postJson('/api/midtrans/webhook', $payload);

        $response->assertStatus(400);
        $this->assertDatabaseHas('bookings', [
            'id'     => $booking->id,
            'status' => 'menunggu_pembayaran',
        ]);
    }

    public function test_webhook_settlement_success_and_idempotent_when_sent_twice(): void
    {
        [$booking, $payment] = $this->createTestBookingAndPayment();

        $grossAmount = (string) $payment->amount;
        $signature = hash('sha512', $booking->booking_code . '200' . $grossAmount . $this->serverKey);

        $payload = [
            'order_id'           => $booking->booking_code,
            'status_code'        => '200',
            'gross_amount'       => $grossAmount,
            'signature_key'      => $signature,
            'transaction_status' => 'settlement',
            'payment_type'       => 'qris',
            'transaction_id'     => 'trx_midtrans_999',
        ];

        // Pengiriman pertama
        $res1 = $this->postJson('/api/midtrans/webhook', $payload);
        $res1->assertStatus(200);

        $booking->refresh();
        $payment->refresh();

        $this->assertEquals('dibayar', $booking->status);
        $this->assertEquals('success', $payment->payment_status);
        $this->assertEquals('trx_midtrans_999', $payment->transaction_id);
        $this->assertNotNull($payment->paid_at);

        // Pengiriman kedua (Idempotent: payload yang sama dikirim ulang)
        $res2 = $this->postJson('/api/midtrans/webhook', $payload);
        $res2->assertStatus(200);

        $booking->refresh();
        $payment->refresh();
        $this->assertEquals('dibayar', $booking->status);
        $this->assertEquals('success', $payment->payment_status);
    }

    public function test_webhook_failure_restores_quota_only_once(): void
    {
        // Misalkan quota awal adalah 10, lalu berkurang 2 karena booking dibuat -> sisa 8
        $this->schedule->update(['quota' => 8]);
        [$booking, $payment] = $this->createTestBookingAndPayment(2);

        $grossAmount = (string) $payment->amount;
        $signature = hash('sha512', $booking->booking_code . '202' . $grossAmount . $this->serverKey);

        $payload = [
            'order_id'           => $booking->booking_code,
            'status_code'        => '202',
            'gross_amount'       => $grossAmount,
            'signature_key'      => $signature,
            'transaction_status' => 'cancel',
            'payment_type'       => 'bank_transfer',
            'transaction_id'     => 'trx_cancel_123',
        ];

        // Pengiriman pertama: Quota harus bertambah 2 (kembali ke 10)
        $res1 = $this->postJson('/api/midtrans/webhook', $payload);
        $res1->assertStatus(200);

        $this->schedule->refresh();
        $booking->refresh();
        $payment->refresh();

        $this->assertEquals(10, $this->schedule->quota);
        $this->assertEquals('dibatalkan', $booking->status);
        $this->assertEquals('failed', $payment->payment_status);

        // Pengiriman kedua: Quota TIDAK boleh bertambah lagi (tetap 10)
        $res2 = $this->postJson('/api/midtrans/webhook', $payload);
        $res2->assertStatus(200);

        $this->schedule->refresh();
        $this->assertEquals(10, $this->schedule->quota);
    }

    public function test_settlement_after_expired_reactivates_when_quota_available(): void
    {
        // Booking sudah expired dan kuota sudah dikembalikan
        $this->schedule->update(['quota' => 10]);
        [$booking, $payment] = $this->createTestBookingAndPayment(2, 'kedaluwarsa');
        $payment->update(['payment_status' => 'expired']);

        $grossAmount = (string) $payment->amount;
        $signature = hash('sha512', $booking->booking_code . '200' . $grossAmount . $this->serverKey);

        $payload = [
            'order_id'           => $booking->booking_code,
            'status_code'        => '200',
            'gross_amount'       => $grossAmount,
            'signature_key'      => $signature,
            'transaction_status' => 'settlement',
            'payment_type'       => 'bca_va',
            'transaction_id'     => 'trx_late_settlement_123',
        ];

        $response = $this->postJson('/api/midtrans/webhook', $payload);
        $response->assertStatus(200);

        $booking->refresh();
        $payment->refresh();
        $this->schedule->refresh();

        // Booking diaktifkan kembali menjadi 'dibayar', kuota dikurangi 2 (10 -> 8)
        $this->assertEquals('dibayar', $booking->status);
        $this->assertEquals('success', $payment->payment_status);
        $this->assertEquals(8, $this->schedule->quota);
    }

    public function test_settlement_after_expired_marks_perlu_refund_when_quota_full(): void
    {
        // Booking sudah expired, namun kuota sekarang sudah habis (0)
        $this->schedule->update(['quota' => 0]);
        [$booking, $payment] = $this->createTestBookingAndPayment(2, 'kedaluwarsa');
        $payment->update(['payment_status' => 'expired']);

        $grossAmount = (string) $payment->amount;
        $signature = hash('sha512', $booking->booking_code . '200' . $grossAmount . $this->serverKey);

        $payload = [
            'order_id'           => $booking->booking_code,
            'status_code'        => '200',
            'gross_amount'       => $grossAmount,
            'signature_key'      => $signature,
            'transaction_status' => 'settlement',
            'payment_type'       => 'bca_va',
            'transaction_id'     => 'trx_late_refund_123',
        ];

        $response = $this->postJson('/api/midtrans/webhook', $payload);
        $response->assertStatus(200);

        $booking->refresh();
        $payment->refresh();

        // Ditandai 'perlu_refund' untuk ditangani admin
        $this->assertEquals('perlu_refund', $booking->status);
        $this->assertEquals('success', $payment->payment_status);
    }

    public function test_two_bookings_competing_for_one_seat_only_one_succeeds(): void
    {
        // Set sisa kuota hanya 1
        $this->schedule->update(['quota' => 1]);

        $user1 = User::create([
            'name'     => 'User Satu',
            'email'    => 'user1@test.com',
            'password' => Hash::make('password'),
            'role'     => 'user',
            'status'   => 'active',
        ]);

        $user2 = User::create([
            'name'     => 'User Dua',
            'email'    => 'user2@test.com',
            'password' => Hash::make('password'),
            'role'     => 'user',
            'status'   => 'active',
        ]);

        // User 1 memesan 1 kursi -> Sukses
        $res1 = $this->actingAs($user1)->post("/packages/{$this->package->slug}/book", [
            'package_schedule_id' => $this->schedule->id,
            'guest_count'         => 1,
        ]);

        $this->schedule->refresh();
        $this->assertEquals(0, $this->schedule->quota);

        // User 2 mencoba memesan 1 kursi untuk jadwal yang sama -> Ditolak karena kuota 0
        $res2 = $this->actingAs($user2)->post("/packages/{$this->package->slug}/book", [
            'package_schedule_id' => $this->schedule->id,
            'guest_count'         => 1,
        ]);

        $res2->assertSessionHasErrors('guest_count');

        // Kuota tidak berubah (tetap 0) dan hanya ada 1 booking yang dibuat
        $this->schedule->refresh();
        $this->assertEquals(0, $this->schedule->quota);
        $this->assertEquals(1, Booking::where('package_schedule_id', $this->schedule->id)->count());
    }

    public function test_auto_expire_scheduled_command(): void
    {
        $this->schedule->update(['quota' => 5]);

        // Buat booking yang sudah melewati batas waktu
        [$booking, $payment] = $this->createTestBookingAndPayment(3);
        $booking->update(['expires_at' => now()->subMinutes(10)]);

        $this->assertEquals('menunggu_pembayaran', $booking->status);

        // Jalankan artisan command
        Artisan::call('bookings:expire-pending');

        $booking->refresh();
        $payment->refresh();
        $this->schedule->refresh();

        $this->assertEquals('kedaluwarsa', $booking->status);
        $this->assertEquals('expired', $payment->payment_status);
        // Kuota dikembalikan 3 (5 + 3 = 8)
        $this->assertEquals(8, $this->schedule->quota);
    }

    public function test_midtrans_callback_route_bypasses_csrf(): void
    {
        [$booking, $payment] = $this->createTestBookingAndPayment();

        $payload = [
            'order_id'           => $booking->booking_code,
            'status_code'        => '200',
            'gross_amount'       => (string) $payment->amount,
            'signature_key'      => 'INVALID_FAKE_SIGNATURE_KEY',
            'transaction_status' => 'settlement',
        ];

        // Testing direct post without CSRF token to /midtrans/callback
        $response = $this->post('/midtrans/callback', $payload);

        // It should pass CSRF verification and hit the controller (which rejects fake signature with 403, NOT 419 Page Expired)
        $this->assertNotEquals(419, $response->getStatusCode(), 'Route /midtrans/callback must not fail with 419 CSRF Token Mismatch.');
        $response->assertStatus(403);
    }
}
