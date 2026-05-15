<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents_ong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ong_id')->constrained('ongs')->cascadeOnDelete();
            $table->string('type_document');
            $table->string('chemin_fichier');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents_ong');
    }
};
