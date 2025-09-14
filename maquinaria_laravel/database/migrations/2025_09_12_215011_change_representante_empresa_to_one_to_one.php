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
        // Agregar restricción unique para hacer la relación 1:1
        Schema::table('representantes', function (Blueprint $table) {
            $table->unique('empresa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar la restricción unique
        Schema::table('representantes', function (Blueprint $table) {
            $table->dropUnique(['empresa_id']);
        });
    }
};
