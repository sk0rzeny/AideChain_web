<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiaires', function (Blueprint $table) {
            $table->id();
            $table->string('identity_hash', 64)->unique();
            $table->string('prenom');
            $table->string('nom');
            $table->date('date_naissance');
            $table->enum('genre', ['homme', 'femme', 'autre']);
            $table->enum('categorie', ['adulte', 'enfant', 'personne_agee', 'autre']);
            $table->foreignId('village_id')->nullable()->constrained('villages')->nullOnDelete();
            $table->foreignId('ong_id')->constrained('ongs')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiaires');
    }
};
