<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'order_id',
        'transaction_id',
        'snap_token',
        'amount',
        'payment_type',
        'payment_status',
        'raw_notification',
        'paid_at',
    ];

    protected $casts = [
        'amount'           => 'integer',
        'raw_notification' => 'array',
        'paid_at'          => 'datetime',
    ];

    /**
     * Relasi ke model Booking.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function isSuccess(): bool
    {
        return $this->payment_status === 'success';
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->payment_status === 'failed';
    }

    public function isExpired(): bool
    {
        return $this->payment_status === 'expired';
    }
}
