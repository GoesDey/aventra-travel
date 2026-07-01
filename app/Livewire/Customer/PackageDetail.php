<?php

namespace App\Livewire\Customer;

use App\Models\Package;
use Livewire\Component;

class PackageDetail extends Component
{
    public Package $package;

    public function mount(Package $package)
    {
        $this->package = $package->load(['destinations', 'reviews.user']);
    }

    public function render()
    {
        return view('livewire.customer.package-detail');
    }
}
