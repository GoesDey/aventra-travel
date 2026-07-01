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

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->regenerate();
            if (auth()->user()->isAdmin()) {
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
