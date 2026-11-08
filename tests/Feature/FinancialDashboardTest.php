<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\Booking;
use App\Models\Package;
use App\Models\PackageSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class FinancialDashboardTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $agentUserA;
    protected Agent $agentA;
    protected Package $packageA;

    protected User $agentUserB;
    protected Agent $agentB;
    protected Package $packageB;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup Super Admin
        $this->superAdmin = User::create([
            'name'     => 'Super Admin Test',
            'email'    => 'superadmin@test.com',
            'password' => Hash::make('password'),
            'role'     => 'super_admin',
            'status'   => 'active',
        ]);

        // 2. Setup Agent A
        $this->agentUserA = User::create([
            'name'     => 'Agent A User',
            'email'    => 'agenta@test.com',
            'password' => Hash::make('password'),
            'role'     => 'agent',
            'status'   => 'active',
        ]);

        $this->agentA = Agent::create([
            'user_id'             => $this->agentUserA->id,
            'agent_name'          => 'PT Bali Tour Operator A',
            'description'         => 'Operator A',
            'address'             => 'Denpasar',
            'phone_number'        => '0811111111',
            'vehicle_count'       => 5,
            'bank_name'           => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_name'   => 'PT Bali Tour Operator A',
        ]);

        $this->packageA = Package::create([
            'agent_id'      => $this->agentA->id,
            'name'          => 'Paket Tour Nusa Penida A',
            'slug'          => 'paket-tour-nusa-penida-a-' . Str::random(5),
            'description'   => 'Deskripsi paket A.',
            'category'      => 'reguler',
            'region'        => 'timur',
            'price_per_pax' => 500000,
            'duration_days' => 1,
            'max_guests'    => 10,
            'is_active'     => true,
        ]);

        // 3. Setup Agent B
        $this->agentUserB = User::create([
            'name'     => 'Agent B User',
            'email'    => 'agentb@test.com',
            'password' => Hash::make('password'),
            'role'     => 'agent',
            'status'   => 'active',
        ]);

        $this->agentB = Agent::create([
            'user_id'             => $this->agentUserB->id,
            'agent_name'          => 'PT Ubud Adventure B',
            'description'         => 'Operator B',
            'address'             => 'Ubud',
            'phone_number'        => '0822222222',
            'vehicle_count'       => 3,
            'bank_name'           => 'Mandiri',
            'bank_account_number' => '0987654321',
            'bank_account_name'   => 'PT Ubud Adventure B',
        ]);

        $this->packageB = Package::create([
            'agent_id'      => $this->agentB->id,
            'name'          => 'Paket Rafting Ayung B',
            'slug'          => 'paket-rafting-ayung-b-' . Str::random(5),
            'description'   => 'Deskripsi paket B.',
            'category'      => 'custom',
            'region'        => 'barat',
            'price_per_pax' => 300000,
            'duration_days' => 1,
            'max_guests'    => 10,
            'is_active'     => true,
        ]);

        // 4. Setup Customer
        $this->customer = User::create([
            'name'     => 'Customer Test',
            'email'    => 'customer@test.com',
            'password' => Hash::make('password'),
            'role'     => 'user',
            'status'   => 'active',
        ]);
    }

    public function test_super_admin_dashboard_security_and_financial_aggregation(): void
    {
        // 1. Tamu (guest) diarahkan ke login
        $resGuest = $this->get('/super-admin/dashboard');
        $resGuest->assertRedirect('/login');

        // 2. User biasa ditolak (403)
        $resUser = $this->actingAs($this->customer)->get('/super-admin/dashboard');
        $resUser->assertStatus(403);

        // 3. Agen ditolak (403)
        $resAgent = $this->actingAs($this->agentUserA)->get('/super-admin/dashboard');
        $resAgent->assertStatus(403);

        // Buat booking sukses untuk Agen A (Total: 1.000.000, Comm: 100.000, Earning: 900.000)
        Booking::create([
            'booking_code'       => 'TRIP-TEST-A1',
            'user_id'            => $this->customer->id,
            'package_id'         => $this->packageA->id,
            'guest_count'        => 2,
            'total_price'        => 1000000,
            'commission_rate'    => 10.00,
            'commission_amount'  => 100000,
            'agent_earning'      => 900000,
            'status'             => 'dibayar',
        ]);

        // Buat booking sukses untuk Agen B (Total: 600.000, Comm: 60.000, Earning: 540.000)
        Booking::create([
            'booking_code'       => 'TRIP-TEST-B1',
            'user_id'            => $this->customer->id,
            'package_id'         => $this->packageB->id,
            'guest_count'        => 2,
            'total_price'        => 600000,
            'commission_rate'    => 10.00,
            'commission_amount'  => 60000,
            'agent_earning'      => 540000,
            'status'             => 'selesai',
        ]);

        // Booking dibatalkan (TIDAK boleh dihitung di omset/komisi)
        Booking::create([
            'booking_code'       => 'TRIP-TEST-CANCEL',
            'user_id'            => $this->customer->id,
            'package_id'         => $this->packageA->id,
            'guest_count'        => 2,
            'total_price'        => 1000000,
            'commission_rate'    => 10.00,
            'commission_amount'  => 100000,
            'agent_earning'      => 900000,
            'status'             => 'dibatalkan',
        ]);

        // 4. Super Admin mengakses dashboard
        $response = $this->actingAs($this->superAdmin)->get('/super-admin/dashboard');
        $response->assertStatus(200);

        // Verifikasi view data
        $response->assertViewHas('total_commission', 160000);
        $response->assertViewHas('total_successful_transactions', 2);
        $response->assertViewHas('total_active_agents');
        $response->assertViewHas('top_agents');
        $response->assertViewHas('totalPlatformCommission', 160000);
        $response->assertViewHas('totalGrossRevenue', 1600000);
        $response->assertViewHas('totalAgentEarnings', 1440000);
        $response->assertViewHas('totalSuccessfulBookings', 2);

        // Verifikasi konten halaman memuat nama agen teratas
        $response->assertSee('PT Bali Tour Operator A');
        $response->assertSee('PT Ubud Adventure B');
        $response->assertSee('Dashboard Keuangan Super Admin');
    }

    public function test_agent_dashboard_strictly_isolates_financial_data_between_agents(): void
    {
        // 1. Tamu (guest) diarahkan ke login
        $resGuest = $this->get('/agent/dashboard');
        $resGuest->assertRedirect('/login');

        // 2. User biasa ditolak (403)
        $resUser = $this->actingAs($this->customer)->get('/agent/dashboard');
        $resUser->assertStatus(403);

        // 3. Super Admin bukan agen (403)
        $resAdmin = $this->actingAs($this->superAdmin)->get('/agent/dashboard');
        $resAdmin->assertStatus(403);

        // Buat booking Agen A: 900.000 earning (dibayar)
        $bookingA = Booking::create([
            'booking_code'       => 'TRIP-AGENTA-101',
            'user_id'            => $this->customer->id,
            'package_id'         => $this->packageA->id,
            'guest_count'        => 2,
            'total_price'        => 1000000,
            'commission_rate'    => 10.00,
            'commission_amount'  => 100000,
            'agent_earning'      => 900000,
            'status'             => 'dibayar',
        ]);

        // Buat booking Agen B: 540.000 earning (selesai)
        $bookingB = Booking::create([
            'booking_code'       => 'TRIP-AGENTB-202',
            'user_id'            => $this->customer->id,
            'package_id'         => $this->packageB->id,
            'guest_count'        => 2,
            'total_price'        => 600000,
            'commission_rate'    => 10.00,
            'commission_amount'  => 60000,
            'agent_earning'      => 540000,
            'status'             => 'selesai',
        ]);

        // 4. Agen A mengakses dashboardnya
        $responseA = $this->actingAs($this->agentUserA)->get('/agent/dashboard');
        $responseA->assertStatus(200);

        // HANYA melihat earning milik Agen A (900.000)
        $responseA->assertViewHas('total_earning', 900000);
        $responseA->assertViewHas('active_bookings_count', 1);
        $responseA->assertViewHas('recent_bookings');
        $responseA->assertViewHas('totalEarnings', 900000);
        $responseA->assertViewHas('successfulOrdersCount', 1);
        $responseA->assertViewHas('activeOrdersCount', 1);
        $responseA->assertSee('TRIP-AGENTA-101');
        $responseA->assertSee('Paket Tour Nusa Penida A');

        // TIDAK BOLEH melihat data Agen B
        $responseA->assertDontSee('TRIP-AGENTB-202');
        $responseA->assertDontSee('Paket Rafting Ayung B');

        // 5. Agen B mengakses dashboardnya
        $responseB = $this->actingAs($this->agentUserB)->get('/agent/dashboard');
        $responseB->assertStatus(200);

        // HANYA melihat earning milik Agen B (540.000)
        $responseB->assertViewHas('total_earning', 540000);
        $responseB->assertViewHas('active_bookings_count', 0);
        $responseB->assertViewHas('recent_bookings');
        $responseB->assertViewHas('totalEarnings', 540000);
        $responseB->assertViewHas('successfulOrdersCount', 1);
        $responseB->assertViewHas('activeOrdersCount', 0);
        $responseB->assertSee('TRIP-AGENTB-202');
        $responseB->assertSee('Paket Rafting Ayung B');

        // TIDAK BOLEH melihat data Agen A
        $responseB->assertDontSee('TRIP-AGENTA-101');
        $responseB->assertDontSee('Paket Tour Nusa Penida A');
    }
}
