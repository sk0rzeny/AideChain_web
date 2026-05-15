<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ville extends Model
{
    protected $fillable = ['nom', 'departement_id'];

    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departement::class);
    }

    public function villages(): HasMany
    {
        return $this->hasMany(Village::class);
    }
}
