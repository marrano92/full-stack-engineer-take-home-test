<?php

namespace App\Models;

use Database\Factories\OwnerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Owner extends Model
{
    use SoftDeletes;

    /** @use HasFactory<OwnerFactory> */
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
    ];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'current_owner_id');
    }
    
    public function assignments(): HasMany
    {
        return $this->hasMany(AssetOwnerAssignment::class);
    }
}
