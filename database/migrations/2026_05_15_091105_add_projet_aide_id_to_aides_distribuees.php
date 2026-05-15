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
        Schema::table('aides_distribuees', function (Blueprint $table) {
            $table->foreignId('projet_aide_id')->nullable()->after('ong_id')
                  ->constrained('projets_aide')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('aides_distribuees', function (Blueprint $table) {
            $table->dropForeign(['projet_aide_id']);
            $table->dropColumn('projet_aide_id');
        });
    }
};
