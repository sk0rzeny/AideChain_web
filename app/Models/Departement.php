<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departement extends Model
{
    protected $fillable = ['nom', 'region_id'];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function villes(): HasMany
    {
        return $this->hasMany(Ville::class);
    }
}
