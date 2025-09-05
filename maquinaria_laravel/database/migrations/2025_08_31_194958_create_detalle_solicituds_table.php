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
        Schema::create('detalle_solicituds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained()->onDelete('cascade');
            $table->foreignId('mantenimiento_id')->constrained()->onDelete('cascade');
            $table->integer('cantidad_maquinas');
            $table->decimal('costo_total', 10, 2);
            $table->text('Url_foto');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_solicituds');
    }
};
