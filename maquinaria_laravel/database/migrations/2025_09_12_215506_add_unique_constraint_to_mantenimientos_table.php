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
        Schema::table('mantenimientos', function (Blueprint $table) {
            // Agregar restricción única para evitar que el mismo mantenimiento 
            // se pueda aplicar a diferentes tipos de maquinaria
            $table->unique(['codigo', 'tipo_maquinaria_id'], 'mantenimiento_tipo_maquinaria_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mantenimientos', function (Blueprint $table) {
            // Eliminar la restricción única
            $table->dropUnique('mantenimiento_tipo_maquinaria_unique');
        });
    }
};
