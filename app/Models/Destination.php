<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'name',
        'slug',
        'description',
        'description_id',
        'location',
        'region',
        'latitude',
        'longitude',
        'default_stay_minutes',
        'requires_sea_crossing',
        'is_active',
        'image_path',
    ];

    protected $casts = [
        'latitude'              => 'float',
        'longitude'             => 'float',
        'default_stay_minutes'  => 'integer',
        'requires_sea_crossing' => 'boolean',
        'is_active'             => 'boolean',
    ];

    /**
     * Tampilkan deskripsi sesuai bahasa aktif (fallback ke English jika description_id kosong).
     */
    public function getLocalizedDescriptionAttribute(): string
    {
        if (app()->getLocale() === 'id' && !empty($this->description_id)) {
            return $this->description_id;
        }

        return (string) $this->description;
    }

    /**
     * Perkiraan wilayah Bali berdasarkan koordinat (lat, lng).
     */
    public static function inferRegionFromCoordinates(float $lat, float $lng): string
    {
        if ($lng < 115.05) {
            return 'barat';
        }
        if ($lng > 115.38) {
            return 'timur';
        }
        if ($lat > -8.38) {
            return 'utara';
        }

        return 'selatan';
    }

    /**
     * Relasi ke model Agent.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class)
            ->withPivot('visit_order')
            ->orderByPivot('visit_order');
    }
}
