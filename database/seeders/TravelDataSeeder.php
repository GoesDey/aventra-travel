<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Agent;
use App\Models\Destination;
use App\Models\Package;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TravelDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Default Official Agent Exists
        $agentUser = User::firstOrCreate(
            ['email' => 'agent@trihitatour.com'],
            [
                'name'     => 'Dewata Tour Operator',
                'password' => Hash::make('password'),
                'role'     => 'agent',
                'status'   => 'active',
            ]
        );

        $agent = Agent::firstOrCreate(
            ['user_id' => $agentUser->id],
            [
                'agent_name'          => 'PT Dewata Tour & Travel',
                'description'         => 'Trusted licensed tour operator in Bali with modern charter vehicles and experienced English-speaking guides.',
                'address'             => 'Jl. By Pass Ngurah Rai No. 128, Sanur, Denpasar, Bali',
                'operating_regions'   => ['utara', 'selatan', 'timur', 'barat'],
                'latitude'            => -8.6705000,
                'longitude'           => 115.2625000,
                'phone_number'        => '081234567890',
                'vehicle_count'       => 8,
                'nib_number'          => '9120003456781',
                'bank_name'           => 'Bank Central Asia (BCA)',
                'bank_account_number' => '8271092831',
                'bank_account_name'   => 'PT Dewata Tour & Travel',
            ]
        );

        // 2. Ensure all destinations are owned by the active agent
        Destination::whereNull('agent_id')->update(['agent_id' => $agent->id]);

        // 3. Seed Unique West Bali Packages (no duplicates with PackageSeeder)
        $westBaliPackages = [
            [
                'name'          => 'Tanah Lot & Ulun Danu Beratan Explorer',
                'description'   => 'Discover Bali’s iconic water temples, from the misty mountain shores of Ulun Danu Beratan in Bedugul to the dramatic ocean sunset at Tanah Lot.',
                'category'      => 'reguler',
                'region'        => 'barat',
                'price_per_pax' => 520000,
                'duration_days' => 1,
                'max_guests'    => 15,
                'is_active'     => true,
                'destinations'  => [
                    'Ulun Danu Beratan Temple',
                    'Tanah Lot Sunset Temple',
                ],
            ],
            [
                'name'          => 'West Bali & Menjangan Island Adventure 3D2N',
                'description'   => 'A 3-day, 2-night private escape exploring the untouched rainforest of West Bali National Park and world-class coral wall snorkeling at Menjangan Island.',
                'category'      => 'custom',
                'region'        => 'barat',
                'price_per_pax' => 2800000,
                'duration_days' => 3,
                'max_guests'    => 8,
                'is_active'     => true,
                'destinations'  => [
                    'West Bali National Park',
                    'Menjangan Island',
                ],
            ],
        ];

        $westUnsplash = [
            'tanah-lot-ulun-danu-beratan-explorer'      => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=80',
            'west-bali-menjangan-island-adventure-3d2n' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
        ];

        foreach ($westBaliPackages as $pkg) {
            $destinationNames = $pkg['destinations'] ?? [];
            unset($pkg['destinations']);

            $slug = Str::slug($pkg['name']);
            $package = Package::updateOrCreate(
                ['slug' => $slug],
                array_merge($pkg, [
                    'agent_id'    => $agent->id,
                    'slug'        => $slug,
                    'cover_image' => $westUnsplash[$slug] ?? "images/packages/{$slug}.jpg",
                    'avg_rating'  => 0,
                ])
            );

            $syncData = [];
            foreach ($destinationNames as $index => $destName) {
                $dest = Destination::where('slug', Str::slug($destName))->first();
                if ($dest) {
                    $syncData[$dest->id] = ['visit_order' => $index + 1];
                }
            }

            if (!empty($syncData)) {
                $package->destinations()->sync($syncData);
            }
        }

        // 4. Assign default agent to any unowned packages and ensure 4 future schedules (>= H+4) with quota matching max_guests
        $scheduleDayOffsets = [4, 9, 15, 22];

        $allPackages = Package::where('is_active', true)->get();
        foreach ($allPackages as $pkg) {
            if (!$pkg->agent_id) {
                $pkg->update(['agent_id' => $agent->id]);
            }

            foreach ($scheduleDayOffsets as $daysAhead) {
                $startDate = now()->addDays($daysAhead)->toDateString();
                $endDate   = now()->addDays($daysAhead + max(1, (int) $pkg->duration_days) - 1)->toDateString();

                $pkg->schedules()->firstOrCreate(
                    ['start_date' => $startDate],
                    [
                        'end_date' => $endDate,
                        'quota'    => max(1, (int) $pkg->max_guests),
                    ]
                );
            }
        }
    }
}
