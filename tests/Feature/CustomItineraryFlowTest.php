<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class CustomItineraryFlowTest extends TestCase
{
    public function test_guest_is_redirected_to_login_when_accessing_custom_itinerary(): void
    {
        $response = $this->get(route('custom-itinerary.create'));
        $response->assertRedirect(route('login'));

        $responseAlias = $this->get(route('user.custom-itinerary.create'));
        $responseAlias->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_custom_itinerary_page(): void
    {
        $user = User::factory()->create([
            'role'   => 'user',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('custom-itinerary.create'));
        $response->assertStatus(200);
        $response->assertSee('Build Your Custom Itinerary');
        $response->assertSee('Tailor-Made Bali Experience');

        $responseAlias = $this->actingAs($user)->get(route('user.custom-itinerary.create'));
        $responseAlias->assertStatus(200);
    }

    public function test_landing_page_has_cta_button_for_custom_itinerary(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Build Custom Itinerary');
        $response->assertSee(route('custom-itinerary.create'));
    }

    public function test_navbar_contains_custom_trip_link(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Custom Trip');
        $response->assertSee(route('custom-itinerary.create'));
    }

    public function test_packages_index_has_custom_itinerary_cta_banner(): void
    {
        $response = $this->get(route('packages.index'));
        $response->assertStatus(200);
        $response->assertSee("Can't find the perfect trip? Request a Custom Itinerary", false);
        $response->assertSee(route('custom-itinerary.create'));
    }
}
