<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use Livewire\Component;
use Livewire\WithPagination;

class BookingManager extends Component
{
    use WithPagination;

    public string $statusFilter = '';
    public ?int $selectedBookingId = null;

    public function showDetail(int $bookingId): void
    {
        $this->selectedBookingId = $bookingId;
    }

    public function closeDetail(): void
    {
        $this->selectedBookingId = null;
    }

    public function updateStatus(Booking $booking, string $status): void
    {
        if (auth()->user()?->isSuperAdmin()) {
            abort(403, __('Super Admin has read-only access to bookings. Only partner agents can update booking status.'));
        }

        if (auth()->user()?->isAgent()) {
            if ($booking->package->agent_id !== auth()->user()->agent?->id) {
                abort(403, __('Access denied. This booking does not belong to your travel packages.'));
            }
        }

        try {
            $booking->transitionStatusByAgent($status);
            session()->flash('success', __('Booking status successfully updated to :status.', ['status' => Booking::statusLabel($status)]));
        } catch (\DomainException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $selectedBooking = $this->selectedBookingId
            ? Booking::with(['user', 'package.agent', 'schedule', 'payment'])->find($this->selectedBookingId)
            : null;

        return view('livewire.admin.booking-manager', [
            'bookings' => Booking::with(['user', 'package.agent'])
                ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
                ->latest()
                ->paginate(15),
            'selectedBooking' => $selectedBooking,
        ])->layout('components.layouts.admin');
    }
}
