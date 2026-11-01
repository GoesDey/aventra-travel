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
        if (auth()->user()?->isAgent()) {
            if ($booking->package->agent_id !== auth()->user()->agent?->id) {
                abort(403, 'Akses ditolak. Booking ini bukan bagian dari paket travel Anda.');
            }

            try {
                $booking->transitionStatusByAgent($status);
                session()->flash('success', "Status booking berhasil diperbarui menjadi {$status}.");
            } catch (\DomainException $e) {
                session()->flash('error', $e->getMessage());
            }
            return;
        }

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
