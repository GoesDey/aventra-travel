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
            if (!Schema::hasColumn('bookings', 'package_schedule_id')) {
                $table->foreignId('package_schedule_id')
                    ->nullable()
                    ->after('package_id')
                    ->constrained('package_schedules')
                    ->cascadeOnDelete();
            }

            if (Schema::hasColumn('bookings', 'booking_code')) {
                $table->string('booking_code', 50)->nullable()->change();
            }

            if (Schema::hasColumn('bookings', 'travel_date')) {
                $table->date('travel_date')->nullable()->change();
            }

            $table->enum('status', [
                'menunggu_pembayaran',
                'dibayar',
                'berjalan',
                'selesai',
                'dibatalkan'
            ])->default('menunggu_pembayaran')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'package_schedule_id')) {
                $table->dropForeign(['package_schedule_id']);
                $table->dropColumn('package_schedule_id');
            }
        });
    }
};
