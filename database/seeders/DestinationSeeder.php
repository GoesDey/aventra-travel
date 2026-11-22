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
            // Ubud & Gianyar
            [
                'name'        => 'Monkey Forest Ubud',
                'location'    => 'Ubud, Gianyar',
                'latitude'    => -8.5186990,
                'longitude'   => 115.2586670,
                'image'       => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80',
                'description' => 'Cagar alam suci dengan ratusan monyet ekor panjang dan pepohonan purba yang rindang.',
            ],
            [
                'name'        => 'Tirta Empul Temple',
                'location'    => 'Tampaksiring, Gianyar',
                'latitude'    => -8.4157300,
                'longitude'   => 115.3153200,
                'image'       => 'https://images.unsplash.com/photo-1555400038-63f5ba517a47?auto=format&fit=crop&w=800&q=80',
                'description' => 'Pura pemandian suci bersejarah dengan mata air alami untuk ritual penyucian diri (Melukat).',
            ],
            [
                'name'        => 'Tegalalang Rice Terrace',
                'location'    => 'Tegalalang, Gianyar',
                'latitude'    => -8.4312000,
                'longitude'   => 115.2792700,
                'image'       => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80',
                'description' => 'Hamparan sawah terasering berundak khas sistem pengairan Subak tradisional Bali.',
            ],
            [
                'name'        => 'Bali Safari & Marine Park',
                'location'    => 'Gianyar, Bali',
                'latitude'    => -8.5829000,
                'longitude'   => 115.3446000,
                'image'       => 'https://images.unsplash.com/photo-1577717903315-1691ae25ab3f?auto=format&fit=crop&w=800&q=80',
                'description' => 'Taman konservasi satwa liar terbesar di Bali dengan pengalaman safari interaktif.',
            ],

            // Kintamani & Bangli
            [
                'name'        => 'Mount Batur Caldera',
                'location'    => 'Kintamani, Bangli',
                'latitude'    => -8.2422000,
                'longitude'   => 115.3753000,
                'image'       => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80',
                'description' => 'Gunung berapi aktif dengan panorama sunrise menembus kabut awan dan danau Batur yang menyejukkan.',
            ],
            [
                'name'        => 'Kintamani Natural Hot Spring',
                'location'    => 'Toya Bungkah, Kintamani, Bangli',
                'latitude'    => -8.2489000,
                'longitude'   => 115.4001000,
                'image'       => 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=800&q=80',
                'description' => 'Pemandian air panas alami di tepi Danau Batur dengan latar belakang Gunung Batur.',
            ],

            // Nusa Penida
            [
                'name'        => 'Kelingking Beach Nusa Penida',
                'location'    => 'Nusa Penida, Klungkung',
                'latitude'    => -8.7518000,
                'longitude'   => 115.4741000,
                'image'       => 'https://images.unsplash.com/photo-1577717903315-1691ae25ab3f?auto=format&fit=crop&w=800&q=80',
                'description' => 'Tebing karst megah berbentuk T-Rex dengan pasir putih pantai dan air laut biru toska spektakuler.',
            ],
            [
                'name'        => 'Diamond Beach Nusa Penida',
                'location'    => 'Pejukutan, Nusa Penida, Klungkung',
                'latitude'    => -8.7755000,
                'longitude'   => 115.6203000,
                'image'       => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                'description' => 'Pantai berpasir putih dengan tebing batu kapur runcing menyerupai berlian dan tangga tebing ikonik.',
            ],
            [
                'name'        => 'Crystal Bay Nusa Penida',
                'location'    => 'Sakti, Nusa Penida, Klungkung',
                'latitude'    => -8.7153000,
                'longitude'   => 115.4589000,
                'image'       => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=80',
                'description' => 'Teluk berair jernih yang populer untuk snorkeling melihat terumbu karang dan ikan Mola-Mola.',
            ],

            // Bali Selatan (Uluwatu, Jimbaran, Benoa)
            [
                'name'        => 'Uluwatu Cliff & Temple',
                'location'    => 'Pecatu, Badung',
                'latitude'    => -8.8291000,
                'longitude'   => 115.0849000,
                'image'       => 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=800&q=80',
                'description' => 'Pura luhur di tebing karang setinggi 70 meter menghadap Samudera Hindia dengan atraksi Tari Kecak.',
            ],
            [
                'name'        => 'Jimbaran Bay Seafood Beach',
                'location'    => 'Jimbaran, Kuta Selatan, Badung',
                'latitude'    => -8.7802000,
                'longitude'   => 115.1669000,
                'image'       => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                'description' => 'Pantai teluk tenang yang terkenal dengan kuliner makan malam seafood bakar di tepi pantai saat sunset.',
            ],
            [
                'name'        => 'Pelabuhan Benoa Sunset Harbor',
                'location'    => 'Benoa, Denpasar Selatan',
                'latitude'    => -8.7429000,
                'longitude'   => 115.2128000,
                'image'       => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                'description' => 'Dermaga keberangkatan kapal pesiar sunset cruise mewah mengelilingi perairan Bali Selatan.',
            ],

            // Bali Barat & Utara (Tanah Lot, Bedugul, Air Terjun, Menjangan)
            [
                'name'        => 'Tanah Lot Sunset',
                'location'    => 'Beraban, Tabanan',
                'latitude'    => -8.6212000,
                'longitude'   => 115.0868000,
                'image'       => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=80',
                'description' => 'Pura ikonik di atas bongkahan batu karang besar di tengah samudera dengan sunset memukau.',
            ],
            [
                'name'        => 'Pura Ulun Danu Beratan',
                'location'    => 'Candikuning, Bedugul, Tabanan',
                'latitude'    => -8.2751000,
                'longitude'   => 115.1668000,
                'image'       => 'https://images.unsplash.com/photo-1555400038-63f5ba517a47?auto=format&fit=crop&w=800&q=80',
                'description' => 'Pura air ikonik di tepi Danau Beratan dengan udara pegunungan Bedugul yang sejuk.',
            ],
            [
                'name'        => 'Sekumpul Waterfall',
                'location'    => 'Sawan, Buleleng',
                'latitude'    => -8.1763000,
                'longitude'   => 115.1827000,
                'image'       => 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=800&q=80',
                'description' => 'Kompleks air terjun kembar tertinggi dan terindah di tengah lembah hijau Bali Utara.',
            ],
            [
                'name'        => 'Gitgit Waterfall',
                'location'    => 'Sukasada, Buleleng',
                'latitude'    => -8.1917000,
                'longitude'   => 115.1342000,
                'image'       => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80',
                'description' => 'Air terjun bertingkat yang mudah diakses dengan tebing batu alami dan kolam jernih.',
            ],
            [
                'name'        => 'Taman Nasional Bali Barat',
                'location'    => 'Gerokgak, Buleleng',
                'latitude'    => -8.1489000,
                'longitude'   => 114.4847000,
                'image'       => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80',
                'description' => 'Kawasan konservasi hutan tropis, mangrove, dan habitat burung Jalak Bali yang langka.',
            ],
            [
                'name'        => 'Pulau Menjangan',
                'location'    => 'Sumberklampok, Buleleng',
                'latitude'    => -8.0968000,
                'longitude'   => 114.5153000,
                'image'       => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                'description' => 'Pulau kecil tak berpenghuni dengan dinding terumbu karang bawah laut kelas dunia.',
            ],

            // Bali Selatan Tambahan
            [
                'name'        => 'Pantai Kuta',
                'location'    => 'Kuta, Badung',
                'latitude'    => -8.7185000,
                'longitude'   => 115.1686000,
                'image'       => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                'description' => 'Pantai berpasir putih legendaris dengan ombak berselancar kelas dunia dan panorama matahari terbenam yang ikonik.',
            ],
            [
                'name'        => 'GWK Cultural Park',
                'location'    => 'Ungasan, Kuta Selatan, Badung',
                'latitude'    => -8.8104000,
                'longitude'   => 115.1676000,
                'image'       => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80',
                'description' => 'Taman budaya megah dengan patung Garuda Wisnu Kencana setinggi 121 meter dan pentas seni budaya harian.',
            ],
            [
                'name'        => 'Pantai Melasti',
                'location'    => 'Ungasan, Kuta Selatan, Badung',
                'latitude'    => -8.8482000,
                'longitude'   => 115.1601000,
                'image'       => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                'description' => 'Pantai pasir putih di dasar tebing kapur terjal berkelok dengan air laut jernih bergradasi toska.',
            ],

            // Bali Timur (Karangasem)
            [
                'name'        => 'Pura Besakih',
                'location'    => 'Rendang, Karangasem',
                'latitude'    => -8.3740000,
                'longitude'   => 115.4509000,
                'image'       => 'https://images.unsplash.com/photo-1555400038-63f5ba517a47?auto=format&fit=crop&w=800&q=80',
                'description' => 'Pura terbesar dan tersuci di Bali (Mother Temple) yang berdiri megah di lereng Gunung Agung.',
            ],
            [
                'name'        => 'Pura Lempuyang',
                'location'    => 'Abang, Karangasem',
                'latitude'    => -8.3905000,
                'longitude'   => 115.6294000,
                'image'       => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80',
                'description' => 'Pura purba di puncak bukit dengan Gerbang Surga (Gates of Heaven) yang berlatar kemegahan Gunung Agung.',
            ],
            [
                'name'        => 'Tirta Gangga',
                'location'    => 'Ababi, Karangasem',
                'latitude'    => -8.4122000,
                'longitude'   => 115.5873000,
                'image'       => 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=800&q=80',
                'description' => 'Taman istana air kerajaan Karangasem dengan kolam ikan koi, candi air bertingkat, dan jalan setapak di atas air.',
            ],

            // Bali Utara Tambahan
            [
                'name'        => 'Pantai Lovina',
                'location'    => 'Kalibukbuk, Buleleng',
                'latitude'    => -8.1439000,
                'longitude'   => 115.0282000,
                'image'       => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                'description' => 'Pantai berpasir hitam tenang di Bali Utara dengan atraksi perahu tradisional melihat lumba-lumba liar saat matahari terbit.',
            ],
            [
                'name'        => 'Banjar Hot Springs',
                'location'    => 'Banjar, Buleleng',
                'latitude'    => -8.2105000,
                'longitude'   => 114.9671000,
                'image'       => 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=800&q=80',
                'description' => 'Pemandian air panas belerang alami berarsitektur tradisional di tengah lembah tropis Buleleng.',
            ],

            // Bali Barat Tambahan
            [
                'name'        => 'Teluk Gilimanuk',
                'location'    => 'Gilimanuk, Melaya, Jembrana',
                'latitude'    => -8.1587000,
                'longitude'   => 114.4368000,
                'image'       => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80',
                'description' => 'Kawasan teluk tenang di ujung barat pulau Bali dengan hutan mangrove dan panorama Selat Bali.',
            ],
            [
                'name'        => 'Pantai Medewi',
                'location'    => 'Pekutatan, Jembrana',
                'latitude'    => -8.4192000,
                'longitude'   => 114.8083000,
                'image'       => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                'description' => 'Pantai berpasir hitam dan berkarang dengan ombak kiri panjang yang menjadi destinasi berselancar terkemuka.',
            ],
        ];

        $regionMap = [
            'mount-batur-caldera'                 => 'utara',
            'kintamani-natural-hot-spring'        => 'utara',
            'pura-ulun-danu-beratan'              => 'utara',
            'sekumpul-waterfall'                  => 'utara',
            'gitgit-waterfall'                    => 'utara',
            'pantai-lovina'                       => 'utara',
            'banjar-hot-springs'                  => 'utara',
            'monkey-forest-ubud'                  => 'selatan',
            'tirta-empul-temple'                  => 'selatan',
            'tegalalang-rice-terrace'             => 'selatan',
            'bali-safari-marine-park'             => 'selatan',
            'uluwatu-cliff-temple'                => 'selatan',
            'jimbaran-bay-seafood-beach'          => 'selatan',
            'pelabuhan-benoa-sunset-harbor'       => 'selatan',
            'pantai-kuta'                         => 'selatan',
            'gwk-cultural-park'                   => 'selatan',
            'pantai-melasti'                      => 'selatan',
            'kelingking-beach-nusa-penida'        => 'timur',
            'diamond-beach-nusa-penida'           => 'timur',
            'crystal-bay-nusa-penida'             => 'timur',
            'pura-besakih'                        => 'timur',
            'pura-lempuyang'                      => 'timur',
            'tirta-gangga'                        => 'timur',
            'tanah-lot-sunset'                    => 'barat',
            'taman-nasional-bali-barat'           => 'barat',
            'pulau-menjangan'                     => 'barat',
            'teluk-gilimanuk'                     => 'barat',
            'pantai-medewi'                       => 'barat',
        ];

        foreach ($destinations as $d) {
            $slug = Str::slug($d['name']);
            Destination::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'        => $d['name'],
                    'location'    => $d['location'],
                    'region'      => $regionMap[$slug] ?? 'selatan',
                    'latitude'    => $d['latitude'],
                    'longitude'   => $d['longitude'],
                    'image_path'  => $d['image'],
                    'description' => $d['description'],
                ]
            );
        }
    }
}
