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
            if (Schema::hasColumn('bookings', 'overtime_hours')) {
                $table->unsignedInteger('overtime_hours')->nullable()->default(0)->change();
            }
            if (Schema::hasColumn('bookings', 'overtime_charge')) {
                $table->unsignedBigInteger('overtime_charge')->nullable()->default(0)->change();
            }
            if (Schema::hasColumn('bookings', 'is_custom')) {
                $table->boolean('is_custom')->nullable()->default(false)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'overtime_hours')) {
                $table->unsignedInteger('overtime_hours')->default(0)->change();
            }
            if (Schema::hasColumn('bookings', 'overtime_charge')) {
                $table->unsignedBigInteger('overtime_charge')->default(0)->change();
            }
            if (Schema::hasColumn('bookings', 'is_custom')) {
                $table->boolean('is_custom')->default(false)->change();
            }
        });
    }
};

