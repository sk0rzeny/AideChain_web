<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    protected $fillable = ['nom', 'pays_id'];

    public function pays(): BelongsTo
    {
        return $this->belongsTo(Pays::class);
    }

    public function departements(): HasMany
    {
        return $this->hasMany(Departement::class);
    }
}
