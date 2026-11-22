<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Destination;
use Illuminate\Support\Str;

class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        $destinations = [
            // Ubud & Gianyar (South Bali)
            [
                'name'        => 'Monkey Forest Ubud',
                'location'    => 'Ubud, Gianyar',
                'region'      => 'selatan',
                'latitude'    => -8.5186990,
                'longitude'   => 115.2586670,
                'image'       => 'images/destinations/monkey-forest-ubud.jpg',
                'description' => 'Sacred nature reserve home to hundreds of long-tailed macaques and ancient shaded nutmeg trees.',
            ],
            [
                'name'        => 'Tirta Empul Temple',
                'location'    => 'Tampaksiring, Gianyar',
                'region'      => 'selatan',
                'latitude'    => -8.4157300,
                'longitude'   => 115.3153200,
                'image'       => 'images/destinations/tirta-empul-temple.jpg',
                'description' => 'Historic sacred water temple featuring natural holy springs used for traditional Melukat purification rituals.',
            ],
            [
                'name'        => 'Tegalalang Rice Terrace',
                'location'    => 'Tegalalang, Gianyar',
                'region'      => 'selatan',
                'latitude'    => -8.4312000,
                'longitude'   => 115.2792700,
                'image'       => 'images/destinations/tegalalang-rice-terrace.jpg',
                'description' => 'Iconic emerald-green terraced rice paddies showcasing Bali’s traditional Subak irrigation heritage.',
            ],
            [
                'name'        => 'Bali Safari & Marine Park',
                'location'    => 'Gianyar, Bali',
                'region'      => 'selatan',
                'latitude'    => -8.5829000,
                'longitude'   => 115.3446000,
                'image'       => 'images/destinations/bali-safari-marine-park.jpg',
                'description' => 'Bali’s premier wildlife conservation park offering interactive safari journeys across natural habitats.',
            ],

            // Kintamani & Bangli (North Bali)
            [
                'name'        => 'Mount Batur Caldera',
                'location'    => 'Kintamani, Bangli',
                'region'      => 'utara',
                'latitude'    => -8.2422000,
                'longitude'   => 115.3753000,
                'image'       => 'images/destinations/mount-batur-caldera.jpg',
                'description' => 'Active volcano famous for breathtaking sunrise panoramas above the clouds overlooking Lake Batur.',
            ],
            [
                'name'        => 'Kintamani Natural Hot Spring',
                'location'    => 'Toya Bungkah, Kintamani, Bangli',
                'region'      => 'utara',
                'latitude'    => -8.2489000,
                'longitude'   => 115.4001000,
                'image'       => 'images/destinations/kintamani-natural-hot-spring.jpg',
                'description' => 'Geothermal mineral hot springs along the shores of Lake Batur with dramatic Mount Batur views.',
            ],

            // Nusa Penida (East Bali)
            [
                'name'        => 'Kelingking Beach Nusa Penida',
                'location'    => 'Nusa Penida, Klungkung',
                'region'      => 'timur',
                'latitude'    => -8.7518000,
                'longitude'   => 115.4741000,
                'image'       => 'images/destinations/kelingking-beach-nusa-penida.jpg',
                'description' => 'Dramatic T-Rex-shaped coastal limestone cliffs overlooking white sand and turquoise ocean waters.',
            ],
            [
                'name'        => 'Diamond Beach Nusa Penida',
                'location'    => 'Pejukutan, Nusa Penida, Klungkung',
                'region'      => 'timur',
                'latitude'    => -8.7755000,
                'longitude'   => 115.6203000,
                'image'       => 'images/destinations/diamond-beach-nusa-penida.jpg',
                'description' => 'Pristine white-sand cove framed by diamond-shaped limestone formations and a scenic cliffside staircase.',
            ],
            [
                'name'        => 'Crystal Bay Nusa Penida',
                'location'    => 'Sakti, Nusa Penida, Klungkung',
                'region'      => 'timur',
                'latitude'    => -8.7153000,
                'longitude'   => 115.4589000,
                'image'       => 'images/destinations/crystal-bay-nusa-penida.jpg',
                'description' => 'Calm, crystal-clear bay popular for vibrant coral reef snorkeling and seasonal Mola-Mola sightings.',
            ],

            // South Bali (Uluwatu, Jimbaran, Benoa, Kuta, Ungasan)
            [
                'name'        => 'Uluwatu Cliff & Temple',
                'location'    => 'Pecatu, Badung',
                'region'      => 'selatan',
                'latitude'    => -8.8291000,
                'longitude'   => 115.0849000,
                'image'       => 'images/destinations/uluwatu-cliff-temple.jpg',
                'description' => 'Majestic sea temple perched atop a 70-meter cliff facing the Indian Ocean, famous for sunset Kecak fire dances.',
            ],
            [
                'name'        => 'Jimbaran Bay Seafood Beach',
                'location'    => 'Jimbaran, South Kuta, Badung',
                'region'      => 'selatan',
                'latitude'    => -8.7802000,
                'longitude'   => 115.1669000,
                'image'       => 'images/destinations/jimbaran-bay-seafood-beach.jpg',
                'description' => 'Tranquil bay renowned for beachfront grilled seafood dinners under golden sunset skies.',
            ],
            [
                'name'        => 'Benoa Sunset Harbor',
                'location'    => 'Benoa, South Denpasar',
                'region'      => 'selatan',
                'latitude'    => -8.7429000,
                'longitude'   => 115.2128000,
                'image'       => 'images/destinations/benoa-sunset-harbor.jpg',
                'description' => 'Departure marina for luxury sunset dinner cruises sailing across the calm waters of South Bali.',
            ],
            [
                'name'        => 'Kuta Beach',
                'location'    => 'Kuta, Badung',
                'region'      => 'selatan',
                'latitude'    => -8.7185000,
                'longitude'   => 115.1686000,
                'image'       => 'images/destinations/kuta-beach.jpg',
                'description' => 'Legendary white-sand beach celebrated for beginner-friendly surf breaks and vibrant golden sunsets.',
            ],
            [
                'name'        => 'GWK Cultural Park',
                'location'    => 'Ungasan, South Kuta, Badung',
                'region'      => 'selatan',
                'latitude'    => -8.8104000,
                'longitude'   => 115.1676000,
                'image'       => 'images/destinations/gwk-cultural-park.jpg',
                'description' => 'Monumental cultural park featuring the towering 121-meter Garuda Wisnu Kencana statue and daily Balinese performances.',
            ],
            [
                'name'        => 'Melasti Beach',
                'location'    => 'Ungasan, South Kuta, Badung',
                'region'      => 'selatan',
                'latitude'    => -8.8482000,
                'longitude'   => 115.1601000,
                'image'       => 'images/destinations/melasti-beach.jpg',
                'description' => 'Scenic white-sand beach tucked beneath towering winding limestone cliffs with crystal-clear turquoise waters.',
            ],

            // East Bali (Karangasem)
            [
                'name'        => 'Besakih Mother Temple',
                'location'    => 'Rendang, Karangasem',
                'region'      => 'timur',
                'latitude'    => -8.3740000,
                'longitude'   => 115.4509000,
                'image'       => 'images/destinations/besakih-mother-temple.jpg',
                'description' => 'Bali’s largest and holiest Hindu temple complex situated on the majestic slopes of Mount Agung.',
            ],
            [
                'name'        => 'Lempuyang Temple',
                'location'    => 'Abang, Karangasem',
                'region'      => 'timur',
                'latitude'    => -8.3905000,
                'longitude'   => 115.6294000,
                'image'       => 'images/destinations/lempuyang-temple.jpg',
                'description' => 'Ancient hilltop sanctuary home to the iconic Gates of Heaven framed by Mount Agung in the background.',
            ],
            [
                'name'        => 'Tirta Gangga Water Palace',
                'location'    => 'Ababi, Karangasem',
                'region'      => 'timur',
                'latitude'    => -8.4122000,
                'longitude'   => 115.5873000,
                'image'       => 'images/destinations/tirta-gangga-water-palace.jpg',
                'description' => 'Former royal water palace of Karangasem adorned with tiered fountains, stone carvings, and koi-filled stepping ponds.',
            ],

            // North Bali (Bedugul, Buleleng, Lovina)
            [
                'name'        => 'Ulun Danu Beratan Temple',
                'location'    => 'Candikuning, Bedugul, Tabanan',
                'region'      => 'utara',
                'latitude'    => -8.2751000,
                'longitude'   => 115.1668000,
                'image'       => 'images/destinations/ulun-danu-beratan-temple.jpg',
                'description' => 'Iconic lakeside water temple floating on serene Lake Beratan amidst cool Bedugul mountain air.',
            ],
            [
                'name'        => 'Sekumpul Waterfall',
                'location'    => 'Sawan, Buleleng',
                'region'      => 'utara',
                'latitude'    => -8.1763000,
                'longitude'   => 115.1827000,
                'image'       => 'images/destinations/sekumpul-waterfall.jpg',
                'description' => 'Stunning cluster of towering twin waterfalls hidden deep inside a lush tropical valley in North Bali.',
            ],
            [
                'name'        => 'Gitgit Waterfall',
                'location'    => 'Sukasada, Buleleng',
                'region'      => 'utara',
                'latitude'    => -8.1917000,
                'longitude'   => 115.1342000,
                'image'       => 'images/destinations/gitgit-waterfall.jpg',
                'description' => 'Easily accessible multi-tiered waterfall surrounded by tropical foliage and natural rocky swimming pools.',
            ],
            [
                'name'        => 'Lovina Beach',
                'location'    => 'Kalibukbuk, Buleleng',
                'region'      => 'utara',
                'latitude'    => -8.1439000,
                'longitude'   => 115.0282000,
                'image'       => 'images/destinations/lovina-beach.jpg',
                'description' => 'Peaceful black-sand northern coast renowned for sunrise traditional boat tours to spot wild dolphins.',
            ],
            [
                'name'        => 'Banjar Hot Springs',
                'location'    => 'Banjar, Buleleng',
                'region'      => 'utara',
                'latitude'    => -8.2105000,
                'longitude'   => 114.9671000,
                'image'       => 'images/destinations/banjar-hot-springs.jpg',
                'description' => 'Natural volcanic sulfur hot spring pools adorned with carved stone dragon spouts in a tropical garden.',
            ],

            // West Bali (Tabanan, Jembrana, West Buleleng)
            [
                'name'        => 'Tanah Lot Sunset Temple',
                'location'    => 'Beraban, Tabanan',
                'region'      => 'barat',
                'latitude'    => -8.6212000,
                'longitude'   => 115.0868000,
                'image'       => 'images/destinations/tanah-lot-sunset-temple.jpg',
                'description' => 'World-famous offshore rock temple offering dramatic ocean waves and unforgettable sunset silhouettes.',
            ],
            [
                'name'        => 'West Bali National Park',
                'location'    => 'Gerokgak, Buleleng',
                'region'      => 'barat',
                'latitude'    => -8.1489000,
                'longitude'   => 114.4847000,
                'image'       => 'images/destinations/west-bali-national-park.jpg',
                'description' => 'Protected rainforest and mangrove sanctuary, home to the critically endangered Bali Starling.',
            ],
            [
                'name'        => 'Menjangan Island',
                'location'    => 'Sumberklampok, Buleleng',
                'region'      => 'barat',
                'latitude'    => -8.0968000,
                'longitude'   => 114.5153000,
                'image'       => 'images/destinations/menjangan-island.jpg',
                'description' => 'Uninhabited marine reserve island celebrated for world-class coral wall diving and snorkeling.',
            ],
            [
                'name'        => 'Gilimanuk Bay',
                'location'    => 'Gilimanuk, Melaya, Jembrana',
                'region'      => 'barat',
                'latitude'    => -8.1587000,
                'longitude'   => 114.4368000,
                'image'       => 'images/destinations/gilimanuk-bay.jpg',
                'description' => 'Serene western bay surrounded by calm mangrove waterways and scenic views across the Bali Strait.',
            ],
            [
                'name'        => 'Medewi Beach',
                'location'    => 'Pekutatan, Jembrana',
                'region'      => 'barat',
                'latitude'    => -8.4192000,
                'longitude'   => 114.8083000,
                'image'       => 'images/destinations/medewi-beach.jpg',
                'description' => 'Laid-back pebble and black-sand beach famous for Bali’s longest mellow left-hand point break.',
            ],
        ];

        foreach ($destinations as $d) {
            $slug = Str::slug($d['name']);
            Destination::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'        => $d['name'],
                    'location'    => $d['location'],
                    'region'      => $d['region'],
                    'latitude'    => $d['latitude'],
                    'longitude'   => $d['longitude'],
                    'image_path'  => $d['image'],
                    'description' => $d['description'],
                ]
            );
        }
    }
}
