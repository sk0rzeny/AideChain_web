<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AideDistribuee extends Model
{
    protected $table = 'aides_distribuees';

    protected $fillable = [
        'beneficiaire_id',
        'projet_aide_id',
        'type_aide_id',
        'ong_id',
        'date_distribution',
        'date_expiration',
        'hash_transaction',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_distribution' => 'date',
            'date_expiration'   => 'date',
        ];
    }

    public function beneficiaire(): BelongsTo
    {
        return $this->belongsTo(Beneficiaire::class);
    }

    public function projetAide(): BelongsTo
    {
        return $this->belongsTo(ProjetAide::class);
    }

    public function typeAide(): BelongsTo
    {
        return $this->belongsTo(TypeAide::class);
    }

    public function ong(): BelongsTo
    {
        return $this->belongsTo(Ong::class);
    }
}
