<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pays extends Model
{
    protected $table = 'pays';

    protected $fillable = ['nom'];

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }
}
