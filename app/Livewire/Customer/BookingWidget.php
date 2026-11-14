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
            'booking_code'    => 'TRIP-' . strtoupper(Str::random(4)) . '-' . date('Ymd') . rand(10, 99),
            'user_id'         => auth()->id(),
            'package_id'      => $this->package->id,
            'travel_date'     => $this->travelDate,
            'guest_count'     => $this->guestCount,
            'total_price'     => $this->subtotal,
            'is_custom'       => false,
            'total_hours'     => null,
            'overtime_hours'  => 0,
            'overtime_charge' => 0,
            'status'          => 'menunggu_pembayaran',
        ]);

        session()->flash('success', 'Booking successful! Waiting for admin confirmation.');
        $this->redirectRoute('history');
    }

    public function render()
    {
        return view('livewire.customer.booking-widget');
    }
}
