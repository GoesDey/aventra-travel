<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'package_id',
        'package_schedule_id',
        'travel_date',
        'guest_count',
        'total_price',
        'commission_rate',
        'commission_amount',
        'agent_earning',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'travel_date'        => 'date',
        'total_price'        => 'decimal:2',
        'guest_count'        => 'integer',
        'commission_rate'    => 'decimal:2',
        'commission_amount'  => 'integer',
        'agent_earning'      => 'integer',
        'expires_at'         => 'datetime',
    ];

    /**
     * Relasi One-to-One ke model Payment.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && now()->greaterThan($this->expires_at);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(PackageSchedule::class, 'package_schedule_id');
    }

    public function packageSchedule(): BelongsTo
    {
        return $this->belongsTo(PackageSchedule::class, 'package_schedule_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Guard transisi status oleh agen.
     * Agen HANYA diizinkan mengubah status:
     * - 'dibayar' -> 'berjalan'
     * - 'berjalan' -> 'selesai'
     * Semua transisi lainnya (misal: menunggu_pembayaran -> selesai) ditolak di backend.
     *
     * @throws \DomainException
     */
    public function transitionStatusByAgent(string $targetStatus): void
    {
        $allowedTransitions = [
            'dibayar'  => ['berjalan'],
            'berjalan' => ['selesai'],
        ];

        $currentStatus = $this->status;

        if (!isset($allowedTransitions[$currentStatus]) || !in_array($targetStatus, $allowedTransitions[$currentStatus], true)) {
            throw new \DomainException("Transisi status tidak diizinkan. Agen hanya boleh mengubah status dari 'dibayar' -> 'berjalan' -> 'selesai'. Status booking saat ini: '{$currentStatus}', target: '{$targetStatus}'.");
        }

        $this->update(['status' => $targetStatus]);
    }
}
