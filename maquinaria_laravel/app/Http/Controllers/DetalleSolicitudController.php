<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\DetalleSolicitud;
use App\Models\Mantenimiento;


class DetalleSolicitudController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/detalles-solicitud",
     *     summary="Listar todos los detalles de solicitud",
     *     tags={"DetalleSolicitud"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de detalles de solicitud",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/DetalleSolicitud")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $detalles = DetalleSolicitud::with('solicitud.empresa', 'mantenimiento')->get();
        
        return response()->json([
            'status' => true,
            'data' => $detalles
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/detalles-solicitud",
     *     summary="Crear un detalle de solicitud",
     *     tags={"DetalleSolicitud"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"solicitud_id","mantenimiento_id","cantidad_maquinas","Url_foto"},
     *             @OA\Property(property="solicitud_id", type="integer", example=1),
     *             @OA\Property(property="mantenimiento_id", type="integer", example=2),
     *             @OA\Property(property="cantidad_maquinas", type="integer", example=3),
     *             @OA\Property(property="Url_foto", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Detalle de solicitud creado",
     *         @OA\JsonContent(ref="#/components/schemas/DetalleSolicitud")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'solicitud_id' => 'required|exists:solicituds,id',
            'mantenimiento_id' => 'required|exists:mantenimientos,id',
            'cantidad_maquinas' => 'required|integer|min:1',
            'Url_foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }
        
        $subirImagenController = new SubirImagenController();
        $url_imagen = $subirImagenController->subirImagen($request);

        $mantenimiento = Mantenimiento::find($request->mantenimiento_id);
        $costo_total = $mantenimiento->costo * $request->cantidad_maquinas;

        $detalle = DetalleSolicitud::create([
            'solicitud_id' => $request->solicitud_id,
            'mantenimiento_id' => $request->mantenimiento_id,
            'cantidad_maquinas' => $request->cantidad_maquinas,
            'costo_total' => $costo_total,
            'Url_foto' => $url_imagen,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Detalle de solicitud creado exitosamente',
            'data' => $detalle->load('solicitud.empresa', 'mantenimiento')
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/detalles-solicitud/{id}",
     *     summary="Obtener un detalle de solicitud por ID",
     *     tags={"DetalleSolicitud"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID del detalle de solicitud"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalle encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/DetalleSolicitud")
     *     ),
     *     @OA\Response(response=404, description="Detalle no encontrado")
     * )
     */
    public function show($id)
    {
        $detalle = DetalleSolicitud::with('solicitud.empresa', 'mantenimiento')->find($id);

        if (!$detalle) {
            return response()->json([
                'status' => false,
                'message' => 'Detalle de solicitud no encontrado'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $detalle
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/detalles-solicitud/{id}",
     *     summary="Actualizar un detalle de solicitud",
     *     tags={"DetalleSolicitud"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="cantidad_maquinas", type="integer", example=4),
     *             @OA\Property(property="mantenimiento_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Detalle actualizado"),
     *     @OA\Response(response=404, description="Detalle no encontrado")
     * )
     */
    public function update(Request $request, $id)
    {
        $detalle = DetalleSolicitud::find($id);

        if (!$detalle) {
            return response()->json([
                'status' => false,
                'message' => 'Detalle de solicitud no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'solicitud_id' => 'sometimes|required|exists:solicituds,id',
            'mantenimiento_id' => 'sometimes|required|exists:mantenimientos,id',
            'cantidad_maquinas' => 'sometimes|required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('cantidad_maquinas') || $request->has('mantenimiento_id')) {
            $mantenimiento_id = $request->mantenimiento_id ?? $detalle->mantenimiento_id;
            $cantidad = $request->cantidad_maquinas ?? $detalle->cantidad_maquinas;
            
            $mantenimiento = Mantenimiento::find($mantenimiento_id);
            $costo_total = $mantenimiento->costo * $cantidad;
            
            $request->merge(['costo_total' => $costo_total]);
        }

        $detalle->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Detalle de solicitud actualizado exitosamente',
            'data' => $detalle->load('solicitud.empresa', 'mantenimiento')
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/detalles-solicitud/{id}",
     *     summary="Eliminar un detalle de solicitud",
     *     tags={"DetalleSolicitud"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Detalle eliminado"),
     *     @OA\Response(response=404, description="Detalle no encontrado")
     * )
     */
    public function destroy(string $id)
    {
        $solicitudDetalle = DetalleSolicitud::find($id);

        if (!$solicitudDetalle) {
            return response()->json(['error' => 'Registro no encontrado'], 404);
        }

        $subirImagenController = new SubirImagenController();
        $subirImagenController->EliminarImagen($solicitudDetalle->Url_foto);

        $solicitudDetalle->delete();

        return response()->json(['message' => 'Registro eliminado correctamente']);
    }
}
