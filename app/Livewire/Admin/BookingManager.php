<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use Livewire\Component;
use Livewire\WithPagination;

class BookingManager extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public function updateStatus(Booking $booking, string $status): void
    {
        $booking->update(['status' => $status]);
        session()->flash('success', 'Status booking diperbarui.');
    }

    public function render()
    {
        return view('livewire.admin.booking-manager', [
            'bookings' => Booking::with(['user', 'package'])
                ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
                ->latest()
                ->paginate(15),
        ])->layout('components.layouts.admin');
    }
}
