<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\DetalleSolicitud;
use App\Models\Mantenimiento;

class DetalleSolicitudController extends Controller
{
    /**
     * Listar todos los detalles de solicitud
     *
     * Obtiene una lista completa de los detalles de solicitud junto con la información
     * relacionada de la solicitud, empresa y mantenimiento.
     *
     * @group DetalleSolicitud
     * @response 200 {
     *  "status": true,
     *  "data": [
     *      {
     *          "id": 1,
     *          "solicitud_id": 1,
     *          "mantenimiento_id": 2,
     *          "cantidad_maquinas": 3,
     *          "costo_total": 45000,
     *          "Url_foto": "uploads/img1.jpg",
     *          "solicitud": { "empresa": { "nombre": "Mi Empresa" } },
     *          "mantenimiento": { "nombre": "Revisión General" }
     *      }
     *  ]
     * }
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
     * Crear un nuevo detalle de solicitud
     *
     * Registra un nuevo detalle de solicitud con su mantenimiento, cantidad de máquinas
     * y foto asociada.
     *
     * @group DetalleSolicitud
     * @bodyParam solicitud_id integer required ID de la solicitud. Example: 1
     * @bodyParam mantenimiento_id integer required ID del mantenimiento. Example: 2
     * @bodyParam cantidad_maquinas integer required Cantidad de máquinas. Example: 3
     * @bodyParam Url_foto file required Imagen de evidencia (jpeg, png, jpg, gif). Example: foto.jpg
     * @response 201 {
     *  "status": true,
     *  "message": "Detalle de solicitud creado exitosamente",
     *  "data": {
     *      "id": 5,
     *      "solicitud_id": 1,
     *      "mantenimiento_id": 2,
     *      "cantidad_maquinas": 3,
     *      "costo_total": 45000,
     *      "Url_foto": "uploads/img5.jpg"
     *  }
     * }
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
     * Mostrar un detalle de solicitud
     *
     * Obtiene la información de un detalle de solicitud específico por su ID.
     *
     * @group DetalleSolicitud
     * @urlParam id integer required ID del detalle de solicitud. Example: 3
     * @response 200 {
     *  "status": true,
     *  "data": {
     *      "id": 3,
     *      "solicitud_id": 1,
     *      "mantenimiento_id": 2,
     *      "cantidad_maquinas": 2,
     *      "costo_total": 30000,
     *      "Url_foto": "uploads/img3.jpg"
     *  }
     * }
     * @response 404 {
     *  "status": false,
     *  "message": "Detalle de solicitud no encontrado"
     * }
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
     * Actualizar un detalle de solicitud
     *
     * Permite modificar los datos de un detalle de solicitud existente.
     *
     * @group DetalleSolicitud
     * @urlParam id integer required ID del detalle a actualizar. Example: 3
     * @bodyParam cantidad_maquinas integer Cantidad de máquinas. Example: 4
     * @bodyParam mantenimiento_id integer ID del mantenimiento. Example: 2
     * @response 200 {
     *  "status": true,
     *  "message": "Detalle de solicitud actualizado exitosamente",
     *  "data": {
     *      "id": 3,
     *      "cantidad_maquinas": 4,
     *      "costo_total": 60000
     *  }
     * }
     * @response 404 {
     *  "status": false,
     *  "message": "Detalle de solicitud no encontrado"
     * }
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
     * Eliminar un detalle de solicitud
     *
     * Elimina un detalle de solicitud y su imagen asociada.
     *
     * @group DetalleSolicitud
     * @urlParam id integer required ID del detalle a eliminar. Example: 5
     * @response 200 {
     *  "message": "Registro eliminado correctamente"
     * }
     * @response 404 {
     *  "error": "Registro no encontrado"
     * }
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
