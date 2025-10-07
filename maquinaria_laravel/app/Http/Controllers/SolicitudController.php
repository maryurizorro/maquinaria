<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Solicitud;

class SolicitudController extends Controller
{
    /**
     * @group Solicitudes
     * 
     * Listar todas las solicitudes
     * 
     * Muestra un listado completo de las solicitudes registradas con sus relaciones.
     * 
     * @response 200 {
     *   "status": true,
     *   "data": [
     *      {
     *        "id": 1,
     *        "codigo": "SOL-2025-001",
     *        "fecha_solicitud": "2025-09-30",
     *        "estado": "pendiente",
     *        "empresa": {
     *           "id": 1,
     *           "nombre": "Maquinarias S.A."
     *        },
     *        "detalles_solicitud": [
     *           {
     *             "id": 1,
     *             "mantenimiento": {
     *                "id": 2,
     *                "nombre": "Cambio de aceite"
     *             }
     *           }
     *        ],
     *        "empleados": []
     *      }
     *   ]
     * }
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
     * @group Solicitudes
     * 
     * Crear una nueva solicitud
     * 
     * Registra una nueva solicitud de mantenimiento en el sistema.
     * 
     * @bodyParam codigo string required Código único de la solicitud. Example: SOL-2025-001
     * @bodyParam fecha_solicitud date required Fecha en la que se realiza la solicitud. Example: 2025-09-30
     * @bodyParam estado string Estado actual de la solicitud (pendiente, en_proceso, completada, cancelada). Example: pendiente
     * @bodyParam observaciones string Observaciones adicionales. Example: Revisión de maquinaria pesada
     * @bodyParam descripcion_solicitud string Descripción detallada de la solicitud. Example: Solicitud de mantenimiento de excavadora
     * @bodyParam fecha_deseada date Fecha deseada para realizar el mantenimiento. Example: 2025-10-15
     * @bodyParam empresa_id integer required ID de la empresa que realiza la solicitud. Example: 1
     * 
     * @response 201 {
     *   "status": true,
     *   "message": "Solicitud creada exitosamente",
     *   "data": {
     *      "id": 1,
     *      "codigo": "SOL-2025-001",
     *      "empresa": {
     *         "id": 1,
     *         "nombre": "Maquinarias S.A."
     *      }
     *   }
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "Error de validación",
     *   "errors": {
     *      "codigo": ["El campo código es obligatorio."]
     *   }
     * }
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
     * @group Solicitudes
     * 
     * Mostrar una solicitud específica
     * 
     * Muestra los datos detallados de una solicitud, incluyendo su empresa, empleados y mantenimientos.
     * 
     * @urlParam id integer required ID de la solicitud. Example: 1
     * 
     * @response 200 {
     *   "status": true,
     *   "data": {
     *     "id": 1,
     *     "codigo": "SOL-2025-001",
     *     "empresa": {
     *       "id": 1,
     *       "nombre": "Maquinarias S.A."
     *     },
     *     "detallesSolicitud": [
     *       {
     *         "id": 1,
     *         "mantenimiento": {
     *           "id": 2,
     *           "nombre": "Cambio de aceite"
     *         }
     *       }
     *     ],
     *     "empleados": []
     *   }
     * }
     * @response 404 {
     *   "status": false,
     *   "message": "Solicitud no encontrada"
     * }
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
     * @group Solicitudes
     * 
     * Actualizar una solicitud
     * 
     * Permite modificar los datos de una solicitud existente.
     * 
     * @urlParam id integer required ID de la solicitud a actualizar. Example: 1
     * @bodyParam codigo string Código único de la solicitud. Example: SOL-2025-002
     * @bodyParam fecha_solicitud date Fecha en la que se realiza la solicitud. Example: 2025-09-28
     * @bodyParam estado string Estado de la solicitud (pendiente, en_proceso, completada, cancelada). Example: en_proceso
     * @bodyParam observaciones string Observaciones adicionales. Example: Se requiere repuesto urgente
     * @bodyParam descripcion_solicitud string Descripción detallada. Example: Solicitud de mantenimiento de cargador frontal
     * @bodyParam fecha_deseada date Fecha deseada para el mantenimiento. Example: 2025-10-20
     * @bodyParam empresa_id integer ID de la empresa. Example: 2
     * 
     * @response 200 {
     *   "status": true,
     *   "message": "Solicitud actualizada exitosamente",
     *   "data": {
     *     "id": 1,
     *     "codigo": "SOL-2025-002",
     *     "empresa": {
     *       "id": 2,
     *       "nombre": "Construcciones del Norte"
     *     }
     *   }
     * }
     * @response 404 {
     *   "status": false,
     *   "message": "Solicitud no encontrada"
     * }
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
     * @group Solicitudes
     * 
     * Eliminar una solicitud
     * 
     * Elimina una solicitud del sistema de manera permanente.
     * 
     * @urlParam id integer required ID de la solicitud a eliminar. Example: 1
     * 
     * @response 200 {
     *   "status": true,
     *   "message": "Solicitud eliminada exitosamente"
     * }
     * @response 404 {
     *   "status": false,
     *   "message": "Solicitud no encontrada"
     * }
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
