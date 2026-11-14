<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MidtransWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Rute API bebas dari verifikasi CSRF dan diproteksi rate limiting (throttle).
|
*/

Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle'])
    ->middleware('throttle:300,1')
    ->name('api.midtrans.webhook');

Route::post('/midtrans/callback', [MidtransWebhookController::class, 'handle'])
    ->middleware('throttle:300,1')
    ->name('api.midtrans.callback');
