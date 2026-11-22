<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

class LoginForm extends Component
{
    public string $email = '';
    public string $password = '';

    protected function rules(): array
    {
        return [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ];
    }

    public function login()
    {
        $this->validate();

        $throttleKey = Str::lower(trim($this->email)) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', __('Too many login attempts. Please try again in :seconds seconds.', [
                'seconds' => $seconds,
            ]));
            return;
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            RateLimiter::clear($throttleKey);
            $user = Auth::user();

            // 1. Cek jika akun berstatus suspended
            if ($user->status === 'suspended') {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();

                $this->addError('email', __('Your account has been suspended. Please contact Super Admin.'));
                return;
            }

            // 2. Cek jika agen masih berstatus pending (belum di-approve Super Admin)
            if ($user->role === 'agent' && $user->status === 'pending') {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();

                $this->addError('email', __('Your agent account is currently pending approval from Super Admin.'));
                return;
            }

            session()->regenerate();

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

            return redirect()->route('packages.index');
        }

        RateLimiter::hit($throttleKey, 60);
        $this->addError('email', __('These credentials do not match our records.'));
    }

    public function render()
    {
        return view('livewire.auth.login-form')
            ->layout('components.layouts.auth');
    }
}
