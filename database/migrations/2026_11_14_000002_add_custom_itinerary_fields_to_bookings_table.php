<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'is_custom')) {
                $table->boolean('is_custom')->default(false)->after('package_schedule_id');
            }
            if (!Schema::hasColumn('bookings', 'total_hours')) {
                $table->decimal('total_hours', 5, 2)->nullable()->after('guest_count');
            }
            if (!Schema::hasColumn('bookings', 'overtime_hours')) {
                $table->unsignedInteger('overtime_hours')->default(0)->after('total_hours');
            }
            if (!Schema::hasColumn('bookings', 'overtime_charge')) {
                $table->unsignedBigInteger('overtime_charge')->default(0)->after('overtime_hours');
            }
            if (!Schema::hasColumn('bookings', 'custom_destinations')) {
                $table->json('custom_destinations')->nullable()->after('overtime_charge');
            }
            if (!Schema::hasColumn('bookings', 'special_requests')) {
                $table->text('special_requests')->nullable()->after('custom_destinations');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $cols = array_filter([
                'is_custom',
                'total_hours',
                'overtime_hours',
                'overtime_charge',
                'custom_destinations',
                'special_requests',
            ], fn ($col) => Schema::hasColumn('bookings', $col));

            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};

