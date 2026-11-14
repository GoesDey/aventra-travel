<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Destination;
use App\Models\Package;
use Illuminate\Support\Str;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name'          => 'Ubud Cultural Tour',
                'description'   => 'Jelajahi keindahan budaya Ubud selama 1 hari penuh, termasuk Monkey Forest, Tirta Empul, dan Tegalalang Rice Terrace.',
                'category'      => 'reguler',
                'region'        => 'timur',
                'price_per_pax' => 350000,
                'duration_days' => 1,
                'max_guests'    => 15,
                'cover_image'   => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?q=80&w=800&auto=format&fit=crop',
                'is_active'     => true,
                'destinations'  => [
                    'Monkey Forest Ubud',
                    'Tegalalang Rice Terrace',
                    'Tirta Empul Temple',
                ],
            ],
            [
                'name'          => 'Nusa Penida Island Hopping',
                'description'   => 'Nikmati keindahan Kelingking Beach, Diamond Beach, dan snorkeling di Crystal Bay selama satu hari.',
                'category'      => 'reguler',
                'region'        => 'timur',
                'price_per_pax' => 750000,
                'duration_days' => 1,
                'max_guests'    => 10,
                'cover_image'   => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?q=80&w=800&auto=format&fit=crop',
                'is_active'     => true,
                'destinations'  => [
                    'Kelingking Beach Nusa Penida',
                    'Diamond Beach Nusa Penida',
                    'Crystal Bay Nusa Penida',
                ],
            ],
            [
                'name'          => 'Mount Batur Sunrise Trekking',
                'description'   => 'Mendaki Gunung Batur dan nikmati pemandangan matahari terbit terbaik di Bali, dilanjutkan berendam di pemandian air panas Kintamani.',
                'category'      => 'reguler',
                'region'        => 'timur',
                'price_per_pax' => 450000,
                'duration_days' => 1,
                'max_guests'    => 20,
                'cover_image'   => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=800&auto=format&fit=crop',
                'is_active'     => true,
                'destinations'  => [
                    'Mount Batur Caldera',
                    'Kintamani Natural Hot Spring',
                ],
            ],
            [
                'name'          => 'Gili Islands Gateway 3D2N',
                'description'   => 'Liburan tropis 3 hari 2 malam di Gili Trawangan dan Gili Air dengan aktivitas island hopping dan snorkeling.',
                'category'      => 'reguler',
                'region'        => 'lainnya',
                'price_per_pax' => 2100000,
                'duration_days' => 3,
                'max_guests'    => 12,
                'cover_image'   => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=800&auto=format&fit=crop',
                'is_active'     => true,
                'destinations'  => [
                    'Gili Trawangan',
                    'Gili Air',
                ],
            ],
            [
                'name'          => 'Bali Waterfall Explorer',
                'description'   => 'Petualangan mencari air terjun tersembunyi di Bali utara seperti Sekumpul dan Gitgit.',
                'category'      => 'reguler',
                'region'        => 'barat',
                'price_per_pax' => 500000,
                'duration_days' => 1,
                'max_guests'    => 8,
                'cover_image'   => 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=800&auto=format&fit=crop',
                'is_active'     => true,
                'destinations'  => [
                    'Gitgit Waterfall',
                    'Sekumpul Waterfall',
                ],
            ],
            [
                'name'          => 'Uluwatu Temple & Kecak Dance',
                'description'   => 'Kunjungi Pura Uluwatu di tebing laut dan nikmati pertunjukan Tari Kecak dengan latar sunset serta makan malam di Jimbaran.',
                'category'      => 'reguler',
                'region'        => 'barat',
                'price_per_pax' => 400000,
                'duration_days' => 1,
                'max_guests'    => 30,
                'cover_image'   => 'https://images.unsplash.com/photo-1555400038-63f5ba517a47?q=80&w=800&auto=format&fit=crop',
                'is_active'     => true,
                'destinations'  => [
                    'Uluwatu Cliff & Temple',
                    'Jimbaran Bay Seafood Beach',
                ],
            ],
            [
                'name'          => 'Lombok Rinjani Trekking 4D3N',
                'description'   => 'Tantang diri Anda mendaki Gunung Rinjani selama 4 hari 3 malam bersama pemandu profesional.',
                'category'      => 'custom',
                'region'        => 'lainnya',
                'price_per_pax' => 3500000,
                'duration_days' => 4,
                'max_guests'    => 10,
                'cover_image'   => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=800&auto=format&fit=crop',
                'is_active'     => true,
                'destinations'  => [
                    'Taman Nasional Gunung Rinjani',
                ],
            ],
            [
                'name'          => 'Bali Safari & Marine Park',
                'description'   => 'Paket keluarga 1 hari penuh menikmati berbagai satwa liar di habitat alaminya.',
                'category'      => 'reguler',
                'region'        => 'timur',
                'price_per_pax' => 850000,
                'duration_days' => 1,
                'max_guests'    => 50,
                'cover_image'   => 'https://images.unsplash.com/photo-1577717903315-1691ae25ab3f?q=80&w=800&auto=format&fit=crop',
                'is_active'     => true,
                'destinations'  => [
                    'Bali Safari & Marine Park',
                ],
            ],
            [
                'name'          => 'Kintamani Cycling Tour',
                'description'   => 'Bersepeda menuruni bukit Kintamani melewati pedesaan, persawahan Tegalalang, dan Pura Tirta Empul.',
                'category'      => 'reguler',
                'region'        => 'timur',
                'price_per_pax' => 420000,
                'duration_days' => 1,
                'max_guests'    => 15,
                'cover_image'   => 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=800&auto=format&fit=crop',
                'is_active'     => true,
                'destinations'  => [
                    'Mount Batur Caldera',
                    'Tirta Empul Temple',
                    'Tegalalang Rice Terrace',
                ],
            ],
            [
                'name'          => 'Seminyak Luxury Sunset Cruise',
                'description'   => 'Berlayar menikmati sunset Bali dari Pelabuhan Benoa dilanjutkan makan malam mewah di tepi Pantai Jimbaran.',
                'category'      => 'custom',
                'region'        => 'barat',
                'price_per_pax' => 1250000,
                'duration_days' => 1,
                'max_guests'    => 25,
                'cover_image'   => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=800&auto=format&fit=crop',
                'is_active'     => true,
                'destinations'  => [
                    'Pelabuhan Benoa Sunset Harbor',
                    'Jimbaran Bay Seafood Beach',
                ],
            ],
            [
                'name'          => 'Komodo Island Adventure 3D2N',
                'description'   => 'Jelajahi Taman Nasional Komodo, Pink Beach, dan Pulau Padar selama 3 hari 2 malam.',
                'category'      => 'custom',
                'region'        => 'lainnya',
                'price_per_pax' => 4500000,
                'duration_days' => 3,
                'max_guests'    => 20,
                'cover_image'   => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?q=80&w=800&auto=format&fit=crop',
                'is_active'     => true,
                'destinations'  => [
                    'Pulau Padar & Taman Nasional Komodo',
                    'Pink Beach Komodo',
                ],
            ],
        ];

        // Pemetaan region resmi (utara, selatan, timur, barat) berdasarkan lokasi paket
        $regionMap = [
            'ubud-cultural-tour'            => 'selatan',
            'nusa-penida-island-hopping'    => 'timur',
            'mount-batur-sunrise-trekking'  => 'utara',
            'gili-islands-gateway-3d2n'     => 'timur',
            'bali-waterfall-explorer'       => 'utara',
            'uluwatu-temple-kecak-dance'    => 'selatan',
            'lombok-rinjani-trekking-4d3n'  => 'timur',
            'bali-safari-marine-park'       => 'selatan',
            'kintamani-cycling-tour'        => 'utara',
            'seminyak-luxury-sunset-cruise' => 'selatan',
            'komodo-island-adventure-3d2n'  => 'timur',
        ];

        foreach ($packages as $pkgData) {
            $destinationNames = $pkgData['destinations'] ?? [];
            unset($pkgData['destinations']);

            $pkgData['slug'] = Str::slug($pkgData['name']);
            $pkgData['region'] = $regionMap[$pkgData['slug']] ?? ($pkgData['region'] ?? 'selatan');
            if ($pkgData['region'] === 'lainnya') {
                $pkgData['region'] = 'selatan';
            }
            $pkgData['avg_rating'] = rand(44, 50) / 10;

            $package = Package::updateOrCreate(
                ['slug' => $pkgData['slug']],
                $pkgData
            );

            // Pemetaan statis berdasarkan urutan kunjungan (visit_order)
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
    }
}
