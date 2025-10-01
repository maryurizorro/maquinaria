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

/**
 * @OA\Tag(
 *     name="Consultas",
 *     description="Consultas personalizadas sobre empleados, empresas, maquinaria y solicitudes"
 * )
 */
class ConsultaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/consultas/empleados-ordenados",
     *     tags={"Consultas"},
     *     summary="Listar empleados ordenados por apellido",
     *     @OA\Response(response=200, description="Listado de empleados ordenado")
     * )
     */
    public function empleadosOrdenados()
    {
        $empleados = Empleado::select('nombre', 'apellido', 'documento', 'email', 'telefono')
            ->orderBy('apellido')
            ->get();

        return response()->json(['status' => true, 'data' => $empleados]);
    }

    /**
     * @OA\Get(
     *     path="/api/consultas/maquinaria-costosa",
     *     tags={"Consultas"},
     *     summary="Listar maquinaria pesada con mantenimientos superiores a 1 millón",
     *     @OA\Response(response=200, description="Maquinaria encontrada")
     * )
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
     * @OA\Get(
     *     path="/api/consultas/empresa-mas-solicitudes",
     *     tags={"Consultas"},
     *     summary="Obtener empresa con mayor número de solicitudes",
     *     @OA\Response(response=200, description="Empresa con más solicitudes")
     * )
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
     * @OA\Get(
     *     path="/api/consultas/maquinas-argos",
     *     tags={"Consultas"},
     *     summary="Total de máquinas con solicitud de mantenimiento de empresa Argos",
     *     @OA\Response(response=200, description="Cantidad de máquinas de Argos")
     * )
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
     * @OA\Get(
     *     path="/api/consultas/solicitudes-empleado",
     *     tags={"Consultas"},
     *     summary="Solicitudes que debe atender el empleado con documento 1057896547",
     *     @OA\Response(response=200, description="Solicitudes asignadas")
     * )
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
     * @OA\Get(
     *     path="/api/consultas/representantes-sin-solicitudes",
     *     tags={"Consultas"},
     *     summary="Representantes y empresas sin solicitudes",
     *     @OA\Response(response=200, description="Representantes encontrados")
     * )
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
     * @OA\Get(
     *     path="/api/consultas/listado-solicitudes",
     *     tags={"Consultas"},
     *     summary="Listado de solicitudes con empresa, código, máquina y valor total",
     *     @OA\Response(response=200, description="Listado obtenido")
     * )
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
     * @OA\Post(
     *     path="/api/consultas/solicitud-codigo",
     *     tags={"Consultas"},
     *     summary="Buscar solicitud por código y empleados asignados",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"codigo"},
     *             @OA\Property(property="codigo", type="string", example="SOL123")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Solicitud encontrada"),
     *     @OA\Response(response=404, description="Solicitud no encontrada")
     * )
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
     * @OA\Get(
     *     path="/api/consultas/mantenimientos-retroexcavadoras",
     *     tags={"Consultas"},
     *     summary="Cantidad de mantenimientos asignados a retroexcavadoras",
     *     @OA\Response(response=200, description="Cantidad obtenida")
     * )
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
     * @OA\Get(
     *     path="/api/consultas/solicitudes-octubre-2023",
     *     tags={"Consultas"},
     *     summary="Listado de solicitudes de octubre 2023",
     *     @OA\Response(response=200, description="Solicitudes encontradas")
     * )
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
