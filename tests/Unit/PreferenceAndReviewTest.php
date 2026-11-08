<?php

namespace Tests\Unit;

use App\Models\Agent;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Review;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class PreferenceAndReviewTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_preference_stores_json_and_casts_properly(): void
    {
        $user = User::create([
            'name'     => 'Preference Test User',
            'email'    => 'preftest@example.com',
            'password' => Hash::make('password'),
            'role'     => 'user',
            'status'   => 'active',
        ]);

        $preference = UserPreference::create([
            'user_id'              => $user->id,
            'preferred_categories' => ['reguler', 'custom'],
            'preferred_regions'    => ['timur', 'barat'],
            'budget_max'           => 1500000,
        ]);

        $this->assertDatabaseHas('user_preferences', [
            'id'      => $preference->id,
            'user_id' => $user->id,
        ]);

        $freshPref = UserPreference::find($preference->id);
        $this->assertIsArray($freshPref->preferred_categories);
        $this->assertEquals(['reguler', 'custom'], $freshPref->preferred_categories);
        $this->assertIsArray($freshPref->preferred_regions);
        $this->assertEquals(['timur', 'barat'], $freshPref->preferred_regions);

        // Test user relationship
        $this->assertEquals($user->id, $freshPref->user->id);
        $this->assertEquals($preference->id, $user->userPreference->id);
        $this->assertEquals($preference->id, $user->preference->id);
    }

    public function test_review_creation_and_relations(): void
    {
        $user = User::create([
            'name'     => 'Reviewer User',
            'email'    => 'reviewer@example.com',
            'password' => Hash::make('password'),
            'role'     => 'user',
            'status'   => 'active',
        ]);

        $agentUser = User::create([
            'name'     => 'Review Agent User',
            'email'    => 'reviewagent@example.com',
            'password' => Hash::make('password'),
            'role'     => 'agent',
            'status'   => 'active',
        ]);

        $agent = Agent::create([
            'user_id'             => $agentUser->id,
            'agent_name'          => 'Review Agent Operator',
            'address'             => 'Jl. Bypass Ngurah Rai No. 88',
            'phone_number'        => '08123456789',
            'vehicle_count'       => 2,
            'bank_name'           => 'BCA',
            'bank_account_number' => '11223344',
            'bank_account_name'   => 'Review Agent',
        ]);

        $package = Package::create([
            'agent_id'      => $agent->id,
            'name'          => 'Paket Review Test',
            'slug'          => 'paket-review-test-' . Str::random(5),
            'description'   => 'Deskripsi paket review.',
            'category'      => 'reguler',
            'region'        => 'timur',
            'price_per_pax' => 500000,
            'duration_days' => 1,
            'max_guests'    => 10,
            'is_active'     => true,
        ]);

        $booking = Booking::create([
            'booking_code' => 'BOOK-REV-101',
            'user_id'      => $user->id,
            'package_id'   => $package->id,
            'guest_count'  => 1,
            'total_price'  => 500000,
            'status'       => 'selesai',
        ]);

        $review = Review::create([
            'booking_id' => $booking->id,
            'user_id'    => $user->id,
            'package_id' => $package->id,
            'rating'     => 5,
            'comment'    => 'Perjalanan luar biasa dan memuaskan!',
        ]);

        $this->assertDatabaseHas('reviews', [
            'id'         => $review->id,
            'booking_id' => $booking->id,
            'rating'     => 5,
        ]);

        // Test relations
        $this->assertEquals($booking->id, $review->booking->id);
        $this->assertEquals($user->id, $review->user->id);
        $this->assertEquals($package->id, $review->package->id);

        $this->assertEquals($review->id, $booking->fresh()->review->id);
        $this->assertTrue($package->fresh()->reviews->contains($review));
        $this->assertTrue($user->fresh()->reviews->contains($review));
    }
}
