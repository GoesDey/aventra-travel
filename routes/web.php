<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Customer\PackageList;
use App\Livewire\Customer\PackageDetail;
use App\Livewire\Customer\BookingHistory;
use App\Livewire\Admin\DestinationManager;
use App\Livewire\Admin\PackageManager;
use App\Livewire\Admin\BookingManager;
use App\Livewire\Admin\PartnerApplicationManager;

use App\Http\Controllers\UserBookingController;
use App\Http\Controllers\PartnerApplicationController;

Route::get('/locale/{locale}', function (string $locale) {
    session(['locale' => $locale]);

    return redirect()->back();
})->whereIn('locale', ['en', 'id'])->name('locale.switch');

Route::view('/', 'home')->name('home');
Route::get('/packages', [UserBookingController::class, 'index'])->name('packages.index');
Route::get('/packages/{slug}', [UserBookingController::class, 'show'])->name('packages.show');
Route::post('/packages/{slug}/book', [UserBookingController::class, 'store'])
    ->middleware(['auth', 'is_user'])
    ->name('packages.book');

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CustomItineraryController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\Api\MidtransWebhookController;

// User Preferences
Route::middleware(['auth', 'is_user'])->group(function () {
    Route::get('/preferences', [PreferenceController::class, 'index'])->name('preferences.index');
    Route::post('/preferences', [PreferenceController::class, 'store'])->name('preferences.store');
});

// Midtrans Webhook / Callback notification endpoints (bypassed from CSRF)
Route::post('/midtrans/callback', [MidtransWebhookController::class, 'handle'])->name('midtrans.callback');
Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle'])->name('midtrans.webhook');
Route::post('/payment/notification', [MidtransWebhookController::class, 'handle'])->name('midtrans.notification');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }
        if ($user->isAgent()) {
            return redirect()->route('agent.dashboard');
        }
        if ($user->isAdmin()) {
            return redirect()->route('admin.bookings');
        }
        if ($user->partnerApplication && in_array($user->partnerApplication->status, ['pending', 'rejected'], true)) {
            return redirect()->route('partner.status');
        }
        return redirect()->route('history');
    })->name('dashboard');

    Route::get('/partner/status', [PartnerApplicationController::class, 'status'])->name('partner.status');
    Route::get('/history', BookingHistory::class)->name('history');
    Route::get('/history/{booking_code}', [UserBookingController::class, 'historyShow'])->name('history.show');
    Route::post('/history/{booking_code}/cancel', [UserBookingController::class, 'cancel'])->name('history.cancel');
    Route::get('/payments/{booking_code}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/{booking_code}/status', [PaymentController::class, 'checkStatus'])->name('payments.status');
    Route::post('/payments/{booking_code}/sync', [PaymentController::class, 'syncStatus'])->name('payments.sync');

    // Custom Itinerary (disimpan melalui Livewire CustomItineraryBuilder; route POST legacy dinonaktifkan)
    Route::get('/custom-itinerary', [CustomItineraryController::class, 'create'])->name('custom-itinerary.create');
    Route::get('/user/custom-itinerary', [CustomItineraryController::class, 'create'])->name('user.custom-itinerary.create');
});

use App\Livewire\Admin\FinanceDashboard;
use App\Http\Controllers\SuperAdminDashboardController;

Route::get('/super-admin/dashboard', [SuperAdminDashboardController::class, 'index'])
    ->middleware(['auth', 'is_super_admin'])
    ->name('super-admin.dashboard');

Route::get('/super-admin/partner-applications', PartnerApplicationManager::class)
    ->middleware(['auth', 'is_super_admin'])
    ->name('super-admin.partner-applications');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/finance', FinanceDashboard::class)->name('admin.finance');
    Route::get('/destinations', DestinationManager::class)
        ->middleware('is_super_admin')
        ->name('admin.destinations');
    Route::get('/packages', PackageManager::class)->name('admin.packages');
    Route::get('/bookings', BookingManager::class)->name('admin.bookings');
});

use App\Http\Controllers\AgentDashboardController;
use App\Http\Controllers\AgentPackageController;
use App\Http\Controllers\AgentBookingController;

Route::middleware(['auth', 'is_agent'])->prefix('agent')->name('agent.')->group(function () {
    Route::get('/dashboard', [AgentDashboardController::class, 'index'])->name('dashboard');
    Route::resource('packages', AgentPackageController::class);
    Route::patch('bookings/{booking}/status', [AgentBookingController::class, 'updateStatus'])->name('bookings.update-status');
});

// Since Livewire uses components for Auth as well, let's just define the routes directly
use App\Livewire\Auth\LoginForm;
use App\Livewire\Auth\RegisterForm;

Route::middleware('guest')->group(function () {
    Route::get('/login', LoginForm::class)->name('login');
    Route::get('/register', RegisterForm::class)->name('register');
});

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');
