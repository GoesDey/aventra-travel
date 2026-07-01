<?php

namespace App\Livewire\Customer;

use App\Models\Booking;
use Livewire\Component;

class BookingHistory extends Component
{
    public function render()
    {
        return view('livewire.customer.booking-history', [
            'bookings' => auth()->user()->bookings()->with(['package', 'review'])->latest()->get(),
        ]);
    }
}
