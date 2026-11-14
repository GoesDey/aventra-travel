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
        'location',
        'latitude',
        'longitude',
        'image_path',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

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
