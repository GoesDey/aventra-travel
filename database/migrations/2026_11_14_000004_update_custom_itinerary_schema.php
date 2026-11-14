<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan kolom region ke tabel destinations
        if (!Schema::hasColumn('destinations', 'region')) {
            Schema::table('destinations', function (Blueprint $table) {
                $table->enum('region', ['utara', 'selatan', 'timur', 'barat'])->nullable()->after('location');
            });
        }

        // Isi default region untuk 23 destinasi yang ada
        $regionMappings = [
            'utara' => [
                'Mount Batur Caldera',
                'Kintamani Natural Hot Spring',
                'Pura Ulun Danu Beratan',
                'Sekumpul Waterfall',
                'Gitgit Waterfall',
            ],
            'selatan' => [
                'Monkey Forest Ubud',
                'Tirta Empul Temple',
                'Tegalalang Rice Terrace',
                'Bali Safari & Marine Park',
                'Uluwatu Cliff & Temple',
                'Jimbaran Bay Seafood Beach',
                'Pelabuhan Benoa Sunset Harbor',
            ],
            'timur' => [
                'Kelingking Beach Nusa Penida',
                'Diamond Beach Nusa Penida',
                'Crystal Bay Nusa Penida',
                'Gili Trawangan',
                'Gili Air',
                'Taman Nasional Gunung Rinjani',
                'Pulau Padar & Taman Nasional Komodo',
                'Pink Beach Komodo',
            ],
            'barat' => [
                'Tanah Lot Sunset',
                'Taman Nasional Bali Barat',
                'Pulau Menjangan',
            ],
        ];

        foreach ($regionMappings as $region => $destNames) {
            DB::table('destinations')->whereIn('name', $destNames)->update(['region' => $region]);
        }

        // 2. Tambahkan kolom pendukung ke tabel agents
        Schema::table('agents', function (Blueprint $table) {
            if (!Schema::hasColumn('agents', 'operating_regions')) {
                $table->json('operating_regions')->nullable()->after('address');
            }
            if (!Schema::hasColumn('agents', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('operating_regions');
            }
            if (!Schema::hasColumn('agents', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
        });

        // Berikan default operating_regions ke semua agen yang ada agar melayani seluruh region
        DB::table('agents')->whereNull('operating_regions')->update([
            'operating_regions' => json_encode(['utara', 'selatan', 'timur', 'barat']),
            'latitude'          => -8.6705000,
            'longitude'         => 115.2625000,
        ]);

        // 3. Tambahkan kolom rute & estimasi waktu ke tabel bookings
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'start_point')) {
                $table->text('start_point')->nullable()->after('guest_count');
            }
            if (!Schema::hasColumn('bookings', 'travel_time_hours')) {
                $table->decimal('travel_time_hours', 5, 2)->nullable()->default(0)->after('start_point');
            }
            if (!Schema::hasColumn('bookings', 'stay_time_hours')) {
                $table->decimal('stay_time_hours', 5, 2)->nullable()->default(0)->after('travel_time_hours');
            }
            if (!Schema::hasColumn('bookings', 'travel_distance_km')) {
                $table->decimal('travel_distance_km', 6, 2)->nullable()->default(0)->after('stay_time_hours');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            if (Schema::hasColumn('destinations', 'region')) {
                $table->dropColumn('region');
            }
        });

        Schema::table('agents', function (Blueprint $table) {
            $cols = array_filter(['operating_regions', 'latitude', 'longitude'], fn ($c) => Schema::hasColumn('agents', $c));
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            $cols = array_filter(['start_point', 'travel_time_hours', 'stay_time_hours', 'travel_distance_km'], fn ($c) => Schema::hasColumn('bookings', $c));
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};

