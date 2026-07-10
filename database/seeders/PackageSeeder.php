<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use Illuminate\Support\Str;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Ubud Cultural Tour',
                'description' => 'Jelajahi keindahan budaya Ubud selama 1 hari penuh, termasuk Monkey Forest dan Tirta Empul.',
                'price_per_pax' => 350000,
                'duration_days' => 1,
                'max_guests' => 15,
                'cover_image' => 'packages/dummy_temple.png',
                'is_active' => true,
            ],
            [
                'name' => 'Nusa Penida Island Hopping',
                'description' => 'Nikmati keindahan Kelingking Beach, Broken Beach, dan snorkeling di Crystal Bay selama satu hari.',
                'price_per_pax' => 750000,
                'duration_days' => 1,
                'max_guests' => 10,
                'cover_image' => 'packages/dummy_beach.png',
                'is_active' => true,
            ],
            [
                'name' => 'Mount Batur Sunrise Trekking',
                'description' => 'Mendaki Gunung Batur dan nikmati pemandangan matahari terbit terbaik di Bali, lengkap dengan sarapan.',
                'price_per_pax' => 450000,
                'duration_days' => 1,
                'max_guests' => 20,
                'cover_image' => 'packages/dummy_mountain.png',
                'is_active' => true,
            ],
            [
                'name' => 'Gili Islands Gateway 3D2N',
                'description' => 'Liburan tropis 3 hari 2 malam di Gili Trawangan, Gili Meno, dan Gili Air dengan aktivitas snorkeling.',
                'price_per_pax' => 2100000,
                'duration_days' => 3,
                'max_guests' => 12,
                'cover_image' => 'packages/dummy_beach.png',
                'is_active' => true,
            ],
            [
                'name' => 'Bali Waterfall Explorer',
                'description' => 'Petualangan mencari air terjun tersembunyi di Bali utara seperti Sekumpul dan Gitgit.',
                'price_per_pax' => 500000,
                'duration_days' => 1,
                'max_guests' => 8,
                'cover_image' => 'packages/dummy_temple.png', // Or something else
                'is_active' => true,
            ],
            [
                'name' => 'Uluwatu Temple & Kecak Dance',
                'description' => 'Kunjungi Pura Uluwatu di tebing laut dan nikmati pertunjukan Tari Kecak dengan latar sunset.',
                'price_per_pax' => 400000,
                'duration_days' => 1,
                'max_guests' => 30,
                'cover_image' => 'packages/dummy_temple.png',
                'is_active' => true,
            ],
            [
                'name' => 'Lombok Rinjani Trekking 4D3N',
                'description' => 'Tantang diri Anda mendaki Gunung Rinjani selama 4 hari 3 malam bersama pemandu profesional.',
                'price_per_pax' => 3500000,
                'duration_days' => 4,
                'max_guests' => 10,
                'cover_image' => 'packages/dummy_mountain.png',
                'is_active' => true,
            ],
            [
                'name' => 'Bali Safari & Marine Park',
                'description' => 'Paket keluarga 1 hari penuh menikmati berbagai satwa liar di habitat alaminya.',
                'price_per_pax' => 850000,
                'duration_days' => 1,
                'max_guests' => 50,
                'cover_image' => 'packages/dummy_beach.png', // Placeholder
                'is_active' => true,
            ],
            [
                'name' => 'Kintamani Cycling Tour',
                'description' => 'Bersepeda menuruni bukit Kintamani melewati pedesaan, persawahan, dan perkebunan kopi tradisional.',
                'price_per_pax' => 420000,
                'duration_days' => 1,
                'max_guests' => 15,
                'cover_image' => 'packages/dummy_mountain.png',
                'is_active' => true,
            ],
            [
                'name' => 'Seminyak Luxury Sunset Cruise',
                'description' => 'Berlayar menikmati sunset Bali dengan makan malam mewah di atas kapal pesiar.',
                'price_per_pax' => 1250000,
                'duration_days' => 1,
                'max_guests' => 25,
                'cover_image' => 'packages/dummy_beach.png',
                'is_active' => true,
            ],
            [
                'name' => 'Komodo Island Adventure 3D2N',
                'description' => 'Jelajahi Taman Nasional Komodo, Pink Beach, dan Pulau Padar selama 3 hari 2 malam.',
                'price_per_pax' => 4500000,
                'duration_days' => 3,
                'max_guests' => 20,
                'cover_image' => 'packages/dummy_mountain.png',
                'is_active' => true,
            ]
        ];

        foreach ($packages as $pkgData) {
            $pkgData['slug'] = Str::slug($pkgData['name']);
            // Randomly generate avg_rating for better visual testing
            $pkgData['avg_rating'] = rand(40, 50) / 10; // 4.0 - 5.0

            $package = Package::updateOrCreate(
                ['slug' => $pkgData['slug']],
                $pkgData
            );

            // Attach destinations (assuming destinations 1,2,3 exist, maybe we should just attach 1, 2)
            $package->destinations()->attach([
                1 => ['visit_order' => 1],
                2 => ['visit_order' => 2],
            ]);
        }
    }
}
