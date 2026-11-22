<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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
        $label = self::REGIONS[$region] ?? ucfirst((string) $region);

        return __($label);
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
     * Resolve cover image URL if the file exists (supports external URL, public/images/..., or storage/...).
     * Returns null when no image is set or the referenced local file is missing.
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        if (!$this->cover_image) {
            return null;
        }

        if (str_starts_with($this->cover_image, 'http://') || str_starts_with($this->cover_image, 'https://')) {
            return $this->cover_image;
        }

        if (str_starts_with($this->cover_image, 'images/')) {
            return file_exists(public_path($this->cover_image))
                ? asset($this->cover_image)
                : null;
        }

        return Storage::disk('public')->exists($this->cover_image)
            ? asset('storage/' . $this->cover_image)
            : null;
    }

    /**
     * Gradient background class for fallback cover cards based on Bali region.
     */
    public function getRegionGradientAttribute(): string
    {
        return match ($this->region) {
            'utara'   => 'from-oceanic-deep via-cyan-800 to-tropical-teal',
            'selatan' => 'from-oceanic-deep via-sky-800 to-teal-600',
            'timur'   => 'from-indigo-950 via-oceanic-deep to-tropical-teal',
            'barat'   => 'from-emerald-950 via-teal-800 to-tropical-teal',
            default   => 'from-oceanic-deep via-tropical-teal to-secondary',
        };
    }

    /**
     * Material Symbol icon name for fallback cover cards based on Bali region.
     */
    public function getRegionIconAttribute(): string
    {
        return match ($this->region) {
            'utara'   => 'landscape',
            'selatan' => 'waves',
            'timur'   => 'temple_hindu',
            'barat'   => 'forest',
            default   => 'travel_explore',
        };
    }

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
