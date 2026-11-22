<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    public const STATUS_LABELS = [
        'menunggu_pembayaran' => 'Awaiting Payment',
        'dibayar'             => 'Paid',
        'berjalan'            => 'Ongoing',
        'selesai'             => 'Completed',
        'dibatalkan'          => 'Cancelled',
        'kedaluwarsa'         => 'Expired',
        'perlu_refund'        => 'Refund Needed',
    ];

    public static function statusLabel(?string $status): string
    {
        if (!$status) {
            return __('Unknown');
        }

        $label = self::STATUS_LABELS[$status] ?? ucfirst(str_replace('_', ' ', $status));

        return __($label);
    }

    protected $fillable = [
        'booking_code',
        'user_id',
        'package_id',
        'package_schedule_id',
        'travel_date',
        'guest_count',
        'total_price',
        'is_custom',
        'start_point',
        'travel_time_hours',
        'stay_time_hours',
        'travel_distance_km',
        'total_hours',
        'overtime_hours',
        'overtime_charge',
        'custom_destinations',
        'special_requests',
        'commission_rate',
        'commission_amount',
        'agent_earning',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'travel_date'         => 'date',
        'total_price'         => 'decimal:2',
        'guest_count'         => 'integer',
        'is_custom'           => 'boolean',
        'travel_time_hours'   => 'decimal:2',
        'stay_time_hours'     => 'decimal:2',
        'travel_distance_km'  => 'decimal:2',
        'total_hours'         => 'decimal:2',
        'overtime_hours'      => 'integer',
        'overtime_charge'     => 'integer',
        'custom_destinations' => 'array',
        'commission_rate'     => 'decimal:2',
        'commission_amount'   => 'integer',
        'agent_earning'       => 'integer',
        'expires_at'          => 'datetime',
    ];

    /**
     * Hitung jam lembur & biaya tambahan untuk Custom Itinerary.
     *
     * Aturan:
     * - total_hours <= custom_base_hours  => overtime 0, charge 0
     * - total_hours >  custom_base_hours  => overtime_hours = ceil(total - base),
     *                                        overtime_charge = overtime_hours * rate
     *
     * @return array{base_hours: int, rate_per_hour: int, total_hours: float, is_overtime: bool, overtime_hours: int, overtime_charge: int}
     */
    public static function calculateOvertime(float $totalHours): array
    {
        $baseHours   = (int) config('trihita.custom_base_hours', 8);
        $ratePerHour = (int) config('trihita.overtime_rate_per_hour', 75000);
        $total       = max(0.0, round($totalHours, 2));

        $overtimeHours = $total > $baseHours ? (int) ceil($total - $baseHours) : 0;

        return [
            'base_hours'      => $baseHours,
            'rate_per_hour'   => $ratePerHour,
            'total_hours'     => $total,
            'is_overtime'     => $overtimeHours > 0,
            'overtime_hours'  => $overtimeHours,
            'overtime_charge' => $overtimeHours * $ratePerHour,
        ];
    }

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
     * Agen diizinkan mengubah status:
     * - 'dibayar' -> 'berjalan' atau 'dibatalkan'
     * - 'berjalan' -> 'selesai' atau 'dibatalkan'
     * Semua transisi lainnya ditolak di backend.
     *
     * @throws \DomainException
     */
    public function transitionStatusByAgent(string $targetStatus): void
    {
        $allowedTransitions = [
            'dibayar'  => ['berjalan', 'dibatalkan'],
            'berjalan' => ['selesai', 'dibatalkan'],
        ];

        $currentStatus = $this->status;

        if (!isset($allowedTransitions[$currentStatus]) || !in_array($targetStatus, $allowedTransitions[$currentStatus], true)) {
            $currentLabel = self::statusLabel($currentStatus);
            $targetLabel  = self::statusLabel($targetStatus);
            throw new \DomainException(__("Status transition is not allowed. Current status: ':current', target: ':target'.", [
                'current' => $currentLabel,
                'target'  => $targetLabel,
            ]));
        }

        if ($targetStatus === 'dibatalkan' && $currentStatus === 'dibayar' && $this->package_schedule_id) {
            PackageSchedule::where('id', $this->package_schedule_id)->increment('quota', $this->guest_count);
        }

        $this->update(['status' => $targetStatus]);
    }
}
