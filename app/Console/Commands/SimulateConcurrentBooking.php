<?php

namespace App\Console\Commands;

use App\Http\Controllers\UserBookingController;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SimulateConcurrentBooking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:simulate-concurrent {userId} {slug} {scheduleId} {guestCount=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulasi proses booking di CLI untuk pengujian konkurensi paralel antar-proses';

    /**
     * Execute the console command.
     */
    public function handle(UserBookingController $controller)
    {
        $userId     = (int) $this->argument('userId');
        $slug       = (string) $this->argument('slug');
        $scheduleId = (int) $this->argument('scheduleId');
        $guestCount = (int) $this->argument('guestCount');

        $user = User::findOrFail($userId);
        auth()->login($user);

        $request = Request::create("/packages/{$slug}/book", 'POST', [
            'package_schedule_id' => $scheduleId,
            'guest_count'         => $guestCount,
        ]);

        try {
            $response = $controller->store($request, $slug);
            $this->line(json_encode([
                'success'     => true,
                'user_id'     => $userId,
                'status_code' => $response->getStatusCode(),
            ]));
            return Command::SUCCESS;
        } catch (ValidationException $e) {
            $this->line(json_encode([
                'success' => false,
                'user_id' => $userId,
                'error'   => $e->errors(),
            ]));
            return Command::FAILURE;
        } catch (\Throwable $e) {
            $this->line(json_encode([
                'success' => false,
                'user_id' => $userId,
                'error'   => $e->getMessage(),
            ]));
            return Command::FAILURE;
        }
    }
}
