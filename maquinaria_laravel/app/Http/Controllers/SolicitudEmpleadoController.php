<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\SolicitudEmpleado;

class SolicitudEmpleadoController extends Controller
{
    /**
     * @group SolicitudEmpleado
     * 
     * Listar todas las asignaciones de empleados a solicitudes
     * 
     * Muestra todas las asignaciones de empleados con sus respectivas solicitudes y empresas.
     * 
     * @response 200 {
     *   "status": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "solicitud": {
     *         "id": 3,
     *         "codigo": "SOL-2025-002",
     *         "empresa": {
     *           "id": 1,
     *           "nombre": "Maquinarias S.A."
     *         }
     *       },
     *       "empleado": {
     *         "id": 5,
     *         "nombre": "Carlos Gómez"
     *       },
     *       "estado": "asignado"
     *     }
     *   ]
     * }
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
     * @group SolicitudEmpleado
     * 
     * Asignar un empleado a una solicitud
     * 
     * Crea una nueva asignación entre un empleado y una solicitud.
     * 
     * @bodyParam solicitud_id integer required ID de la solicitud. Example: 1
     * @bodyParam empleado_id integer required ID del empleado. Example: 2
     * @bodyParam estado string Estado de la asignación (asignado, en_proceso, completado). Example: asignado
     * 
     * @response 201 {
     *   "status": true,
     *   "message": "Empleado asignado a solicitud exitosamente",
     *   "data": {
     *      "id": 1,
     *      "solicitud": {
     *         "id": 3,
     *         "codigo": "SOL-2025-002",
     *         "empresa": {
     *            "id": 1,
     *            "nombre": "Maquinarias S.A."
     *         }
     *      },
     *      "empleado": {
     *         "id": 2,
     *         "nombre": "Carlos Gómez"
     *      },
     *      "estado": "asignado"
     *   }
     * }
     * 
     * @response 422 {
     *   "status": false,
     *   "message": "Error de validación",
     *   "errors": {
     *     "empleado_id": ["El campo empleado_id es obligatorio."]
     *   }
     * }
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
     * @group SolicitudEmpleado
     * 
     * Obtener una asignación por ID
     * 
     * Muestra la información de una asignación específica de empleado a solicitud.
     * 
     * @urlParam id integer required ID de la asignación. Example: 1
     * 
     * @response 200 {
     *   "status": true,
     *   "data": {
     *      "id": 1,
     *      "solicitud": {
     *         "id": 3,
     *         "codigo": "SOL-2025-002",
     *         "empresa": {
     *            "id": 1,
     *            "nombre": "Maquinarias S.A."
     *         }
     *      },
     *      "empleado": {
     *         "id": 2,
     *         "nombre": "Carlos Gómez"
     *      },
     *      "estado": "en_proceso"
     *   }
     * }
     * 
     * @response 404 {
     *   "status": false,
     *   "message": "Asignación no encontrada"
     * }
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
     * @group SolicitudEmpleado
     * 
     * Actualizar una asignación
     * 
     * Modifica los datos de una asignación existente (por ejemplo, cambiar el estado).
     * 
     * @urlParam id integer required ID de la asignación. Example: 1
     * @bodyParam solicitud_id integer ID de la solicitud. Example: 1
     * @bodyParam empleado_id integer ID del empleado. Example: 2
     * @bodyParam estado string Estado de la asignación (asignado, en_proceso, completado). Example: completado
     * 
     * @response 200 {
     *   "status": true,
     *   "message": "Asignación actualizada exitosamente",
     *   "data": {
     *      "id": 1,
     *      "estado": "completado",
     *      "solicitud": {
     *         "id": 3,
     *         "codigo": "SOL-2025-002"
     *      },
     *      "empleado": {
     *         "id": 2,
     *         "nombre": "Carlos Gómez"
     *      }
     *   }
     * }
     * 
     * @response 404 {
     *   "status": false,
     *   "message": "Asignación no encontrada"
     * }
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
     * @group SolicitudEmpleado
     * 
     * Eliminar una asignación
     * 
     * Elimina una asignación de empleado a solicitud.
     * 
     * @urlParam id integer required ID de la asignación a eliminar. Example: 1
     * 
     * @response 200 {
     *   "status": true,
     *   "message": "Asignación eliminada exitosamente"
     * }
     * 
     * @response 404 {
     *   "status": false,
     *   "message": "Asignación no encontrada"
     * }
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
