<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\RepresentanteController;
use App\Http\Controllers\CategoriaMaquinariaController;
use App\Http\Controllers\TipoMaquinariaController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\DetalleSolicitudController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\SolicitudEmpleadoController;
use App\Http\Controllers\ConsultaController;


// Ruta de prueba para verificar que la API funciona
Route::get('/', function () {
    return response()->json([
        'message' => 'API de Maquinaria funcionando correctamente',
        'version' => '1.0.0',
        'timestamp' => now()
    ]);
});

// Ruta para obtener información del usuario autenticado
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas de autenticación (públicas)
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Autenticación
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    // EMPRESAS 
    Route::get('empresas', [EmpresaController::class, 'index']);      
    Route::post('empresas', [EmpresaController::class, 'store']);    
    Route::get('empresas/{id}', [EmpresaController::class, 'show']);  
    Route::put('empresas/{id}', [EmpresaController::class, 'update']); 
    Route::delete('empresas/{id}', [EmpresaController::class, 'destroy']);

    // REPRESENTANTES 
    Route::get('representantes', [RepresentanteController::class, 'index']);
    Route::post('representantes', [RepresentanteController::class, 'store']);
    Route::get('representantes/{id}', [RepresentanteController::class, 'show']);
    Route::put('representantes/{id}', [RepresentanteController::class, 'update']);
    Route::delete('representantes/{id}', [RepresentanteController::class, 'destroy']);

    // CATEGORÍAS DE MAQUINARIA 
    Route::get('categorias', [CategoriaMaquinariaController::class, 'index']);
    Route::post('categorias', [CategoriaMaquinariaController::class, 'store']);
    Route::get('categorias/{id}', [CategoriaMaquinariaController::class, 'show']);
    Route::put('categorias/{id}', [CategoriaMaquinariaController::class, 'update']);
    Route::delete('categorias/{id}', [CategoriaMaquinariaController::class, 'destroy']);

    // TIPOS DE MAQUINARIA
    Route::get('tipos-maquinaria', [TipoMaquinariaController::class, 'index']);
    Route::post('tipos-maquinaria', [TipoMaquinariaController::class, 'store']);
    Route::get('tipos-maquinaria/{id}', [TipoMaquinariaController::class, 'show']);
    Route::put('tipos-maquinaria/{id}', [TipoMaquinariaController::class, 'update']);
    Route::delete('tipos-maquinaria/{id}', [TipoMaquinariaController::class, 'destroy']);

    // MANTENIMIENTOS 
    Route::get('mantenimientos', [MantenimientoController::class, 'index']);
    Route::post('mantenimientos', [MantenimientoController::class, 'store']);
    Route::get('mantenimientos/{id}', [MantenimientoController::class, 'show']);
    Route::put('mantenimientos/{id}', [MantenimientoController::class, 'update']);
    Route::delete('mantenimientos/{id}', [MantenimientoController::class, 'destroy']);

    // SOLICITUDES
    Route::get('solicitudes', [SolicitudController::class, 'index']);
    Route::post('solicitudes', [SolicitudController::class, 'store']);
    Route::get('solicitudes/{id}', [SolicitudController::class, 'show']);
    Route::put('solicitudes/{id}', [SolicitudController::class, 'update']);
    Route::delete('solicitudes/{id}', [SolicitudController::class, 'destroy']);

    // DETALLE DE SOLICITUDES
    Route::get('detalle-solicitudes', [DetalleSolicitudController::class, 'index']);
    Route::post('detalle-solicitudes', [DetalleSolicitudController::class, 'store']);
    Route::get('detalle-solicitudes/{id}', [DetalleSolicitudController::class, 'show']);
    Route::put('detalle-solicitudes/{id}', [DetalleSolicitudController::class, 'update']);
    Route::delete('detalle-solicitudes/{id}', [DetalleSolicitudController::class, 'destroy']);

    // EMPLEADOS
    Route::get('empleados', [EmpleadoController::class, 'index']);
    Route::post('empleados', [EmpleadoController::class, 'store']);
    Route::get('empleados/{id}', [EmpleadoController::class, 'show']);
    Route::put('empleados/{id}', [EmpleadoController::class, 'update']);
    Route::delete('empleados/{id}', [EmpleadoController::class, 'destroy']);

    // ASIGNACIÓN DE EMPLEADOS A SOLICITUD
    Route::get('solicitud-empleados', [SolicitudEmpleadoController::class, 'index']);
    Route::post('solicitud-empleados', [SolicitudEmpleadoController::class, 'store']);
    Route::get('solicitud-empleados/{id}', [SolicitudEmpleadoController::class, 'show']);
    Route::put('solicitud-empleados/{id}', [SolicitudEmpleadoController::class, 'update']);
    Route::delete('solicitud-empleados/{id}', [SolicitudEmpleadoController::class, 'destroy']);

    // CONSULTAS EXTRAS
    Route::prefix('consultas')->group(function () {
        Route::get('empleados-ordenados', [ConsultaController::class, 'empleadosOrdenados']);
        Route::get('maquinaria-pesada-costosa', [ConsultaController::class, 'maquinariaPesadaCostosa']);
        Route::get('empresa-mas-solicitudes', [ConsultaController::class, 'empresaMasSolicitudes']);
        Route::get('maquinas-argos', [ConsultaController::class, 'maquinasArgos']);
        Route::get('solicitudes-empleado', [ConsultaController::class, 'solicitudesEmpleado']);
        Route::get('representantes-sin-solicitudes', [ConsultaController::class, 'representantesEmpresasSinSolicitudes']);
        Route::get('listado-solicitudes', [ConsultaController::class, 'listadoSolicitudes']);
        Route::post('solicitud-por-codigo', [ConsultaController::class, 'solicitudPorCodigo']);
        Route::get('mantenimientos-retroexcavadoras', [ConsultaController::class, 'mantenimientosRetroexcavadoras']);
        Route::get('solicitudes-octubre-2023', [ConsultaController::class, 'solicitudesOctubre2023']);
    });

    // Rutas adicionales útiles
    Route::prefix('dashboard')->group(function () {
        // Estadísticas generales
        Route::get('stats', function () {
            return response()->json([
                'total_empresas' => \App\Models\Empresa::count(),
                'total_solicitudes' => \App\Models\Solicitud::count(),
                'total_empleados' => \App\Models\Empleado::count(),
                'total_mantenimientos' => \App\Models\Mantenimiento::count(),
                'solicitudes_pendientes' => \App\Models\Solicitud::where('estado', 'pendiente')->count(),
                'solicitudes_en_proceso' => \App\Models\Solicitud::where('estado', 'en_proceso')->count(),
                'solicitudes_completadas' => \App\Models\Solicitud::where('estado', 'completada')->count(),
            ]);
        });

        // Solicitudes por estado
        Route::get('solicitudes-por-estado', function () {
            $estados = \App\Models\Solicitud::selectRaw('estado, COUNT(*) as total')
                ->groupBy('estado')
                ->get();
            
            return response()->json(['data' => $estados]);
        });

        // Top 5 empresas con más solicitudes
        Route::get('top-empresas', function () {
            $empresas = \App\Models\Empresa::join('solicituds', 'empresas.id', '=', 'solicituds.empresa_id')
                ->selectRaw('empresas.nombre, COUNT(solicituds.id) as total_solicitudes')
                ->groupBy('empresas.id', 'empresas.nombre')
                ->orderBy('total_solicitudes', 'desc')
                ->limit(5)
                ->get();
            
            return response()->json(['data' => $empresas]);
        });
    });
});

// Ruta para manejar rutas no encontradas
Route::fallback(function () {
    return response()->json([
        'message' => 'Endpoint no encontrado',
        'status' => 404
    ], 404);
});
