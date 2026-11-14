<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\Booking;
use App\Models\Package;
use App\Models\PackageSchedule;
use App\Models\User;
use App\Services\MidtransService;
use Mockery;
use Tests\TestCase;

class RegularBookingFlowTest extends TestCase
{
    public function test_user_can_book_regular_package_and_view_payment_page(): void
    {
        // 1. Setup Active Agent & Package
        $agentUser = User::factory()->create([
            'role'   => 'agent',
            'status' => 'active',
            'email'  => 'agent_' . uniqid() . '@example.com',
        ]);

        $agent = Agent::create([
            'user_id'       => $agentUser->id,
            'agent_name'    => 'Bali Horizon Tours',
            'phone_number'  => '08123456789',
            'address'       => 'Jl. Raya Ubud, Gianyar',
            'vehicle_count' => 5,
        ]);

        $package = Package::create([
            'agent_id'      => $agent->id,
            'name'          => 'Ubud Cultural Heritage & Waterfall Tour',
            'slug'          => 'ubud-cultural-heritage-' . uniqid(),
            'description'   => 'Explore Ubud waterfalls, temples, and art villages.',
            'category'      => 'reguler',
            'region'        => 'selatan',
            'price_per_pax' => 450000,
            'duration_days' => 1,
            'max_guests'    => 10,
            'avg_rating'    => 4.8,
            'is_active'     => true,
        ]);

        $schedule = PackageSchedule::create([
            'package_id' => $package->id,
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'end_date'   => now()->addDays(5)->format('Y-m-d'),
            'quota'      => 10,
        ]);

        // 2. Setup Traveler User
        $traveler = User::factory()->create([
            'role'   => 'user',
            'status' => 'active',
            'email'  => 'traveler_' . uniqid() . '@example.com',
        ]);

        // 3. Mock MidtransService snap token generation so test does not call external Midtrans API
        $mockMidtrans = Mockery::mock(MidtransService::class);
        $mockMidtrans->shouldReceive('createSnapToken')
            ->once()
            ->andReturn('dummy-snap-token-123456');
        $this->app->instance(MidtransService::class, $mockMidtrans);

        // 4. Submit "Book Tour Now" request
        $response = $this->actingAs($traveler)->post(route('packages.book', $package->slug), [
            'package_schedule_id' => $schedule->id,
            'guest_count'         => 2,
        ]);

        // 5. Assert Booking Created and Redirected to Payment
        $booking = Booking::where('user_id', $traveler->id)
            ->where('package_id', $package->id)
            ->first();

        $this->assertNotNull($booking, 'Booking record must be created in database.');
        $response->assertRedirect(route('payments.show', $booking->booking_code));

        // 6. Assert Custom Itinerary Fields have proper defaults
        $this->assertFalse((bool) $booking->is_custom);
        $this->assertNull($booking->total_hours);
        $this->assertEquals(0, $booking->overtime_hours);
        $this->assertEquals(0, $booking->overtime_charge);
        $this->assertNull($booking->custom_destinations);
        $this->assertEquals('menunggu_pembayaran', $booking->status);
        $this->assertEquals(900000, $booking->total_price);

        // 7. Verify schedule quota decremented from 10 to 8
        $this->assertEquals(8, $schedule->fresh()->quota);

        // 8. Follow redirect to Payment Show page and verify rendering without any error
        $paymentResponse = $this->actingAs($traveler)->get(route('payments.show', $booking->booking_code));
        $paymentResponse->assertStatus(200);
        $paymentResponse->assertSee('Ubud Cultural Heritage & Waterfall Tour');
        $paymentResponse->assertSee('South Bali');
        $paymentResponse->assertSee('Rp 900.000');
        $paymentResponse->assertSee('dummy-snap-token-123456');
    }

    public function test_midtrans_payload_for_regular_package_matches_gross_amount(): void
    {
        $agentUser = User::factory()->create([
            'role'   => 'agent',
            'status' => 'active',
            'email'  => 'agent_m_' . uniqid() . '@example.com',
        ]);

        $agent = Agent::create([
            'user_id'       => $agentUser->id,
            'agent_name'    => 'Bali Agent M',
            'phone_number'  => '08123456788',
            'address'       => 'Jl. Kuta',
            'vehicle_count' => 2,
        ]);

        $package = Package::create([
            'agent_id'      => $agent->id,
            'name'          => 'Nusa Penida Tour',
            'slug'          => 'nusa-penida-' . uniqid(),
            'description'   => 'Island trip.',
            'category'      => 'reguler',
            'region'        => 'timur',
            'price_per_pax' => 600000,
            'duration_days' => 1,
            'max_guests'    => 5,
            'is_active'     => true,
        ]);

        $traveler = User::factory()->create([
            'role'   => 'user',
            'status' => 'active',
            'email'  => 'traveler_m_' . uniqid() . '@example.com',
        ]);

        $booking = Booking::create([
            'booking_code'    => 'TRIP-TEST-' . uniqid(),
            'user_id'         => $traveler->id,
            'package_id'      => $package->id,
            'guest_count'     => 3,
            'total_price'     => 1800000,
            'is_custom'       => false,
            'total_hours'     => null,
            'overtime_hours'  => 0,
            'overtime_charge' => 0,
            'status'          => 'menunggu_pembayaran',
        ]);

        $booking->load(['package', 'user']);

        // Test static regionLabel helper
        $this->assertEquals('East Bali', Package::regionLabel($package->region));
        $this->assertEquals('East Bali', $package->regionLabel());
        $this->assertEquals('East Bali', $package->region_label);
    }
}
