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
        Schema::create('projets_aide', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ong_id')->constrained('ongs')->cascadeOnDelete();
            $table->string('nom');
            $table->foreignId('type_aide_id')->constrained('types_aide');
            $table->date('date_expiration');
            $table->string('zone_cible')->nullable();
            $table->enum('statut', ['active', 'suspendue', 'terminee'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projets_aide');
    }
};
