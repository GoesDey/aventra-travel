<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use Illuminate\Support\Str;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $package = Package::create([
            'name'           => 'Ubud Cultural Tour',
            'slug'           => Str::slug('Ubud Cultural Tour'),
            'description'    => 'Jelajahi keindahan budaya Ubud selama 1 hari penuh.',
            'price_per_pax'  => 350000,
            'duration_days'  => 1,
            'max_guests'     => 15,
        ]);

        $package->destinations()->attach([
            1 => ['visit_order' => 1], // Monkey Forest
            2 => ['visit_order' => 2], // Ubud Palace
            3 => ['visit_order' => 3], // Tirta Empul
        ]);
    }
}
