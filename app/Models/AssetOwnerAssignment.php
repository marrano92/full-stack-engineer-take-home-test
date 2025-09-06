<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetOwnerAssignment extends Model
{
    protected $fillable = [
        'asset_id',
        'owner_id',
        'owned_from',
        'owned_to'
    ];

    protected $casts = [
        'owned_from' => 'datetime',
        'owned_to' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNull('owned_to');
    }

    public function scopeForAsset(Builder $query, int $assetId): Builder
    {
        return $query->where('asset_id', $assetId);
    }

    public function scopeForOwner(Builder $query, int $ownerId): Builder
    {
        return $query->where('owner_id', $ownerId);
    }
}
