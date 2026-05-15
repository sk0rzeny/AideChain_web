<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('beneficiaires', function (Blueprint $table) {
            $table->enum('genre', ['homme', 'femme', 'autre'])->change();
            $table->enum('categorie', [
                'individu',
                'famille',
                'enfant',
                'femme_chef_menage',
                'deplacement_interne',
            ])->change();
        });
    }

    public function down(): void
    {
        Schema::table('beneficiaires', function (Blueprint $table) {
            $table->enum('genre', ['homme', 'femme', 'autre'])->change();
            $table->enum('categorie', ['adulte', 'enfant', 'personne_agee', 'autre'])->change();
        });
    }
};
