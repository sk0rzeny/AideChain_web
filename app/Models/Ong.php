<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ong extends Model
{
    protected $fillable = ['nom', 'email', 'telephone', 'adresse', 'statut', 'representant_id'];

    public function representant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'representant_id');
    }

    public function agents(): HasMany
    {
        return $this->hasMany(User::class, 'ong_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DocumentOng::class);
    }

    public function projetsAide(): HasMany
    {
        return $this->hasMany(ProjetAide::class);
    }

    public function beneficiaires(): HasMany
    {
        return $this->hasMany(Beneficiaire::class);
    }

    public function aidesDistribuees(): HasMany
    {
        return $this->hasMany(AideDistribuee::class);
    }
}
