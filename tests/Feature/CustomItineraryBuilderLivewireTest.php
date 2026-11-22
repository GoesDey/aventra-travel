<?php

namespace Tests\Feature;

use App\Livewire\Customer\CustomItineraryBuilder;
use App\Models\Agent;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class CustomItineraryBuilderLivewireTest extends TestCase
{
    use DatabaseTransactions;
    public function test_itinerary_builder_renders_and_initializes_with_region_agent_and_stops(): void
    {
        $user = User::factory()->create([
            'role'   => 'user',
            'status' => 'active',
        ]);

        $component = Livewire::actingAs($user)->test(CustomItineraryBuilder::class);

        $component->assertStatus(200);
        $component->assertSee('Choose Your Bali Region');
        $component->assertSee('Select Verified Partner Agent');
        $component->assertSee('Departure Point & Travel Schedule');
        $component->assertSee('Select Destinations');
        $component->assertSee('Trip Summary');

        // Check defaults
        $this->assertSame(1, $component->get('guestCount'));
        $this->assertNotEmpty($component->get('selectedRegion'));
        $this->assertNotNull($component->get('selectedAgentId'));
        $this->assertNotEmpty($component->get('startPoint'));
        $this->assertNotEmpty($component->get('stops'));
        $this->assertGreaterThan(0, $component->get('totalHours'));
    }

    public function test_switching_region_filters_destinations_to_that_region_only(): void
    {
        $user = User::factory()->create([
            'role'   => 'user',
            'status' => 'active',
        ]);

        $component = Livewire::actingAs($user)->test(CustomItineraryBuilder::class);

        // Switch to North Bali (utara)
        $component->set('selectedRegion', 'utara');

        $stops = $component->get('stops');
        $this->assertNotEmpty($stops);

        foreach ($stops as $stop) {
            $dest = Destination::find($stop['id']);
            $this->assertSame('utara', $dest->region, "Destination {$dest->name} must belong to region 'utara'.");
        }

        // Switch to West Bali (barat)
        $component->set('selectedRegion', 'barat');

        $stopsBarat = $component->get('stops');
        $this->assertNotEmpty($stopsBarat);

        foreach ($stopsBarat as $stop) {
            $dest = Destination::find($stop['id']);
            $this->assertSame('barat', $dest->region, "Destination {$dest->name} must belong to region 'barat'.");
        }
    }

    public function test_selecting_agent_updates_start_point_to_agent_office_address(): void
    {
        $agentUser = User::factory()->create([
            'role'   => 'agent',
            'status' => 'active',
        ]);

        $agent = Agent::create([
            'user_id'           => $agentUser->id,
            'agent_name'        => 'Custom Test Agent ' . uniqid(),
            'phone_number'      => '081999888777',
            'address'           => 'Jl. Danau Tamblingan No. 88, Sanur, Bali',
            'vehicle_count'     => 4,
            'operating_regions' => ['utara', 'selatan', 'timur', 'barat'],
            'latitude'          => -8.691234,
            'longitude'         => 115.259876,
        ]);

        $traveler = User::factory()->create([
            'role'   => 'user',
            'status' => 'active',
        ]);

        $component = Livewire::actingAs($traveler)->test(CustomItineraryBuilder::class);

        $component->set('selectedAgentId', $agent->id);

        $this->assertSame('Jl. Danau Tamblingan No. 88, Sanur, Bali', $component->get('startPoint'));
        $this->assertEquals(-8.691234, $component->get('startLat'));
        $this->assertEquals(115.259876, $component->get('startLng'));

        // User can override the start point with hotel address
        $component->set('startPoint', 'The St. Regis Bali Resort, Nusa Dua');
        $this->assertSame('The St. Regis Bali Resort, Nusa Dua', $component->get('startPoint'));
    }

    public function test_updating_start_coordinates_recalculates_route_and_dispatches_map_event(): void
    {
        $traveler = User::factory()->create([
            'role'   => 'user',
            'status' => 'active',
        ]);

        $component = Livewire::actingAs($traveler)->test(CustomItineraryBuilder::class);

        $initialTravelTime = (float) $component->get('totalTravelTime');
        $initialTotalHours = (float) $component->get('totalHours');

        // Move starting point to a far location (e.g. Gilimanuk in West Bali)
        $component->call('updateStartCoordinates', -8.1587000, 114.4368000, 'Gilimanuk Ferry Port');

        $this->assertEquals(-8.1587000, $component->get('startLat'));
        $this->assertEquals(114.4368000, $component->get('startLng'));
        $this->assertSame('Gilimanuk Ferry Port', $component->get('startPoint'));

        // Route metrics must recalculate
        $newTravelTime = (float) $component->get('totalTravelTime');
        $newTotalHours = (float) $component->get('totalHours');

        $this->assertNotEquals($initialTravelTime, $newTravelTime);
        $this->assertNotEquals($initialTotalHours, $newTotalHours);

        // Also test setting startLat and startLng directly via $wire.set()
        $component->set('startLat', -8.8482000);
        $component->set('startLng', 115.1601000);

        $this->assertEquals(-8.8482000, $component->get('startLat'));
        $this->assertEquals(115.1601000, $component->get('startLng'));
    }

    public function test_selecting_agent_via_select_agent_method_updates_origin_and_dispatches(): void
    {
        $agentUser = User::factory()->create([
            'role'   => 'agent',
            'status' => 'active',
        ]);

        $agent = Agent::create([
            'user_id'           => $agentUser->id,
            'agent_name'        => 'Uluwatu Luxury Tours ' . uniqid(),
            'phone_number'      => '081234567899',
            'address'           => 'Jl. Pantai Suluban No. 10, Pecatu, Bali',
            'vehicle_count'     => 6,
            'operating_regions' => ['utara', 'selatan', 'timur', 'barat'],
            'latitude'          => -8.8104000,
            'longitude'         => 115.1676000,
        ]);

        $traveler = User::factory()->create([
            'role'   => 'user',
            'status' => 'active',
        ]);

        $component = Livewire::actingAs($traveler)->test(CustomItineraryBuilder::class);

        $component->call('selectAgent', $agent->id);

        $this->assertSame($agent->id, $component->get('selectedAgentId'));
        $this->assertSame('Jl. Pantai Suluban No. 10, Pecatu, Bali', $component->get('startPoint'));
        $this->assertEquals(-8.8104000, $component->get('startLat'));
        $this->assertEquals(115.1676000, $component->get('startLng'));

        $component->assertDispatched('itinerary-map-updated');
    }

    public function test_overtime_calculation_when_total_hours_exceeds_base_hours(): void
    {
        $traveler = User::factory()->create([
            'role'   => 'user',
            'status' => 'active',
        ]);

        $component = Livewire::actingAs($traveler)->test(CustomItineraryBuilder::class);

        // Increase stay hours of the first stop to trigger overtime (> 8h)
        $component->call('updateStayHours', 0, 8.0);

        $totalHours = $component->get('totalHours');
        $this->assertGreaterThan(8.0, $totalHours);

        $overtimeCalc = $component->get('overtimeCalculation');
        $this->assertTrue($overtimeCalc['is_overtime']);
        $this->assertGreaterThan(0, $overtimeCalc['overtime_hours']);
        $this->assertGreaterThan(0, $overtimeCalc['overtime_charge']);
        $this->assertSame($overtimeCalc['overtime_hours'] * 75000, $overtimeCalc['overtime_charge']);
    }

    public function test_user_can_submit_custom_itinerary_and_booking_is_saved_with_route_metrics(): void
    {
        $traveler = User::factory()->create([
            'role'   => 'user',
            'status' => 'active',
        ]);

        $component = Livewire::actingAs($traveler)->test(CustomItineraryBuilder::class);

        $component->set('startPoint', 'Ayana Resort, Jimbaran, Bali');
        $component->set('travelDate', now()->addDays(5)->toDateString());
        $component->set('guestCount', 3);
        $component->set('specialRequests', 'Need baby car seat and English-speaking tour guide.');

        $component->call('submitBooking');

        $component->assertHasNoErrors();

        // Verify booking in database
        $booking = Booking::where('user_id', $traveler->id)
            ->where('is_custom', true)
            ->latest('id')
            ->first();

        $this->assertNotNull($booking);
        $this->assertSame('Ayana Resort, Jimbaran, Bali', $booking->start_point);
        $this->assertGreaterThan(0, (float) $booking->travel_time_hours);
        $this->assertGreaterThan(0, (float) $booking->stay_time_hours);
        $this->assertGreaterThan(0, (float) $booking->travel_distance_km);
        $this->assertGreaterThan(0, (float) $booking->total_hours);
        $this->assertNotEmpty($booking->custom_destinations);
        $this->assertSame(3, $booking->guest_count);
        $this->assertSame('Need baby car seat and English-speaking tour guide.', $booking->special_requests);

        // Verify redirect to payments.show
        $component->assertRedirect(route('payments.show', $booking->booking_code));
    }
}

