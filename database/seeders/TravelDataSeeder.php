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
        // 1. Pastikan Akun Agen Tersedia
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
                'description'         => 'Penyedia layanan paket wisata terpercaya di Bali dengan armada terlengkap dan pemandu berlisensi.',
                'address'             => 'Jl. By Pass Ngurah Rai No. 128, Sanur, Denpasar, Bali',
                'phone_number'        => '081234567890',
                'vehicle_count'       => 8,
                'nib_number'          => '9120003456781',
                'bank_name'           => 'Bank Central Asia (BCA)',
                'bank_account_number' => '8271092831',
                'bank_account_name'   => 'PT Dewata Tour & Travel',
            ]
        );

        // 2. Pastikan destinasi milik agen terdaftar
        Destination::whereNull('agent_id')->update(['agent_id' => $agent->id]);

        // 3. Seeding Paket Wisata Unggulan dengan Pemetaan Destinasi Statis yang Logis
        $packagesData = [
            [
                'name'          => 'Eksplorasi Nusa Penida Timur & Snorkeling',
                'description'   => 'Jelajahi keajaiban pulau Nusa Penida. Nikmati keindahan Diamond Beach, Kelingking Beach, dan berenang di jernihnya perairan Crystal Bay.',
                'category'      => 'reguler',
                'region'        => 'timur',
                'price_per_pax' => 750000,
                'duration_days' => 1,
                'max_guests'    => 12,
                'avg_rating'    => 4.9,
                'cover_image'   => 'https://images.unsplash.com/photo-1577717903315-1691ae25ab3f?auto=format&fit=crop&w=800&q=80',
                'is_active'     => true,
                'destinations'  => [
                    'Diamond Beach Nusa Penida',
                    'Kelingking Beach Nusa Penida',
                    'Crystal Bay Nusa Penida',
                ],
            ],
            [
                'name'          => 'Ubud Heritage & Sacred Waterfall Tour',
                'description'   => 'Wisata budaya dan alam khas Ubud. Menyusuri Monkey Forest, merasakan kesegaran Tirta Empul, dan bersantai di Tegalalang Rice Terrace.',
                'category'      => 'reguler',
                'region'        => 'timur',
                'price_per_pax' => 450000,
                'duration_days' => 1,
                'max_guests'    => 15,
                'avg_rating'    => 4.8,
                'cover_image'   => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80',
                'is_active'     => true,
                'destinations'  => [
                    'Monkey Forest Ubud',
                    'Tirta Empul Temple',
                    'Tegalalang Rice Terrace',
                ],
            ],
            [
                'name'          => 'Mount Batur Sunrise Trekking & Hot Spring',
                'description'   => 'Pengalaman mendaki Gunung Batur dini hari untuk menyaksikan magisnya matahari terbit dari puncak kawah, dilanjutkan berendam di pemandian air panas alami Kintamani.',
                'category'      => 'custom',
                'region'        => 'timur',
                'price_per_pax' => 550000,
                'duration_days' => 1,
                'max_guests'    => 10,
                'avg_rating'    => 5.0,
                'cover_image'   => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80',
                'is_active'     => true,
                'destinations'  => [
                    'Mount Batur Caldera',
                    'Kintamani Natural Hot Spring',
                ],
            ],
            [
                'name'          => 'Sunset Cliff Uluwatu & Tari Kecak Romance',
                'description'   => 'Menikmati panorama tebing laut Uluwatu saat senja, menyaksikan pertunjukan epik Tari Kecak berlatar matahari terbenam, diakhiri makan malam seafood romantis di Jimbaran.',
                'category'      => 'reguler',
                'region'        => 'barat',
                'price_per_pax' => 600000,
                'duration_days' => 1,
                'max_guests'    => 20,
                'avg_rating'    => 4.7,
                'cover_image'   => 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=800&q=80',
                'is_active'     => true,
                'destinations'  => [
                    'Uluwatu Cliff & Temple',
                    'Jimbaran Bay Seafood Beach',
                ],
            ],
            [
                'name'          => 'Tanah Lot & Pura Ulun Danu Beratan Explorer',
                'description'   => 'Jelajahi keindahan Bali Barat mulai dari Pura Ulun Danu Beratan di Bedugul yang sejuk berkabut hingga pemandangan sunset siluet legendaris Pura Tanah Lot di tepi samudra.',
                'category'      => 'custom',
                'region'        => 'barat',
                'price_per_pax' => 520000,
                'duration_days' => 1,
                'max_guests'    => 15,
                'avg_rating'    => 4.9,
                'cover_image'   => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=80',
                'is_active'     => true,
                'destinations'  => [
                    'Pura Ulun Danu Beratan',
                    'Tanah Lot Sunset',
                ],
            ],
            [
                'name'          => 'Petualangan Eksklusif Bali Barat 3D2N',
                'description'   => 'Paket liburan private 3 hari 2 malam menelusuri keasrian Taman Nasional Bali Barat dan Pulau Menjangan untuk snorkeling & diving kelas dunia.',
                'category'      => 'custom',
                'region'        => 'barat',
                'price_per_pax' => 2800000,
                'duration_days' => 3,
                'max_guests'    => 8,
                'avg_rating'    => 5.0,
                'cover_image'   => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                'is_active'     => true,
                'destinations'  => [
                    'Taman Nasional Bali Barat',
                    'Pulau Menjangan',
                ],
            ],
        ];

        foreach ($packagesData as $pkg) {
            $destinationNames = $pkg['destinations'] ?? [];
            unset($pkg['destinations']);

            $slug = Str::slug($pkg['name']);
            $package = Package::updateOrCreate(
                ['slug' => $slug],
                array_merge($pkg, [
                    'agent_id' => $agent->id,
                    'slug'     => $slug,
                ])
            );

            // Hubungkan destinasi secara statis sesuai urutan kunjungan (visit_order)
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

            // 4. Seeding Jadwal Perjalanan (Package Schedules)
            $package->schedules()->delete();

            // Jadwal 1: 2 hari lagi (H-2: tidak lolos validasi H-3)
            $package->schedules()->create([
                'start_date' => now()->addDays(2)->toDateString(),
                'end_date'   => now()->addDays(2 + $pkg['duration_days'] - 1)->toDateString(),
                'quota'      => 5,
            ]);

            // Jadwal 2: 5 hari lagi (Memenuhi syarat H-3)
            $package->schedules()->create([
                'start_date' => now()->addDays(5)->toDateString(),
                'end_date'   => now()->addDays(5 + $pkg['duration_days'] - 1)->toDateString(),
                'quota'      => 10,
            ]);

            // Jadwal 3: 12 hari lagi (Memenuhi syarat H-3)
            $package->schedules()->create([
                'start_date' => now()->addDays(12)->toDateString(),
                'end_date'   => now()->addDays(12 + $pkg['duration_days'] - 1)->toDateString(),
                'quota'      => 15,
            ]);

            // Jadwal 4: 25 hari lagi (Memenuhi syarat H-3)
            $package->schedules()->create([
                'start_date' => now()->addDays(25)->toDateString(),
                'end_date'   => now()->addDays(25 + $pkg['duration_days'] - 1)->toDateString(),
                'quota'      => 20,
            ]);
        }

        // Pastikan semua paket dari PackageSeeder juga memiliki agent_id & jadwal aktif
        $otherPackages = Package::whereNull('agent_id')->get();
        foreach ($otherPackages as $otherPkg) {
            $otherPkg->update(['agent_id' => $agent->id]);

            if ($otherPkg->schedules()->count() === 0) {
                $otherPkg->schedules()->create([
                    'start_date' => now()->addDays(6)->toDateString(),
                    'end_date'   => now()->addDays(6 + $otherPkg->duration_days - 1)->toDateString(),
                    'quota'      => 12,
                ]);
                $otherPkg->schedules()->create([
                    'start_date' => now()->addDays(14)->toDateString(),
                    'end_date'   => now()->addDays(14 + $otherPkg->duration_days - 1)->toDateString(),
                    'quota'      => 15,
                ]);
            }
        }
    }
}
