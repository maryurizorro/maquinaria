<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\SolicitudEmpleado;

class SolicitudEmpleadoController extends Controller
{
        /**
     * @OA\Get(
     *     path="/api/solicitud-empleados",
     *     summary="Listar todas las asignaciones de empleados a solicitudes",
     *     tags={"SolicitudEmpleado"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de asignaciones",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/SolicitudEmpleado")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $solicitudEmpleados = SolicitudEmpleado::with('solicitud.empresa', 'empleado')->get();
        
        return response()->json([
            'status' => true,
            'data' => $solicitudEmpleados
        ]);
    }
    /**
     * @OA\Post(
     *     path="/api/solicitud-empleados",
     *     summary="Asignar un empleado a una solicitud",
     *     tags={"SolicitudEmpleado"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"solicitud_id","empleado_id"},
     *             @OA\Property(property="solicitud_id", type="integer", example=1),
     *             @OA\Property(property="empleado_id", type="integer", example=2),
     *             @OA\Property(property="estado", type="string", enum={"asignado","en_proceso","completado"}, example="asignado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Empleado asignado correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/SolicitudEmpleado")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'solicitud_id' => 'required|exists:solicituds,id',
            'empleado_id' => 'required|exists:empleados,id',
            'estado' => 'sometimes|in:asignado,en_proceso,completado',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar que no exista ya la asignación
        $existing = SolicitudEmpleado::where('solicitud_id', $request->solicitud_id)
                                    ->where('empleado_id', $request->empleado_id)
                                    ->first();

        if ($existing) {
            return response()->json([
                'status' => false,
                'message' => 'El empleado ya está asignado a esta solicitud'
            ], 422);
        }

        $solicitudEmpleado = SolicitudEmpleado::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Empleado asignado a solicitud exitosamente',
            'data' => $solicitudEmpleado->load('solicitud.empresa', 'empleado')
        ], 201);
    }
    /**
     * @OA\Get(
     *     path="/api/solicitud-empleados/{id}",
     *     summary="Obtener una asignación por ID",
     *     tags={"SolicitudEmpleado"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la asignación",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Asignación encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/SolicitudEmpleado")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Asignación no encontrada"
     *     )
     * )
     */
    public function show($id)
    {
        $solicitudEmpleado = SolicitudEmpleado::with('solicitud.empresa', 'empleado')->find($id);

        if (!$solicitudEmpleado) {
            return response()->json([
                'status' => false,
                'message' => 'Asignación no encontrada'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $solicitudEmpleado
        ]);
    }
  /**
     * @OA\Put(
     *     path="/api/solicitud-empleados/{id}",
     *     summary="Actualizar una asignación",
     *     tags={"SolicitudEmpleado"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la asignación",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="estado", type="string", enum={"asignado","en_proceso","completado"}, example="en_proceso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Asignación actualizada correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/SolicitudEmpleado")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Asignación no encontrada"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $solicitudEmpleado = SolicitudEmpleado::find($id);

        if (!$solicitudEmpleado) {
            return response()->json([
                'status' => false,
                'message' => 'Asignación no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'solicitud_id' => 'sometimes|required|exists:solicituds,id',
            'empleado_id' => 'sometimes|required|exists:empleados,id',
            'estado' => 'sometimes|in:asignado,en_proceso,completado',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $solicitudEmpleado->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Asignación actualizada exitosamente',
            'data' => $solicitudEmpleado->load('solicitud.empresa', 'empleado')
        ]);
    }
 /**
     * @OA\Delete(
     *     path="/api/solicitud-empleados/{id}",
     *     summary="Eliminar una asignación",
     *     tags={"SolicitudEmpleado"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la asignación",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Asignación eliminada correctamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Asignación no encontrada"
     *     )
     * )
     */
    public function destroy($id)
    {
        $solicitudEmpleado = SolicitudEmpleado::find($id);

        if (!$solicitudEmpleado) {
            return response()->json([
                'status' => false,
                'message' => 'Asignación no encontrada'
            ], 404);
        }

        $solicitudEmpleado->delete();

        return response()->json([
            'status' => true,
            'message' => 'Asignación eliminada exitosamente'
        ]);
    }
}
