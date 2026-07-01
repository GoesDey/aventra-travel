<?php

namespace App\Livewire\Customer;

use App\Models\Booking;
use App\Models\Review;
use Livewire\Component;

class ReviewModal extends Component
{
    public int $bookingId = 0;
    public bool $show = false;
    public int $rating = 5;
    public string $comment = '';

    protected function rules(): array
    {
        return [
            'bookingId' => 'required|integer|exists:bookings,id',
            'rating'    => 'required|integer|min:1|max:5',
            'comment'   => 'required|string|max:500',
        ];
    }

    public function submitReview(): void
    {
        $this->validate();

        $booking = Booking::with('package')->findOrFail($this->bookingId);

        // Prevent duplicate reviews
        if ($booking->review()->exists()) {
            $this->show = false;
            return;
        }

        Review::create([
            'booking_id' => $booking->id,
            'package_id' => $booking->package_id,
            'user_id'    => auth()->id(),
            'rating'     => $this->rating,
            'comment'    => $this->comment,
        ]);

        $this->updatePackageRating($booking);

        $this->show = false;
        $this->reset(['bookingId', 'rating', 'comment']);
        $this->rating = 5;
        $this->dispatch('reviewSubmitted');
        session()->flash('success', 'Terima kasih atas ulasan Anda!');
    }

    private function updatePackageRating(Booking $booking): void
    {
        $package = $booking->package;
        $avgRating = $package->reviews()->avg('rating');
        $package->update(['avg_rating' => round($avgRating, 2)]);
    }

    public function render()
    {
        return view('livewire.customer.review-modal');
    }
}
