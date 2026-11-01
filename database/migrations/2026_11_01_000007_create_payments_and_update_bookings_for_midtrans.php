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
        // 1. Update tabel bookings: komisi platform, expires_at, dan status tambahan
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'commission_rate')) {
                $table->decimal('commission_rate', 5, 2)->default(10.00)->after('total_price');
            }
            if (!Schema::hasColumn('bookings', 'commission_amount')) {
                $table->unsignedInteger('commission_amount')->default(0)->after('commission_rate');
            }
            if (!Schema::hasColumn('bookings', 'agent_earning')) {
                $table->unsignedInteger('agent_earning')->default(0)->after('commission_amount');
            }
            if (!Schema::hasColumn('bookings', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('agent_earning');
            }

            // Pastikan status enum mendukung status 'kedaluwarsa' dan 'perlu_refund'
            $table->enum('status', [
                'menunggu_pembayaran',
                'dibayar',
                'berjalan',
                'selesai',
                'dibatalkan',
                'kedaluwarsa',
                'perlu_refund',
            ])->default('menunggu_pembayaran')->change();
        });

        // 2. Pastikan booking_code unique jika belum ada index unique
        // Menggunakan Schema check atau try-catch untuk safety
        try {
            Schema::table('bookings', function (Blueprint $table) {
                $table->unique('booking_code');
            });
        } catch (\Throwable $e) {
            // Abaikan jika index sudah ada
        }

        // 3. Buat tabel payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')
                ->unique()
                ->constrained('bookings')
                ->cascadeOnDelete();
            $table->string('order_id')->unique();
            $table->string('transaction_id')->nullable();
            $table->string('snap_token')->nullable();
            $table->unsignedInteger('amount');
            $table->string('payment_type')->nullable();
            $table->enum('payment_status', ['pending', 'success', 'failed', 'expired'])->default('pending');
            $table->json('raw_notification')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');

        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'commission_rate')) {
                $table->dropColumn(['commission_rate', 'commission_amount', 'agent_earning', 'expires_at']);
            }
            $table->enum('status', [
                'menunggu_pembayaran',
                'dibayar',
                'berjalan',
                'selesai',
                'dibatalkan',
            ])->default('menunggu_pembayaran')->change();
        });
    }
};
