<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'price_per_pax',
        'duration_days', 'max_guests', 'avg_rating', 'cover_image', 'is_active',
    ];

    protected $casts = [
        'price_per_pax' => 'decimal:2',
        'avg_rating'    => 'decimal:2',
        'is_active'     => 'boolean',
    ];

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
