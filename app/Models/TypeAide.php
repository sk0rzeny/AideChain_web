<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeAide extends Model
{
    protected $table = 'types_aide';

    protected $fillable = ['nom', 'description'];

    public function aides(): HasMany
    {
        return $this->hasMany(AideDistribuee::class);
    }
}
