<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Agent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'agent_name',
        'description',
        'address',
        'phone_number',
        'vehicle_count',
        'nib_number',
        'logo_path',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'vehicle_count' => 'integer',
        ];
    }

    /**
     * Relasi ke model User (Inverse One-to-One / BelongsTo).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi One-to-Many ke model Package.
     */
    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    /**
     * Relasi HasManyThrough ke model Booking melalui Package.
     */
    public function bookings(): HasManyThrough
    {
        return $this->hasManyThrough(Booking::class, Package::class);
    }

    /**
     * Relasi One-to-Many ke model Destination.
     */
    public function destinations(): HasMany
    {
        return $this->hasMany(Destination::class);
    }
}
