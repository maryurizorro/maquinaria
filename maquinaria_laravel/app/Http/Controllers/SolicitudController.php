<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Solicitud;

class SolicitudController extends Controller
{
      /**
     * @OA\Get(
     *     path="/api/solicitudes",
     *     summary="Listar todas las solicitudes",
     *     tags={"Solicitudes"},
     *     @OA\Response(response=200, description="Lista de solicitudes")
     * )
     */
    public function index()
    {
        $solicitudes = Solicitud::with('empresa', 'detallesSolicitud.mantenimiento', 'empleados')->get();
        
        return response()->json([
            'status' => true,
            'data' => $solicitudes
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/solicitudes",
     *     summary="Crear una nueva solicitud",
     *     tags={"Solicitudes"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"codigo","fecha_solicitud","empresa_id"},
     *             @OA\Property(property="codigo", type="string", example="SOL-2025-001"),
     *             @OA\Property(property="fecha_solicitud", type="string", format="date", example="2025-09-30"),
     *             @OA\Property(property="estado", type="string", example="pendiente"),
     *             @OA\Property(property="observaciones", type="string", example="Revisión de maquinaria pesada"),
     *             @OA\Property(property="descripcion_solicitud", type="string", example="Solicitud de mantenimiento de excavadora"),
     *             @OA\Property(property="fecha_deseada", type="string", format="date", example="2025-10-15"),
     *             @OA\Property(property="empresa_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Solicitud creada exitosamente"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|unique:solicituds|max:50',
            'fecha_solicitud' => 'required|date',
            'estado' => 'sometimes|in:pendiente,en_proceso,completada,cancelada',
            'observaciones' => 'nullable|string',
            'fecha_deseada' => 'nullable|date',
            'descripcion_solicitud' => 'nullable|string|max:500',
            'empresa_id' => 'required|exists:empresas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $solicitud = Solicitud::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Solicitud creada exitosamente',
            'data' => $solicitud->load('empresa')
        ], 201);
    }
    /**
     * @OA\Get(
     *     path="/api/solicitudes/{id}",
     *     summary="Obtener una solicitud",
     *     tags={"Solicitudes"},
     *     @OA\Parameter(
     *         name="id",
     *         description="ID de la solicitud",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Solicitud encontrada"),
     *     @OA\Response(response=404, description="Solicitud no encontrada")
     * )
     */
    public function show($id)
    {
        $solicitud = Solicitud::with('empresa', 'detallesSolicitud.mantenimiento', 'empleados')->find($id);

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
    /**
     * @OA\Put(
     *     path="/api/solicitudes/{id}",
     *     summary="Actualizar una solicitud",
     *     tags={"Solicitudes"},
     *     @OA\Parameter(
     *         name="id",
     *         description="ID de la solicitud",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="codigo", type="string", example="SOL-2025-002"),
     *             @OA\Property(property="fecha_solicitud", type="string", format="date", example="2025-09-28"),
     *             @OA\Property(property="estado", type="string", example="en_proceso"),
     *             @OA\Property(property="observaciones", type="string", example="Se requiere repuesto urgente"),
     *             @OA\Property(property="descripcion_solicitud", type="string", example="Solicitud de mantenimiento de cargador frontal"),
     *             @OA\Property(property="fecha_deseada", type="string", format="date", example="2025-10-20"),
     *             @OA\Property(property="empresa_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Solicitud actualizada exitosamente"),
     *     @OA\Response(response=404, description="Solicitud no encontrada")
     * )
     */
    public function update(Request $request, $id)
    {
        $solicitud = Solicitud::find($id);

        if (!$solicitud) {
            return response()->json([
                'status' => false,
                'message' => 'Solicitud no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'codigo' => 'sometimes|required|string|max:50|unique:solicituds,codigo,' . $id,
            'fecha_solicitud' => 'sometimes|required|date',
            'estado' => 'sometimes|in:pendiente,en_proceso,completada,cancelada',
            'observaciones' => 'nullable|string',
            'descripcion_solicitud' => 'nullable|string',
            'fecha_deseada' => 'nullable|date',
            'empresa_id' => 'sometimes|required|exists:empresas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $solicitud->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Solicitud actualizada exitosamente',
            'data' => $solicitud->load('empresa')
        ]);
    }
    /**
     * @OA\Delete(
     *     path="/api/solicitudes/{id}",
     *     summary="Eliminar una solicitud",
     *     tags={"Solicitudes"},
     *     @OA\Parameter(
     *         name="id",
     *         description="ID de la solicitud",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Solicitud eliminada exitosamente"),
     *     @OA\Response(response=404, description="Solicitud no encontrada")
     * )
     */
    public function destroy($id)
    {
        $solicitud = Solicitud::find($id);

        if (!$solicitud) {
            return response()->json([
                'status' => false,
                'message' => 'Solicitud no encontrada'
            ], 404);
        }

        $solicitud->delete();

        return response()->json([
            'status' => true,
            'message' => 'Solicitud eliminada exitosamente'
        ]);
    }
}
