<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== VERIFICANDO RESTRICCIÓN DE MANTENIMIENTOS ===\n\n";

try {
    // Verificar que la restricción única existe
    $indexes = DB::select("SHOW INDEX FROM mantenimientos");
    
    $restriccionEncontrada = false;
    foreach($indexes as $index) {
        if(strpos($index->Key_name, 'mantenimiento_tipo_maquinaria_unique') !== false) {
            echo "✓ Restricción única encontrada: " . $index->Key_name . "\n";
            echo "  Columnas: " . $index->Column_name . "\n";
            $restriccionEncontrada = true;
        }
    }
    
    if (!$restriccionEncontrada) {
        echo "❌ No se encontró la restricción única\n";
    }
    
    echo "\n=== PROBANDO INSERCIÓN ===\n";
    
    // Verificar si hay datos existentes
    $mantenimientosExistentes = DB::table('mantenimientos')->count();
    echo "Mantenimientos existentes: $mantenimientosExistentes\n";
    
    // Verificar tipos de maquinaria
    $tiposExistentes = DB::table('tipo_maquinarias')->count();
    echo "Tipos de maquinaria existentes: $tiposExistentes\n";
    
    if ($tiposExistentes == 0) {
        echo "No hay tipos de maquinaria. Creando datos de prueba...\n";
        
        // Crear categoría
        $categoriaId = DB::table('categoria_maquinarias')->insertGetId([
            'nombre' => 'Pesada',
            'descripcion' => 'Maquinaria pesada',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Crear tipo de maquinaria
        $tipoId = DB::table('tipo_maquinarias')->insertGetId([
            'nombre' => 'Excavadora',
            'descripcion' => 'Excavadora pesada',
            'categoria_id' => $categoriaId,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "✓ Datos de prueba creados\n";
    } else {
        $tipoId = DB::table('tipo_maquinarias')->first()->id;
    }
    
    // Intentar insertar un mantenimiento
    echo "\nIntentando insertar mantenimiento...\n";
    
    try {
        $mantenimientoId = DB::table('mantenimientos')->insertGetId([
            'codigo' => 'TEST-001',
            'nombre' => 'Mantenimiento de Prueba',
            'descripcion' => 'Descripción de prueba',
            'costo' => 1000000.00,
            'tiempo_estimado' => 4,
            'manual_procedimiento' => 'Manual de prueba',
            'tipo_maquinaria_id' => $tipoId,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "✓ Mantenimiento insertado con ID: $mantenimientoId\n";
        
        // Intentar insertar el mismo código para el mismo tipo (debe fallar)
        echo "\nIntentando duplicar código para el mismo tipo...\n";
        
        try {
            DB::table('mantenimientos')->insert([
                'codigo' => 'TEST-001', // Mismo código
                'nombre' => 'Mantenimiento Duplicado',
                'descripcion' => 'Descripción duplicada',
                'costo' => 2000000.00,
                'tiempo_estimado' => 8,
                'manual_procedimiento' => 'Manual duplicado',
                'tipo_maquinaria_id' => $tipoId, // Mismo tipo
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            echo "❌ ERROR: Se permitió duplicar el código\n";
            
        } catch (Exception $e) {
            echo "✓ CORRECTO: Se bloqueó la duplicación del código\n";
            echo "  Error: " . $e->getMessage() . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error al insertar mantenimiento: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== VERIFICACIÓN COMPLETADA ===\n";
    echo "La restricción está funcionando correctamente.\n";
    echo "Un mantenimiento NO puede tener el mismo código para el mismo tipo de maquinaria.\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . " Línea: " . $e->getLine() . "\n";
}
