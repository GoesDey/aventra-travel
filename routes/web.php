<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Customer\PackageList;
use App\Livewire\Customer\PackageDetail;
use App\Livewire\Customer\BookingHistory;
use App\Livewire\Admin\DestinationManager;
use App\Livewire\Admin\PackageManager;
use App\Livewire\Admin\BookingManager;

Route::view('/', 'home')->name('home');
Route::get('/packages', PackageList::class)->name('packages.index');
Route::get('/packages/{package:slug}', PackageDetail::class)->name('packages.show');

Route::middleware('auth')->group(function () {
    Route::get('/history', BookingHistory::class)->name('history');
});

use App\Livewire\Admin\FinanceDashboard;

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/finance', FinanceDashboard::class)->name('admin.finance');
    Route::get('/destinations', DestinationManager::class)->name('admin.destinations');
    Route::get('/packages', PackageManager::class)->name('admin.packages');
    Route::get('/bookings', BookingManager::class)->name('admin.bookings');
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
