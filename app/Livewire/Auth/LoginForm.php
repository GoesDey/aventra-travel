<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LoginForm extends Component
{
    public string $email = '';
    public string $password = '';

    protected function rules(): array
    {
        return [
            'email'    => 'required|email',
            'password' => 'required',
        ];
    }

    public function fillCredentials(string $role): void
    {
        if ($role === 'super_admin') {
            $this->email = 'admin@trihitatour.com';
            $this->password = 'password';
        } elseif ($role === 'agent') {
            $this->email = 'agent@trihitatour.com';
            $this->password = 'password';
        } elseif ($role === 'user') {
            $this->email = 'ardy@example.com';
            $this->password = 'password';
        }
    }

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $user = Auth::user();

            // 1. Cek jika akun berstatus suspended
            if ($user->status === 'suspended') {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();

                $this->addError('email', 'Akun Anda sedang ditangguhkan (suspended). Silakan hubungi Super Admin.');
                return;
            }

            // 2. Cek jika agen masih berstatus pending (belum di-approve Super Admin)
            if ($user->role === 'agent' && $user->status === 'pending') {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();

                $this->addError('email', 'Akun agen Anda masih menunggu persetujuan (pending) dari Super Admin.');
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

            return redirect()->route('packages.index');
        }

        $this->addError('email', 'Kredensial tidak valid.');
    }

    public function render()
    {
        return view('livewire.auth.login-form')
            ->layout('components.layouts.auth');
    }
}
