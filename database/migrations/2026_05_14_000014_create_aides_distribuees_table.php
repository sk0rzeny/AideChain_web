<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aides_distribuees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiaire_id')->constrained('beneficiaires')->cascadeOnDelete();
            $table->foreignId('type_aide_id')->constrained('types_aide')->cascadeOnDelete();
            $table->foreignId('ong_id')->constrained('ongs')->cascadeOnDelete();
            $table->date('date_distribution');
            $table->date('date_expiration');
            $table->string('hash_transaction', 64)->unique();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Index pour la détection de doublons
            $table->index(['beneficiaire_id', 'type_aide_id', 'date_expiration'], 'idx_doublon_check');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aides_distribuees');
    }
};
