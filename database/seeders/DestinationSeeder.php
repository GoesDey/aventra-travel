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

        foreach ($destinations as $d) {
            Destination::updateOrCreate(
                ['slug' => Str::slug($d['name'])],
                [
                    'name'        => $d['name'],
                    'location'    => $d['location'],
                    'image_path'  => $d['image'],
                    'description' => $d['description'],
                ]
            );
        }
    }
}
