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
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            // 1. Expand ENUM temporarily so both old and new values are valid
            DB::statement("ALTER TABLE packages MODIFY COLUMN region ENUM('utara', 'selatan', 'timur', 'barat', 'lainnya') NOT NULL DEFAULT 'selatan'");

            // 2. Migrate legacy 'lainnya' records to 'selatan'
            DB::table('packages')->where('region', 'lainnya')->update(['region' => 'selatan']);

            // 3. Restrict ENUM to the 4 official regions
            DB::statement("ALTER TABLE packages MODIFY COLUMN region ENUM('utara', 'selatan', 'timur', 'barat') NOT NULL DEFAULT 'selatan'");
        } else {
            DB::table('packages')->where('region', 'lainnya')->update(['region' => 'selatan']);

            Schema::table('packages', function (Blueprint $table) {
                $table->string('region', 30)->default('selatan')->change();
            });
        }

        // Also update user_preferences.preferred_regions if any row contains 'lainnya'
        if (Schema::hasTable('user_preferences')) {
            $preferences = DB::table('user_preferences')->get();
            foreach ($preferences as $pref) {
                $regions = json_decode($pref->preferred_regions ?? '[]', true);
                if (is_array($regions) && in_array('lainnya', $regions, true)) {
                    $updated = array_values(array_unique(array_map(
                        fn ($r) => $r === 'lainnya' ? 'selatan' : $r,
                        $regions
                    )));
                    DB::table('user_preferences')
                        ->where('id', $pref->id)
                        ->update(['preferred_regions' => json_encode($updated)]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE packages MODIFY COLUMN region ENUM('utara', 'selatan', 'timur', 'barat', 'lainnya') NOT NULL DEFAULT 'timur'");
            DB::table('packages')->whereIn('region', ['utara', 'selatan'])->update(['region' => 'lainnya']);
            DB::statement("ALTER TABLE packages MODIFY COLUMN region ENUM('timur', 'barat', 'lainnya') NOT NULL DEFAULT 'timur'");
        } else {
            DB::table('packages')->whereIn('region', ['utara', 'selatan'])->update(['region' => 'lainnya']);
        }
    }
};

