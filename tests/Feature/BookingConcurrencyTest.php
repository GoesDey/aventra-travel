<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\Booking;
use App\Models\Package;
use App\Models\PackageSchedule;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BookingConcurrencyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Pastikan koneksi menggunakan database MySQL/PostgreSQL sungguhan
        $driver = DB::connection()->getDriverName();
        $this->assertContains($driver, ['mysql', 'pgsql'], "Pengujian konkurensi wajib menggunakan MySQL/PostgreSQL, bukan SQLite.");
    }

    /**
     * Test Konkurensi Paralel Sungguhan (Multi-Process OS):
     * 2 request konkuren dari 2 proses terpisah pada MySQL memperebutkan kuota 1:
     * - Tepat satu request berhasil
     * - Tepat satu request ditolak karena kehabisan kuota
     * - Kuota akhir tepat 0 (tidak boleh -1 / negatif)
     * - Total record booking tersimpan tepat 1
     */
    public function test_true_concurrency_two_parallel_processes_competing_for_single_seat(): void
    {
        // 1. Siapkan Agen dan User Agen Aktif
        $agentUser = User::factory()->create([
            'role'   => 'agent',
            'status' => 'active',
            'email'  => 'concurrency_agent_' . uniqid() . '@example.com',
        ]);

        $agent = Agent::create([
            'user_id'       => $agentUser->id,
            'agent_name'    => 'Concurrency Travel Agent',
            'phone_number'  => '081299990001',
            'address'       => 'Jl. Sunset Road Bali',
            'vehicle_count' => 3,
        ]);

        // 2. Siapkan Paket Wisata
        $package = Package::create([
            'agent_id'      => $agent->id,
            'name'          => 'Paket Uji Konkurensi ' . uniqid(),
            'slug'          => 'paket-konkurensi-' . uniqid(),
            'description'   => 'Paket khusus pengujian anti-race condition',
            'price_per_pax' => 500000,
            'duration_days' => 3,
            'max_guests'    => 10,
            'is_active'     => true,
            'category'      => 'reguler',
            'region'        => 'barat',
        ]);

        // 3. Siapkan Jadwal Keberangkatan dengan KUOTA = 1 (keberangkatan H+5)
        $schedule = PackageSchedule::create([
            'package_id' => $package->id,
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date'   => now()->addDays(7)->toDateString(),
            'quota'      => 1,
        ]);

        // 4. Siapkan Dua User Berbeda
        $userA = User::factory()->create([
            'role'   => 'user',
            'status' => 'active',
            'email'  => 'user_concurrent_a_' . uniqid() . '@example.com',
        ]);

        $userB = User::factory()->create([
            'role'   => 'user',
            'status' => 'active',
            'email'  => 'user_concurrent_b_' . uniqid() . '@example.com',
        ]);

        // 5. Jalankan Dua Proses CLI Paralel menggunakan proc_open
        $descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $artisanPath = base_path('artisan');
        $cmdA = 'php ' . escapeshellarg($artisanPath) . " booking:simulate-concurrent {$userA->id} {$package->slug} {$schedule->id} 1";
        $cmdB = 'php ' . escapeshellarg($artisanPath) . " booking:simulate-concurrent {$userB->id} {$package->slug} {$schedule->id} 1";

        // Buka kedua proses secara simultan
        $procA = proc_open($cmdA, $descriptorspec, $pipesA, base_path());
        $procB = proc_open($cmdB, $descriptorspec, $pipesB, base_path());

        $this->assertIsResource($procA);
        $this->assertIsResource($procB);

        // Baca output dari masing-masing proses
        $outputA = stream_get_contents($pipesA[1]);
        $outputB = stream_get_contents($pipesB[1]);
        $errA    = stream_get_contents($pipesA[2]);
        $errB    = stream_get_contents($pipesB[2]);

        fclose($pipesA[0]);
        fclose($pipesA[1]);
        fclose($pipesA[2]);
        fclose($pipesB[0]);
        fclose($pipesB[1]);
        fclose($pipesB[2]);

        $exitA = proc_close($procA);
        $exitB = proc_close($procB);

        $jsonA = json_decode(trim($outputA), true);
        $jsonB = json_decode(trim($outputB), true);

        // Salah satu harus sukses (exit code 0) dan salah satu gagal (exit code != 0)
        $successCount = 0;
        if (($jsonA['success'] ?? false) === true) $successCount++;
        if (($jsonB['success'] ?? false) === true) $successCount++;

        $this->assertEquals(1, $successCount, "Dari 2 proses paralel yang berebut kuota 1, harus tepat 1 proses yang berhasil! Output A: {$outputA} | Output B: {$outputB}");

        // 6. Verifikasi Kondisi Database MySQL
        $freshSchedule = PackageSchedule::find($schedule->id);
        $this->assertEquals(0, $freshSchedule->quota, "Sisa kuota di database MySQL harus tepat 0 dan tidak boleh bernilai negatif (-1).");

        $totalBookings = Booking::where('package_schedule_id', $schedule->id)->count();
        $this->assertEquals(1, $totalBookings, "Hanya boleh ada tepat 1 record booking yang tersimpan di database MySQL.");

        // Bersihkan data uji
        Booking::where('package_schedule_id', $schedule->id)->delete();
        $schedule->delete();
        $package->delete();
        $agent->delete();
        $agentUser->delete();
        $userA->delete();
        $userB->delete();
    }

    /**
     * Test Validasi H-3: Pemesanan tepat H-3 diizinkan, H-2 ditolak.
     */
    public function test_h3_boundary_validation(): void
    {
        $agentUser = User::factory()->create(['role' => 'agent', 'status' => 'active']);
        $agent = Agent::create([
            'user_id'       => $agentUser->id,
            'agent_name'    => 'H3 Agent',
            'phone_number'  => '081200001111',
            'address'       => 'Bali',
            'vehicle_count' => 1,
        ]);

        $package = Package::create([
            'agent_id'      => $agent->id,
            'name'          => 'Paket H-3 Test',
            'slug'          => 'paket-h3-' . uniqid(),
            'description'   => 'Test H-3',
            'price_per_pax' => 200000,
            'duration_days' => 1,
            'max_guests'    => 5,
            'is_active'     => true,
        ]);

        $user = User::factory()->create(['role' => 'user', 'status' => 'active']);

        // Jadwal H-2: harus ditolak
        $scheduleH2 = PackageSchedule::create([
            'package_id' => $package->id,
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date'   => now()->addDays(2)->toDateString(),
            'quota'      => 5,
        ]);

        $responseH2 = $this->actingAs($user)->post(route('packages.book', $package->slug), [
            'package_schedule_id' => $scheduleH2->id,
            'guest_count'         => 1,
        ]);
        $responseH2->assertSessionHasErrors('package_schedule_id');

        // Jadwal tepat H-3: harus BERHASIL (diizinkan)
        $scheduleH3 = PackageSchedule::create([
            'package_id' => $package->id,
            'start_date' => now()->addDays(3)->toDateString(),
            'end_date'   => now()->addDays(3)->toDateString(),
            'quota'      => 5,
        ]);

        $responseH3 = $this->actingAs($user)->post(route('packages.book', $package->slug), [
            'package_schedule_id' => $scheduleH3->id,
            'guest_count'         => 1,
        ]);
        $responseH3->assertSessionHasNoErrors();
        $responseH3->assertRedirect();

        // Cleanup
        Booking::where('package_id', $package->id)->delete();
        $scheduleH2->delete();
        $scheduleH3->delete();
        $package->delete();
        $agent->delete();
        $agentUser->delete();
        $user->delete();
    }

    /**
     * Test Validasi: Agen pemilik paket non-aktif (suspended/pending) ditolak.
     */
    public function test_inactive_agent_package_cannot_be_booked(): void
    {
        $suspendedAgentUser = User::factory()->create([
            'role'   => 'agent',
            'status' => 'suspended',
        ]);

        $agent = Agent::create([
            'user_id'       => $suspendedAgentUser->id,
            'agent_name'    => 'Suspended Agent',
            'phone_number'  => '081200002222',
            'address'       => 'Bali',
            'vehicle_count' => 1,
        ]);

        $package = Package::create([
            'agent_id'      => $agent->id,
            'name'          => 'Paket Suspended Agent',
            'slug'          => 'paket-suspended-' . uniqid(),
            'description'   => 'Test',
            'price_per_pax' => 100000,
            'duration_days' => 1,
            'max_guests'    => 5,
            'is_active'     => true,
        ]);

        $schedule = PackageSchedule::create([
            'package_id' => $package->id,
            'start_date' => now()->addDays(10)->toDateString(),
            'end_date'   => now()->addDays(11)->toDateString(),
            'quota'      => 5,
        ]);

        $customer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        $response = $this->actingAs($customer)->post(route('packages.book', $package->slug), [
            'package_schedule_id' => $schedule->id,
            'guest_count'         => 1,
        ]);

        $response->assertSessionHasErrors('package_schedule_id');

        // Cleanup
        $schedule->delete();
        $package->delete();
        $agent->delete();
        $suspendedAgentUser->delete();
        $customer->delete();
    }

    /**
     * Test Guard Transisi Status oleh Agen: hanya dibayar -> berjalan -> selesai.
     */
    public function test_agent_status_transitions_guard(): void
    {
        $booking = new Booking([
            'status' => 'menunggu_pembayaran',
        ]);

        // Mencoba langsung ke 'selesai' dari 'menunggu_pembayaran' harus ditolak
        $this->expectException(\DomainException::class);
        $booking->transitionStatusByAgent('selesai');
    }

    /**
     * Test Paket Belum Aktif: Tetap tampil di katalog beserta gambar dan badge, namun pemesanan ditolak.
     */
    public function test_inactive_package_is_displayed_with_image_and_cannot_be_booked(): void
    {
        $agentUser = User::factory()->create(['role' => 'agent', 'status' => 'active']);
        $agent = Agent::create([
            'user_id'       => $agentUser->id,
            'agent_name'    => 'Active Agent',
            'phone_number'  => '081288889999',
            'address'       => 'Denpasar',
            'vehicle_count' => 2,
        ]);

        $inactivePackage = Package::create([
            'agent_id'      => $agent->id,
            'name'          => 'Paket Belum Aktif ' . uniqid(),
            'slug'          => 'paket-belum-aktif-' . uniqid(),
            'description'   => 'Paket yang belum dibuka',
            'price_per_pax' => 300000,
            'duration_days' => 2,
            'max_guests'    => 10,
            'cover_image'   => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
            'is_active'     => false,
            'category'      => 'reguler',
            'region'        => 'timur',
        ]);

        $schedule = PackageSchedule::create([
            'package_id' => $inactivePackage->id,
            'start_date' => now()->addDays(6)->toDateString(),
            'end_date'   => now()->addDays(7)->toDateString(),
            'quota'      => 5,
        ]);

        // 1. Katalog /packages tetap menampilkan paket dan badge "Belum Aktif"
        $catalogResponse = $this->get(route('packages.index'));
        $catalogResponse->assertOk();
        $catalogResponse->assertSee($inactivePackage->name);
        $catalogResponse->assertSee('Belum Aktif');

        // 2. Halaman detail /packages/{slug} tetap bisa diakses dan menampilkan gambar cover
        $detailResponse = $this->get(route('packages.show', $inactivePackage->slug));
        $detailResponse->assertOk();
        $detailResponse->assertSee($inactivePackage->cover_image);
        $detailResponse->assertSee('Paket Wisata Sedang Belum Aktif');

        // 3. Pemesanan paket belum aktif harus ditolak oleh backend
        $customer = User::factory()->create(['role' => 'user', 'status' => 'active']);
        $bookResponse = $this->actingAs($customer)->post(route('packages.book', $inactivePackage->slug), [
            'package_schedule_id' => $schedule->id,
            'guest_count'         => 1,
        ]);

        $bookResponse->assertSessionHasErrors('package_schedule_id');

        // Cleanup
        $schedule->delete();
        $inactivePackage->delete();
        $agent->delete();
        $agentUser->delete();
        $customer->delete();
    }
}
