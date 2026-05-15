<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentOng extends Model
{
    protected $table = 'documents_ong';

    protected $fillable = ['ong_id', 'type_document', 'chemin_fichier'];

    public function ong(): BelongsTo
    {
        return $this->belongsTo(Ong::class);
    }
}
