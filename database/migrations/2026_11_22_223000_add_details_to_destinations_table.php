<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            if (!Schema::hasColumn('destinations', 'description_id')) {
                $table->text('description_id')->nullable()->after('description');
            }
            if (!Schema::hasColumn('destinations', 'default_stay_minutes')) {
                $table->unsignedSmallInteger('default_stay_minutes')->default(120)->after('longitude');
            }
            if (!Schema::hasColumn('destinations', 'requires_sea_crossing')) {
                $table->boolean('requires_sea_crossing')->default(false)->after('default_stay_minutes');
            }
            if (!Schema::hasColumn('destinations', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('requires_sea_crossing');
            }
        });
    }

    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $cols = array_filter(
                ['description_id', 'default_stay_minutes', 'requires_sea_crossing', 'is_active'],
                fn ($col) => Schema::hasColumn('destinations', $col)
            );
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};

