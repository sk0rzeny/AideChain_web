<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandeAdhesion extends Model
{
    protected $table = 'demandes_adhesion';

    protected $fillable = ['user_id', 'ong_id', 'statut'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ong(): BelongsTo
    {
        return $this->belongsTo(Ong::class);
    }
}
