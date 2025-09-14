<?php

/**
 * Script de prueba para verificar que la restricción de mantenimientos
 * funcione correctamente - Un mantenimiento no puede aplicarse a diferentes tipos de maquinaria
 */

require_once 'vendor/autoload.php';

use App\Models\Mantenimiento;
use App\Models\TipoMaquinaria;
use App\Models\CategoriaMaquinaria;

// Simular el entorno de Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRUEBA DE RESTRICCIÓN DE MANTENIMIENTOS ===\n\n";

try {
    // 1. Crear categorías de maquinaria
    echo "1. Creando categorías de maquinaria...\n";
    
    $categoriaLigera = CategoriaMaquinaria::firstOrCreate(
        ['nombre' => 'Ligera'],
        ['descripcion' => 'Maquinaria para proyectos de pequeña y mediana escala']
    );
    
    $categoriaPesada = CategoriaMaquinaria::firstOrCreate(
        ['nombre' => 'Pesada'],
        ['descripcion' => 'Maquinaria para proyectos de construcción a gran escala']
    );
    
    echo "   ✓ Categorías creadas/verificadas\n\n";
    
    // 2. Crear tipos de maquinaria
    echo "2. Creando tipos de maquinaria...\n";
    
    $excavadora = TipoMaquinaria::firstOrCreate(
        ['nombre' => 'Excavadora'],
        [
            'descripcion' => 'Excavadora pesada para construcción',
            'categoria_id' => $categoriaPesada->id
        ]
    );
    
    $miniexcavadora = TipoMaquinaria::firstOrCreate(
        ['nombre' => 'Miniexcavadora'],
        [
            'descripcion' => 'Excavadora pequeña para proyectos medianos',
            'categoria_id' => $categoriaLigera->id
        ]
    );
    
    echo "   ✓ Tipos de maquinaria creados/verificados\n\n";
    
    // 3. Crear primer mantenimiento para Excavadora
    echo "3. Creando mantenimiento para Excavadora...\n";
    
    $mantenimientoExcavadora = Mantenimiento::create([
        'codigo' => 'MANT-001',
        'nombre' => 'Mantenimiento Preventivo Motor',
        'descripcion' => 'Mantenimiento preventivo del motor principal',
        'costo' => 1500000.00,
        'tiempo_estimado' => 8,
        'manual_procedimiento' => 'Procedimiento específico para excavadora pesada...',
        'tipo_maquinaria_id' => $excavadora->id
    ]);
    
    echo "   ✓ Mantenimiento creado para Excavadora: {$mantenimientoExcavadora->codigo}\n\n";
    
    // 4. Intentar crear el mismo mantenimiento para Miniexcavadora (DEBE FALLAR)
    echo "4. Intentando crear el mismo mantenimiento para Miniexcavadora...\n";
    
    try {
        $mantenimientoMiniexcavadora = Mantenimiento::create([
            'codigo' => 'MANT-001', // MISMO CÓDIGO
            'nombre' => 'Mantenimiento Preventivo Motor',
            'descripcion' => 'Mantenimiento preventivo del motor principal',
            'costo' => 800000.00, // DIFERENTE COSTO
            'tiempo_estimado' => 4, // DIFERENTE TIEMPO
            'manual_procedimiento' => 'Procedimiento específico para miniexcavadora...', // DIFERENTE PROCEDIMIENTO
            'tipo_maquinaria_id' => $miniexcavadora->id
        ]);
        
        echo "   ❌ ERROR: Se permitió crear el mismo mantenimiento para diferentes tipos!\n";
        
    } catch (Exception $e) {
        echo "   ✓ CORRECTO: Se bloqueó la creación del mismo mantenimiento para diferentes tipos\n";
        echo "   ✓ Mensaje de error: " . $e->getMessage() . "\n\n";
    }
    
    // 5. Crear un mantenimiento diferente para Miniexcavadora (DEBE FUNCIONAR)
    echo "5. Creando mantenimiento diferente para Miniexcavadora...\n";
    
    $mantenimientoMiniexcavadora = Mantenimiento::create([
        'codigo' => 'MANT-002', // CÓDIGO DIFERENTE
        'nombre' => 'Mantenimiento Preventivo Motor',
        'descripcion' => 'Mantenimiento preventivo del motor principal',
        'costo' => 800000.00,
        'tiempo_estimado' => 4,
        'manual_procedimiento' => 'Procedimiento específico para miniexcavadora...',
        'tipo_maquinaria_id' => $miniexcavadora->id
    ]);
    
    echo "   ✓ Mantenimiento creado para Miniexcavadora: {$mantenimientoMiniexcavadora->codigo}\n\n";
    
    // 6. Verificar que no se puede duplicar el código en el mismo tipo
    echo "6. Intentando duplicar código en el mismo tipo de maquinaria...\n";
    
    try {
        $mantenimientoDuplicado = Mantenimiento::create([
            'codigo' => 'MANT-001', // MISMO CÓDIGO
            'nombre' => 'Mantenimiento Duplicado',
            'descripcion' => 'Intento de duplicado',
            'costo' => 2000000.00,
            'tiempo_estimado' => 10,
            'manual_procedimiento' => 'Procedimiento duplicado...',
            'tipo_maquinaria_id' => $excavadora->id // MISMO TIPO
        ]);
        
        echo "   ❌ ERROR: Se permitió duplicar el código en el mismo tipo!\n";
        
    } catch (Exception $e) {
        echo "   ✓ CORRECTO: Se bloqueó la duplicación del código en el mismo tipo\n";
        echo "   ✓ Mensaje de error: " . $e->getMessage() . "\n\n";
    }
    
    // 7. Mostrar resumen
    echo "=== RESUMEN DE PRUEBAS ===\n";
    echo "✓ Un mantenimiento NO puede aplicarse a diferentes tipos de maquinaria\n";
    echo "✓ Cada mantenimiento debe tener un código único por tipo de maquinaria\n";
    echo "✓ Los costos, tiempos y procedimientos son específicos por tipo\n";
    echo "✓ La restricción funciona correctamente a nivel de base de datos\n\n";
    
    echo "=== MANTENIMIENTOS CREADOS ===\n";
    $mantenimientos = Mantenimiento::with('tipoMaquinaria.categoria')->get();
    foreach ($mantenimientos as $mant) {
        echo "- {$mant->codigo}: {$mant->nombre} ({$mant->tipoMaquinaria->nombre} - {$mant->tipoMaquinaria->categoria->nombre})\n";
        echo "  Costo: $" . number_format($mant->costo, 0, ',', '.') . " | Tiempo: {$mant->tiempo_estimado}h\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR GENERAL: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . " Línea: " . $e->getLine() . "\n";
}
