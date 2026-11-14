<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Agent;
use App\Models\Destination;
use App\Models\Package;
use App\Models\PackageSchedule;
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

        // 2. Seeding Destinasi dengan Image Path Dummy / Unsplash
        $destinationsData = [
            [
                'name' => 'Monkey Forest Ubud',
                'location' => 'Ubud, Gianyar',
                'image' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80',
                'description' => 'Cagar alam suci dengan ratusan monyet ekor panjang dan pepohonan purba yang rindang.',
            ],
            [
                'name' => 'Tirta Empul Temple',
                'location' => 'Tampaksiring, Gianyar',
                'image' => 'https://images.unsplash.com/photo-1555400038-63f5ba517a47?auto=format&fit=crop&w=800&q=80',
                'description' => 'Pura pemandian suci bersejarah dengan mata air alami untuk ritual penyucian diri (Melukat).',
            ],
            [
                'name' => 'Tanah Lot Sunset',
                'location' => 'Beraban, Tabanan',
                'image' => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=80',
                'description' => 'Pura ikonik di atas bongkahan batu karang besar di tengah samudera dengan sunset memukau.',
            ],
            [
                'name' => 'Uluwatu Cliff & Temple',
                'location' => 'Pecatu, Badung',
                'image' => 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=800&q=80',
                'description' => 'Pura luhur di tebing karang setinggi 70 meter menghadap Samudera Hindia dengan atraksi Tari Kecak.',
            ],
            [
                'name' => 'Kelingking Beach Nusa Penida',
                'location' => 'Nusa Penida, Klungkung',
                'image' => 'https://images.unsplash.com/photo-1577717903315-1691ae25ab3f?auto=format&fit=crop&w=800&q=80',
                'description' => 'Tebing karst megah berbentuk T-Rex dengan pasir putih pantai dan air laut biru toska spektakuler.',
            ],
            [
                'name' => 'Mount Batur Caldera',
                'location' => 'Kintamani, Bangli',
                'image' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80',
                'description' => 'Gunung berapi aktif dengan panorama sunrise menembus kabut awan dan danau Batur yang menyejukkan.',
            ],
        ];

        $createdDestinations = [];
        foreach ($destinationsData as $d) {
            $createdDestinations[] = Destination::updateOrCreate(
                ['slug' => Str::slug($d['name'])],
                [
                    'agent_id'    => $agent->id,
                    'name'        => $d['name'],
                    'location'    => $d['location'],
                    'image_path'  => $d['image'],
                    'description' => $d['description'],
                ]
            );
        }

        // 3. Seeding Paket Wisata dengan Cover Image & Jadwal
        $packagesData = [
            [
                'name'          => 'Eksplorasi Nusa Penida Timur & Snorkeling',
                'description'   => 'Jelajahi keajaiban pulau Nusa Penida bagian timur. Nikmati keindahan Diamond Beach, Atuh Beach, Rumah Pohon Molenteng, dan berenang bersama ikan pari Manta Ray.',
                'category'      => 'reguler',
                'region'        => 'timur',
                'price_per_pax' => 750000,
                'duration_days' => 1,
                'max_guests'    => 12,
                'avg_rating'    => 4.9,
                'cover_image'   => 'https://images.unsplash.com/photo-1577717903315-1691ae25ab3f?auto=format&fit=crop&w=800&q=80',
                'is_active'     => true,
            ],
            [
                'name'          => 'Ubud Heritage & Sacred Waterfall Tour',
                'description'   => 'Wisata budaya dan alam khas Ubud. Menyusuri Monkey Forest, melihat arsitektur Puri Saren Agung, merasakan kesegaran Tirta Empul, dan bersantai di Tegalalang Rice Terrace.',
                'category'      => 'reguler',
                'region'        => 'selatan',
                'price_per_pax' => 450000,
                'duration_days' => 1,
                'max_guests'    => 15,
                'avg_rating'    => 4.8,
                'cover_image'   => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80',
                'is_active'     => true,
            ],
            [
                'name'          => 'Mount Batur Sunrise Trekking & Hot Spring',
                'description'   => 'Pengalaman mendaki Gunung Batur dini hari untuk menyaksikan magisnya matahari terbit dari puncak kawah, dilanjutkan berendam di pemandian air panas alami Kintamani.',
                'category'      => 'custom',
                'region'        => 'utara',
                'price_per_pax' => 550000,
                'duration_days' => 1,
                'max_guests'    => 10,
                'avg_rating'    => 5.0,
                'cover_image'   => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80',
                'is_active'     => true,
            ],
            [
                'name'          => 'Sunset Cliff Uluwatu & Tari Kecak Romance',
                'description'   => 'Menikmati panorama tebing laut Uluwatu saat senja, menyaksikan pertunjukan epik Tari Kecak berlatar matahari terbenam, diakhiri makan malam seafood romantis di Jimbaran.',
                'category'      => 'reguler',
                'region'        => 'selatan',
                'price_per_pax' => 600000,
                'duration_days' => 1,
                'max_guests'    => 20,
                'avg_rating'    => 4.7,
                'cover_image'   => 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=800&q=80',
                'is_active'     => true,
            ],
            [
                'name'          => 'Tanah Lot & Pura Ulun Danu Beratan Explorer',
                'description'   => 'Jelajahi keindahan Bali Barat mulai dari Pura Danau Beratan di Bedugul yang sejuk berkabut hingga pemandangan sunset siluet legendaris Pura Tanah Lot di tepi samudra.',
                'category'      => 'custom',
                'region'        => 'barat',
                'price_per_pax' => 520000,
                'duration_days' => 1,
                'max_guests'    => 15,
                'avg_rating'    => 4.9,
                'cover_image'   => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=80',
                'is_active'     => true,
            ],
            [
                'name'          => 'Petualangan Eksklusif Bali Barat 3D2N',
                'description'   => 'Paket liburan private 3 hari 2 malam menelusuri keasrian Taman Nasional Bali Barat, Pulau Menjangan untuk diving kelas dunia, dan resort tepi hutan rimba.',
                'category'      => 'custom',
                'region'        => 'barat',
                'price_per_pax' => 2800000,
                'duration_days' => 3,
                'max_guests'    => 8,
                'avg_rating'    => 5.0,
                'cover_image'   => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                'is_active'     => true,
            ],
        ];

        foreach ($packagesData as $pkg) {
            $slug = Str::slug($pkg['name']);
            $package = Package::updateOrCreate(
                ['slug' => $slug],
                array_merge($pkg, [
                    'agent_id' => $agent->id,
                    'slug'     => $slug,
                ])
            );

            // Hubungkan destinasi ke paket wisata
            if (!empty($createdDestinations)) {
                $package->destinations()->sync([
                    $createdDestinations[0]->id => ['visit_order' => 1],
                    $createdDestinations[1]->id => ['visit_order' => 2],
                ]);
            }

            // 4. Seeding Jadwal Perjalanan (Package Schedules)
            // Buat beberapa jadwal: H-2 (tidak lolos validasi), H+5, H+10, H+18 (lolos validasi)
            $package->schedules()->delete();

            // Jadwal 1: 2 hari lagi (H-2: Sengaja untuk pengujian validasi H-3 agar tidak tampil)
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
    }
}
