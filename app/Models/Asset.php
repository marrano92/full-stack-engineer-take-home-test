<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'refence',
        'serial_number',
        'description',
        'current_owner_id',
        'current_owned_from'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'current_owner_id');
    }
}
