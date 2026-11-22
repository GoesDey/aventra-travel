<?php

namespace App\Livewire\Auth;

use App\Models\PartnerApplication;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class RegisterForm extends Component
{
    public string $accountType = 'traveler';

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Kolom tambahan khusus pendaftar Mitra Travel
    public string $business_name = '';
    public string $whatsapp_number = '';
    public string $nib_number = '';
    public array $operating_regions = [];
    public string $bank_name = '';
    public string $bank_account_number = '';
    public string $bank_account_name = '';

    public function setAccountType(string $type): void
    {
        $this->accountType = $type === 'partner' ? 'partner' : 'traveler';
        $this->resetValidation();
    }

    protected function rules(): array
    {
        $rules = [
            'accountType' => 'required|in:traveler,partner',
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|min:8|confirmed',
        ];

        if ($this->accountType === 'partner') {
            $rules = array_merge($rules, [
                'business_name'       => 'required|string|max:255',
                'whatsapp_number'     => 'required|string|max:30',
                'nib_number'          => 'required|string|max:100',
                'operating_regions'   => 'required|array|min:1',
                'operating_regions.*' => 'in:utara,selatan,timur,barat',
                'bank_name'           => 'required|string|max:100',
                'bank_account_number' => 'required|string|max:60',
                'bank_account_name'   => 'required|string|max:150',
            ]);
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'operating_regions.required' => __('Please select at least one Bali operating region.'),
            'operating_regions.min'      => __('Please select at least one Bali operating region.'),
        ];
    }

    public function register()
    {
        $validated = $this->validate();

        $isPartnerApplication = $this->accountType === 'partner';

        $user = DB::transaction(function () use ($validated, $isPartnerApplication) {
            // Keamanan: role SELALU ditetapkan di server ('user'), tidak pernah dari input form.
            // Pendaftar mitra baru akan dipromosikan menjadi 'agent' hanya setelah disetujui Super Admin.
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => 'user',
                'status'   => 'active',
            ]);

            if ($isPartnerApplication) {
                PartnerApplication::create([
                    'user_id'             => $user->id,
                    'business_name'       => $validated['business_name'],
                    'whatsapp_number'     => $validated['whatsapp_number'],
                    'nib_number'          => $validated['nib_number'],
                    'operating_regions'   => array_values($validated['operating_regions']),
                    'bank_name'           => $validated['bank_name'],
                    'bank_account_number' => $validated['bank_account_number'],
                    'bank_account_name'   => $validated['bank_account_name'],
                    'status'              => 'pending',
                ]);
            }

            return $user;
        });

        auth()->login($user);

        if ($isPartnerApplication) {
            return redirect()->route('partner.status')
                ->with('success', __('Your partner application has been submitted and is currently under verification.'));
        }

        return redirect()->route('packages.index');
    }

    public function render()
    {
        return view('livewire.auth.register-form')
            ->layout('components.layouts.auth');
    }
}
