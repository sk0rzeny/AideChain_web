<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Village extends Model
{
    protected $fillable = ['nom', 'ville_id'];

    public function ville(): BelongsTo
    {
        return $this->belongsTo(Ville::class);
    }

    public function beneficiaires(): HasMany
    {
        return $this->hasMany(Beneficiaire::class);
    }
}
