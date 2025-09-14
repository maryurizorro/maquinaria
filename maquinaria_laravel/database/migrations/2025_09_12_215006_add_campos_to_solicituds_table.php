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
        Schema::table('solicituds', function (Blueprint $table) {
            if (!Schema::hasColumn('solicituds', 'descripcion_solicitud')) {
                $table->text('descripcion_solicitud')->nullable()->after('observaciones');
            }
            if (!Schema::hasColumn('solicituds', 'fecha_deseada')) {
                $table->date('fecha_deseada')->nullable()->after('descripcion_solicitud');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicituds', function (Blueprint $table) {
            $table->dropColumn(['descripcion_solicitud', 'fecha_deseada']);
        });
    }
};
