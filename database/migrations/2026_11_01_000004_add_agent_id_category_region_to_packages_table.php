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
        Schema::table('packages', function (Blueprint $table) {
            $table->foreignId('agent_id')
                ->nullable()
                ->after('id')
                ->constrained('agents')
                ->cascadeOnDelete();
            $table->enum('category', ['reguler', 'custom'])
                ->default('reguler')
                ->after('description');
            $table->enum('region', ['timur', 'barat', 'lainnya'])
                ->default('timur')
                ->after('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropColumn(['agent_id', 'category', 'region']);
        });
    }
};
