<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Beneficiaire extends Model
{
    protected $table = 'beneficiaires';

    protected $fillable = [
        'identity_hash',
        'prenom',
        'nom',
        'date_naissance',
        'genre',
        'categorie',
        'village_id',
        'ong_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
        ];
    }

    public function ong(): BelongsTo
    {
        return $this->belongsTo(Ong::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function aides(): HasMany
    {
        return $this->hasMany(AideDistribuee::class);
    }
}
