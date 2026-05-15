<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjetAide extends Model
{
    protected $table = 'projets_aide';

    protected $fillable = [
        'ong_id',
        'nom',
        'type_aide_id',
        'date_expiration',
        'zone_cible',
        'statut',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'date_expiration' => 'date',
        ];
    }

    public function ong(): BelongsTo
    {
        return $this->belongsTo(Ong::class);
    }

    public function typeAide(): BelongsTo
    {
        return $this->belongsTo(TypeAide::class);
    }

    public function distributions(): HasMany
    {
        return $this->hasMany(AideDistribuee::class);
    }
}
