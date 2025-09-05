<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\Representante;
use App\Models\Solicitud;
use App\Models\DetalleSolicitud;
use App\Models\Mantenimiento;
use App\Models\TipoMaquinaria;
use App\Models\CategoriaMaquinaria;

class ConsultaController extends Controller
{
    // 1. Listar empleados ordenados por apellido
    public function empleadosOrdenados()
    {
        $empleados = Empleado::select('nombre', 'apellido', 'documento', 'email', 'telefono')
            ->orderBy('apellido')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $empleados
        ]);
    }

    // 2. Maquinaria pesada con mantenimientos de costo superior a 1 millón
    public function maquinariaPesadaCostosa()
    {
        $maquinaria = TipoMaquinaria::join('mantenimientos', 'tipo_maquinarias.id', '=', 'mantenimientos.tipo_maquinaria_id')
            ->join('categoria_maquinarias', 'tipo_maquinarias.categoria_id', '=', 'categoria_maquinarias.id')
            ->where('categoria_maquinarias.nombre', 'like', '%pesado%')
            ->where('mantenimientos.costo', '>', 1000000)
            ->select(
                'tipo_maquinarias.nombre as tipo_maquinaria',
                'categoria_maquinarias.nombre as categoria',
                'mantenimientos.codigo',
                'mantenimientos.nombre as mantenimiento',
                'mantenimientos.costo'
            )
            ->get();

        return response()->json([
            'status' => true,
            'data' => $maquinaria
        ]);
    }

    // 3. Empresa con mayor número de solicitudes
    public function empresaMasSolicitudes()
    {
        $empresa = Empresa::join('solicituds', 'empresas.id', '=', 'solicituds.empresa_id')
            ->select(
                'empresas.id',
                'empresas.nombre',
                'empresas.nit',
                'empresas.direccion',
                'empresas.telefono',
                'empresas.email',
                DB::raw('COUNT(solicituds.id) as total_solicitudes')
            )
            ->groupBy('empresas.id', 'empresas.nombre', 'empresas.nit', 'empresas.direccion', 'empresas.telefono', 'empresas.email')
            ->orderBy('total_solicitudes', 'desc')
            ->first();

        return response()->json([
            'status' => true,
            'data' => $empresa
        ]);
    }

    // 4. Total de máquinas con solicitud de mantenimiento de empresa Argos
    public function maquinasArgos()
    {
        $total = DetalleSolicitud::join('solicituds', 'detalle_solicituds.solicitud_id', '=', 'solicituds.id')
            ->join('empresas', 'solicituds.empresa_id', '=', 'empresas.id')
            ->where('empresas.nombre', 'like', '%Argos%')
            ->sum('detalle_solicituds.cantidad_maquinas');

        return response()->json([
            'status' => true,
            'data' => [
                'total_maquinas' => $total,
                'empresa' => 'Argos'
            ]
        ]);
    }

    // 5. Datos de las solicitudes que debe atender el Empleado con número de documento 1057896547.
    public function solicitudesEmpleado()
    {
        $solicitudes = Solicitud::whereHas('empleados', function ($query) {
            $query->where('documento', '1057896547');
        })->with('empleados')->get();

        return response()->json($solicitudes);
    }

    // 6. Representantes y empresas sin solicitudes
    public function representantesEmpresasSinSolicitudes()
    {
        $representantes = Representante::join('empresas', 'representantes.empresa_id', '=', 'empresas.id')
            ->leftJoin('solicituds', 'empresas.id', '=', 'solicituds.empresa_id')
            ->whereNull('solicituds.id')
            ->select('representantes.*', 'empresas.nombre as empresa_nombre')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $representantes
        ]);
    }

    // 7. Listado con empresa, código solicitud, máquina y valor total
    public function listadoSolicitudes()
    {
        $solicitudes = Solicitud::join('empresas', 'solicituds.empresa_id', '=', 'empresas.id')
            ->join('detalle_solicituds', 'solicituds.id', '=', 'detalle_solicituds.solicitud_id')
            ->join('mantenimientos', 'detalle_solicituds.mantenimiento_id', '=', 'mantenimientos.id')
            ->select(
                'empresas.nombre as empresa',
                'solicituds.codigo as codigo_solicitud',
                'detalle_solicituds.cantidad_maquinas',
                'detalle_solicituds.costo_total'
            )
            ->get();

        return response()->json([
            'status' => true,
            'data' => $solicitudes
        ]);
    }

    // 8. Búsqueda de solicitud por código y empleados asignados
    public function solicitudPorCodigo(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string'
        ]);

        $solicitud = Solicitud::where('codigo', $request->codigo)
            ->with([
                'empresa:id,nombre,nit',
                'detallesSolicitud.mantenimiento.tipoMaquinaria:id,nombre',
                'empleados' => function ($query) {
                    $query->select('empleados.id', 'empleados.nombre', 'empleados.apellido', 'empleados.documento')
                        ->withPivot('estado');
                }
            ])
            ->first();

        if (!$solicitud) {
            return response()->json([
                'status' => false,
                'message' => 'Solicitud no encontrada'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $solicitud
        ]);
    }

    // 9. Cantidad de mantenimientos asignados a retroexcavadoras
    public function mantenimientosRetroexcavadoras()
    {
        $cantidad = DetalleSolicitud::join('mantenimientos', 'detalle_solicituds.mantenimiento_id', '=', 'mantenimientos.id')
            ->join('tipo_maquinarias', 'mantenimientos.tipo_maquinaria_id', '=', 'tipo_maquinarias.id')
            ->where('tipo_maquinarias.nombre', 'like', '%retroexcavadora%')
            ->count();

        return response()->json([
            'status' => true,
            'data' => [
                'cantidad_mantenimientos' => $cantidad,
                'tipo_maquinaria' => 'Retroexcavadoras'
            ]
        ]);
    }

    // 10. Listado de solicitudes de octubre 2023
    public function solicitudesOctubre2023()
    {
        $solicitudes = Solicitud::join('empresas', 'solicituds.empresa_id', '=', 'empresas.id')
            ->join('detalle_solicituds', 'solicituds.id', '=', 'detalle_solicituds.solicitud_id')
            ->join('mantenimientos', 'detalle_solicituds.mantenimiento_id', '=', 'mantenimientos.id')
            ->join('tipo_maquinarias', 'mantenimientos.tipo_maquinaria_id', '=', 'tipo_maquinarias.id')
            ->whereYear('solicituds.fecha_solicitud', 2023)
            ->whereMonth('solicituds.fecha_solicitud', 10)
            ->select(
                'empresas.nombre as empresa',
                'tipo_maquinarias.nombre as maquinaria',
                'mantenimientos.codigo',
                'mantenimientos.nombre as mantenimiento',
                'detalle_solicituds.cantidad_maquinas'
            )
            ->get();

        return response()->json([
            'status' => true,
            'data' => $solicitudes
        ]);
    }
}
