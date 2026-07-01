<?php

namespace App\Livewire\Customer;

use App\Models\Package;
use App\Models\Booking;
use Livewire\Component;
use Illuminate\Support\Str;

class BookingWidget extends Component
{
    public Package $package;
    public string $travelDate = '';
    public int $guestCount = 1;

    public function getSubtotalProperty(): float
    {
        return $this->package->price_per_pax * $this->guestCount;
    }

    public function book(): void
    {
        if (!auth()->check()) {
            $this->redirectRoute('login');
            return;
        }

        $this->validate([
            'travelDate' => 'required|date|after:today',
            'guestCount' => 'required|integer|min:1|max:'.$this->package->max_guests,
        ]);

        $booking = Booking::create([
            'booking_code' => 'AVN-' . strtoupper(Str::random(6)),
            'user_id'      => auth()->id(),
            'package_id'   => $this->package->id,
            'travel_date'  => $this->travelDate,
            'guest_count'  => $this->guestCount,
            'total_price'  => $this->subtotal,
            'status'       => 'pending',
        ]);

        session()->flash('success', 'Booking berhasil! Menunggu konfirmasi admin.');
        $this->redirectRoute('history');
    }

    public function render()
    {
        return view('livewire.customer.booking-widget');
    }
}
