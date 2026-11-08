<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $admin;
    protected User $agentUser;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::create([
            'name'     => 'Super Admin Test',
            'email'    => 'superadmin_access@test.com',
            'password' => Hash::make('password'),
            'role'     => 'super_admin',
            'status'   => 'active',
        ]);

        $this->agentUser = User::create([
            'name'     => 'Agent Test',
            'email'    => 'agent_access@test.com',
            'password' => Hash::make('password'),
            'role'     => 'agent',
            'status'   => 'active',
        ]);

        $this->customer = User::create([
            'name'     => 'Customer Test',
            'email'    => 'customer_access@test.com',
            'password' => Hash::make('password'),
            'role'     => 'user',
            'status'   => 'active',
        ]);
    }

    public function test_super_admin_can_access_all_admin_management_pages(): void
    {
        $routes = [
            route('admin.finance'),
            route('admin.bookings'),
            route('admin.destinations'),
            route('admin.packages'),
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($this->superAdmin)->get($url);
            $response->assertStatus(200);
        }
    }

    public function test_agent_and_user_are_forbidden_from_admin_management_pages(): void
    {
        $routes = [
            route('admin.finance'),
            route('admin.bookings'),
            route('admin.destinations'),
            route('admin.packages'),
        ];

        foreach ($routes as $url) {
            $this->actingAs($this->agentUser)->get($url)->assertStatus(403);
            $this->actingAs($this->customer)->get($url)->assertStatus(403);
        }
    }
}
