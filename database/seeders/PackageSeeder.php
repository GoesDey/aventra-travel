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
                'description'   => 'Explore the cultural heart of Ubud in one full day, visiting the Sacred Monkey Forest, Tegalalang Rice Terrace, and Tirta Empul Temple.',
                'category'      => 'reguler',
                'region'        => 'selatan',
                'price_per_pax' => 350000,
                'duration_days' => 1,
                'max_guests'    => 15,
                'is_active'     => true,
                'destinations'  => [
                    'Monkey Forest Ubud',
                    'Tegalalang Rice Terrace',
                    'Tirta Empul Temple',
                ],
            ],
            [
                'name'          => 'Nusa Penida Island Hopping',
                'description'   => 'Discover the iconic cliffs of Kelingking Beach, the white sands of Diamond Beach, and vibrant snorkeling at Crystal Bay in one unforgettable day.',
                'category'      => 'reguler',
                'region'        => 'timur',
                'price_per_pax' => 750000,
                'duration_days' => 1,
                'max_guests'    => 10,
                'is_active'     => true,
                'destinations'  => [
                    'Kelingking Beach Nusa Penida',
                    'Diamond Beach Nusa Penida',
                    'Crystal Bay Nusa Penida',
                ],
            ],
            [
                'name'          => 'Mount Batur Sunrise Trekking',
                'description'   => 'Hike Mount Batur before dawn for a breathtaking sunrise above the clouds, followed by a relaxing soak at Kintamani Natural Hot Spring.',
                'category'      => 'reguler',
                'region'        => 'utara',
                'price_per_pax' => 450000,
                'duration_days' => 1,
                'max_guests'    => 20,
                'is_active'     => true,
                'destinations'  => [
                    'Mount Batur Caldera',
                    'Kintamani Natural Hot Spring',
                ],
            ],
            [
                'name'          => 'East Bali Heritage & Water Palace',
                'description'   => 'Journey through East Bali’s sacred landmarks: Besakih Mother Temple on Mount Agung, Tirta Gangga Water Palace, and the Gates of Heaven at Lempuyang Temple.',
                'category'      => 'reguler',
                'region'        => 'timur',
                'price_per_pax' => 650000,
                'duration_days' => 1,
                'max_guests'    => 15,
                'is_active'     => true,
                'destinations'  => [
                    'Besakih Mother Temple',
                    'Tirta Gangga Water Palace',
                    'Lempuyang Temple',
                ],
            ],
            [
                'name'          => 'Bali Waterfall Explorer',
                'description'   => 'Chase North Bali’s most spectacular hidden waterfalls, trekking through lush valleys to Gitgit Waterfall and the towering twin cascades of Sekumpul.',
                'category'      => 'reguler',
                'region'        => 'utara',
                'price_per_pax' => 500000,
                'duration_days' => 1,
                'max_guests'    => 8,
                'is_active'     => true,
                'destinations'  => [
                    'Gitgit Waterfall',
                    'Sekumpul Waterfall',
                ],
            ],
            [
                'name'          => 'Uluwatu Temple & Kecak Dance',
                'description'   => 'Visit the clifftop Uluwatu Temple overlooking the Indian Ocean, watch the traditional sunset Kecak fire dance, and enjoy a beachfront seafood dinner in Jimbaran.',
                'category'      => 'reguler',
                'region'        => 'selatan',
                'price_per_pax' => 400000,
                'duration_days' => 1,
                'max_guests'    => 30,
                'is_active'     => true,
                'destinations'  => [
                    'Uluwatu Cliff & Temple',
                    'Jimbaran Bay Seafood Beach',
                ],
            ],
            [
                'name'          => 'North Bali Dolphin & Hot Springs',
                'description'   => 'Spot wild dolphins at sunrise off Lovina Beach, cool off at Gitgit Waterfall, and unwind in the volcanic pools of Banjar Hot Springs.',
                'category'      => 'custom',
                'region'        => 'utara',
                'price_per_pax' => 550000,
                'duration_days' => 1,
                'max_guests'    => 10,
                'is_active'     => true,
                'destinations'  => [
                    'Lovina Beach',
                    'Gitgit Waterfall',
                    'Banjar Hot Springs',
                ],
            ],
            [
                'name'          => 'Bali Safari & Marine Park',
                'description'   => 'A full-day family adventure encountering exotic wildlife up close in open natural habitats with interactive safari tram rides and cultural shows.',
                'category'      => 'reguler',
                'region'        => 'selatan',
                'price_per_pax' => 850000,
                'duration_days' => 1,
                'max_guests'    => 50,
                'is_active'     => true,
                'destinations'  => [
                    'Bali Safari & Marine Park',
                ],
            ],
            [
                'name'          => 'Kintamani Cycling Tour',
                'description'   => 'Coast downhill by bicycle from the Kintamani highlands through quiet Balinese villages, Tegalalang rice paddies, and Tirta Empul Temple.',
                'category'      => 'reguler',
                'region'        => 'utara',
                'price_per_pax' => 420000,
                'duration_days' => 1,
                'max_guests'    => 15,
                'is_active'     => true,
                'destinations'  => [
                    'Mount Batur Caldera',
                    'Tirta Empul Temple',
                    'Tegalalang Rice Terrace',
                ],
            ],
            [
                'name'          => 'Seminyak Luxury Sunset Cruise',
                'description'   => 'Sail into the golden Bali sunset departing from Benoa Harbor, followed by an elegant beachfront dining experience along Jimbaran Bay.',
                'category'      => 'custom',
                'region'        => 'selatan',
                'price_per_pax' => 1250000,
                'duration_days' => 1,
                'max_guests'    => 25,
                'is_active'     => true,
                'destinations'  => [
                    'Benoa Sunset Harbor',
                    'Jimbaran Bay Seafood Beach',
                ],
            ],
            [
                'name'          => 'South Bali Sunset & Cultural Highlights',
                'description'   => 'Experience the best of South Bali: the monumental Garuda Wisnu Kencana Cultural Park, the turquoise cliffs of Melasti Beach, and sunset at Uluwatu Temple.',
                'category'      => 'custom',
                'region'        => 'selatan',
                'price_per_pax' => 500000,
                'duration_days' => 1,
                'max_guests'    => 20,
                'is_active'     => true,
                'destinations'  => [
                    'GWK Cultural Park',
                    'Melasti Beach',
                    'Uluwatu Cliff & Temple',
                ],
            ],
        ];

        $unsplashBySlug = [
            'ubud-cultural-tour'                    => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?q=80&w=800&auto=format&fit=crop',
            'nusa-penida-island-hopping'            => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?q=80&w=800&auto=format&fit=crop',
            'mount-batur-sunrise-trekking'          => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=800&auto=format&fit=crop',
            'east-bali-heritage-water-palace'       => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?q=80&w=800&auto=format&fit=crop',
            'bali-waterfall-explorer'               => 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=800&auto=format&fit=crop',
            'uluwatu-temple-kecak-dance'            => 'https://images.unsplash.com/photo-1555400038-63f5ba517a47?q=80&w=800&auto=format&fit=crop',
            'north-bali-dolphin-hot-springs'        => 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=800&auto=format&fit=crop',
            'bali-safari-marine-park'               => 'https://images.unsplash.com/photo-1577717903315-1691ae25ab3f?q=80&w=800&auto=format&fit=crop',
            'kintamani-cycling-tour'                => 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=800&auto=format&fit=crop',
            'seminyak-luxury-sunset-cruise'         => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=800&auto=format&fit=crop',
            'south-bali-sunset-cultural-highlights' => 'https://images.unsplash.com/photo-1555400038-63f5ba517a47?q=80&w=800&auto=format&fit=crop',
        ];

        foreach ($packages as $pkgData) {
            $destinationNames = $pkgData['destinations'] ?? [];
            unset($pkgData['destinations']);

            $slug = Str::slug($pkgData['name']);
            $pkgData['slug']        = $slug;
            $pkgData['cover_image'] = $unsplashBySlug[$slug] ?? "images/packages/{$slug}.jpg";
            $pkgData['avg_rating']  = 0;

            $package = Package::updateOrCreate(
                ['slug' => $slug],
                $pkgData
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
    }
}
