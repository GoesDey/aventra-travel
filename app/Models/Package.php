<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'name',
        'slug',
        'description',
        'category',
        'region',
        'price_per_pax',
        'duration_days',
        'max_guests',
        'avg_rating',
        'cover_image',
        'is_active',
    ];

    protected $casts = [
        'price_per_pax' => 'decimal:2',
        'avg_rating'    => 'decimal:2',
        'is_active'     => 'boolean',
    ];

    /**
     * Relasi ke model Agent.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Relasi ke model PackageSchedule.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(PackageSchedule::class);
    }

    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(Destination::class)
            ->withPivot('visit_order')
            ->orderByPivot('visit_order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
