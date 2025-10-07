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
    /**
     * @group Consultas
     * 
     * Listar empleados ordenados por apellido
     * 
     * Devuelve una lista de todos los empleados ordenados alfabéticamente por el campo `apellido`.
     * 
     * @response 200 {
     *   "status": true,
     *   "data": [
     *     {
     *       "nombre": "Juan",
     *       "apellido": "Arias",
     *       "documento": "1057893210",
     *       "email": "juan@empresa.com",
     *       "telefono": "3100000000"
     *     }
     *   ]
     * }
     */
    public function empleadosOrdenados()
    {
        $empleados = Empleado::select('nombre', 'apellido', 'documento', 'email', 'telefono')
            ->orderBy('apellido')
            ->get();

        return response()->json(['status' => true, 'data' => $empleados]);
    }

    /**
     * @group Consultas
     * 
     * Listar maquinaria pesada con mantenimientos superiores a 1 millón
     * 
     * Obtiene la maquinaria de tipo "Pesada" cuyos mantenimientos superen los $1,000,000.
     * 
     * @response 200 {
     *   "status": true,
     *   "data": [
     *     {
     *       "tipo_maquinaria": "Excavadora",
     *       "categoria": "Pesada",
     *       "codigo": "MNT001",
     *       "mantenimiento": "Cambio de motor",
     *       "costo": 1500000
     *     }
     *   ]
     * }
     */
    public function maquinariaPesadaCostosa()
    {
        $maquinaria = TipoMaquinaria::join('mantenimientos', 'tipo_maquinarias.id', '=', 'mantenimientos.tipo_maquinaria_id')
            ->join('categoria_maquinarias', 'tipo_maquinarias.categoria_id', '=', 'categoria_maquinarias.id')
            ->where('categoria_maquinarias.nombre', 'like', '%Pesada%')
            ->where('mantenimientos.costo', '>', 1000000)
            ->select(
                'tipo_maquinarias.nombre as tipo_maquinaria',
                'categoria_maquinarias.nombre as categoria',
                'mantenimientos.codigo',
                'mantenimientos.nombre as mantenimiento',
                'mantenimientos.costo'
            )
            ->get();

        return response()->json(['status' => true, 'data' => $maquinaria]);
    }

    /**
     * @group Consultas
     * 
     * Empresa con mayor número de solicitudes
     * 
     * Devuelve la empresa que ha realizado la mayor cantidad de solicitudes de mantenimiento.
     * 
     * @response 200 {
     *   "status": true,
     *   "data": {
     *     "id": 1,
     *     "nombre": "Constructora Andes",
     *     "nit": "900123456-7",
     *     "direccion": "Calle 10 #20-30",
     *     "telefono": "3101234567",
     *     "email": "contacto@andes.com",
     *     "total_solicitudes": 8
     *   }
     * }
     */
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

        return response()->json(['status' => true, 'data' => $empresa]);
    }

    /**
     * @group Consultas
     * 
     * Total de máquinas con solicitud de mantenimiento de empresa Argos
     * 
     * Calcula la cantidad total de máquinas solicitadas por la empresa "Argos".
     * 
     * @response 200 {
     *   "status": true,
     *   "data": {
     *     "total_maquinas": 25,
     *     "empresa": "Argos"
     *   }
     * }
     */
    public function maquinasArgos()
    {
        $total = DetalleSolicitud::join('solicituds', 'detalle_solicituds.solicitud_id', '=', 'solicituds.id')
            ->join('empresas', 'solicituds.empresa_id', '=', 'empresas.id')
            ->where('empresas.nombre', 'like', '%Argos%')
            ->sum('detalle_solicituds.cantidad_maquinas');

        return response()->json([
            'status' => true,
            'data' => ['total_maquinas' => $total, 'empresa' => 'Argos']
        ]);
    }

    /**
     * @group Consultas
     * 
     * Solicitudes asignadas al empleado con documento 1057896547
     * 
     * Devuelve todas las solicitudes que debe atender un empleado específico.
     * 
     * @response 200 {
     *   "status": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "codigo": "SOL123",
     *       "fecha_solicitud": "2023-10-10",
     *       "empleados": [
     *         {
     *           "nombre": "Carlos",
     *           "apellido": "Gomez",
     *           "documento": "1057896547"
     *         }
     *       ]
     *     }
     *   ]
     * }
     */
    public function solicitudesEmpleado()
    {
        $documento = '1057896547';

        $solicitudes = Solicitud::whereHas('empleados', function ($query) use ($documento) {
                $query->where('documento', $documento);
            })
            ->with(['empleados' => function ($query) use ($documento) {
                $query->where('documento', $documento);
            }])
            ->get();

        return response()->json(['status' => true, 'data' => $solicitudes]);
    }

    /**
     * @group Consultas
     * 
     * Representantes y empresas sin solicitudes
     * 
     * Muestra los representantes y las empresas que no han realizado solicitudes.
     * 
     * @response 200 {
     *   "status": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "nombre": "Pedro Martínez",
     *       "empresa_nombre": "Maquinarias del Norte"
     *     }
     *   ]
     * }
     */
    public function representantesEmpresasSinSolicitudes()
    {
        $representantes = Representante::join('empresas', 'representantes.empresa_id', '=', 'empresas.id')
            ->leftJoin('solicituds', 'empresas.id', '=', 'solicituds.empresa_id')
            ->whereNull('solicituds.id')
            ->select('representantes.*', 'empresas.nombre as empresa_nombre')
            ->get();

        return response()->json(['status' => true, 'data' => $representantes]);
    }

    /**
     * @group Consultas
     * 
     * Listado de solicitudes con empresa, código, máquina y valor total
     * 
     * Devuelve un listado completo de solicitudes, con la empresa, código, cantidad de máquinas y costo total.
     * 
     * @response 200 {
     *   "status": true,
     *   "data": [
     *     {
     *       "empresa": "Constructora Sur",
     *       "codigo_solicitud": "SOL456",
     *       "cantidad_maquinas": 3,
     *       "costo_total": 4500000
     *     }
     *   ]
     * }
     */
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

        return response()->json(['status' => true, 'data' => $solicitudes]);
    }

    /**
     * @group Consultas
     * 
     * Buscar solicitud por código y empleados asignados
     * 
     * Busca una solicitud por su código y devuelve información detallada incluyendo empleados y mantenimientos.
     * 
     * @bodyParam codigo string required Código de la solicitud. Example: SOL123
     * 
     * @response 200 {
     *   "status": true,
     *   "data": {
     *     "codigo": "SOL123",
     *     "empresa": {
     *       "nombre": "Constructora Andes",
     *       "nit": "900123456-7"
     *     },
     *     "empleados": [
     *       {
     *         "nombre": "Carlos",
     *         "apellido": "Gomez",
     *         "documento": "1057896547",
     *         "pivot": { "estado": "pendiente" }
     *       }
     *     ]
     *   }
     * }
     * 
     * @response 404 {
     *   "status": false,
     *   "message": "Solicitud no encontrada"
     * }
     */
    public function solicitudPorCodigo(Request $request)
    {
        $request->validate(['codigo' => 'required|string']);

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
            return response()->json(['status' => false, 'message' => 'Solicitud no encontrada'], 404);
        }

        return response()->json(['status' => true, 'data' => $solicitud]);
    }

    /**
     * @group Consultas
     * 
     * Cantidad de mantenimientos asignados a retroexcavadoras
     * 
     * Devuelve la cantidad total de mantenimientos asociados a maquinaria tipo “retroexcavadora”.
     * 
     * @response 200 {
     *   "status": true,
     *   "data": {
     *     "cantidad_mantenimientos": 12,
     *     "tipo_maquinaria": "Retroexcavadoras"
     *   }
     * }
     */
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

    /**
     * @group Consultas
     * 
     * Listado de solicitudes de octubre 2023
     * 
     * Devuelve todas las solicitudes registradas en octubre de 2023 con su empresa y maquinaria relacionada.
     * 
     * @response 200 {
     *   "status": true,
     *   "data": [
     *     {
     *       "empresa": "Constructora Sur",
     *       "maquinaria": "Excavadora",
     *       "codigo": "MNT008",
     *       "mantenimiento": "Cambio de filtros",
     *       "cantidad_maquinas": 2
     *     }
     *   ]
     * }
     */
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

        return response()->json(['status' => true, 'data' => $solicitudes]);
    }
}
