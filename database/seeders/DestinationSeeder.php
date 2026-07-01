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
            ['name' => 'Monkey Forest', 'location' => 'Ubud'],
            ['name' => 'Ubud Palace', 'location' => 'Ubud'],
            ['name' => 'Tirta Empul', 'location' => 'Tampaksiring'],
            ['name' => 'Tanah Lot', 'location' => 'Tabanan'],
            ['name' => 'Uluwatu Temple', 'location' => 'Uluwatu'],
            ['name' => 'Kuta Beach', 'location' => 'Kuta'],
        ];

        foreach ($destinations as $d) {
            Destination::create([
                'name'        => $d['name'],
                'slug'        => Str::slug($d['name']),
                'description' => "Destinasi wisata populer di {$d['location']}, Bali.",
                'location'    => $d['location'],
            ]);
        }
    }
}
