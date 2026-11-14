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

    /**
     * Daftar region resmi (nilai enum kolom `packages.region` => label tampilan).
     * Single source of truth untuk validasi & filter di backend.
     */
    public const REGIONS = [
        'utara'   => 'North Bali',
        'selatan' => 'South Bali',
        'timur'   => 'East Bali',
        'barat'   => 'West Bali',
    ];

    /**
     * Daftar nilai enum region yang valid.
     *
     * @return array<int, string>
     */
    public static function regionKeys(): array
    {
        return array_keys(self::REGIONS);
    }

    /**
     * Format label region yang mudah dibaca (contoh: 'selatan' => 'South Bali').
     */
    public static function formatRegion(?string $region): string
    {
        return self::REGIONS[$region] ?? ucfirst((string) $region);
    }

    /**
     * Accessor untuk $package->region_label
     */
    public function getRegionLabelAttribute(): string
    {
        return self::formatRegion($this->region);
    }

    /**
     * Dukung pemanggilan dinamis $package->regionLabel() dan parameter kustom.
     */
    public function __call($method, $parameters)
    {
        if ($method === 'regionLabel') {
            $region = $parameters[0] ?? $this->region;
            return self::formatRegion($region);
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Dukung pemanggilan statis Package::regionLabel('selatan').
     */
    public static function __callStatic($method, $parameters)
    {
        if ($method === 'regionLabel') {
            return self::formatRegion($parameters[0] ?? null);
        }

        return parent::__callStatic($method, $parameters);
    }

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
