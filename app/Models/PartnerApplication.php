<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'whatsapp_number',
        'nib_number',
        'operating_regions',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'operating_regions' => 'array',
            'reviewed_at'       => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function regionLabels(): array
    {
        $map = [
            'utara'   => __('North Bali'),
            'selatan' => __('South Bali'),
            'timur'   => __('East Bali'),
            'barat'   => __('West Bali'),
        ];

        return collect($this->operating_regions ?? [])
            ->map(fn ($region) => $map[$region] ?? ucfirst((string) $region))
            ->values()
            ->all();
    }
}
