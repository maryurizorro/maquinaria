<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\SolicitudEmpleado;

class SolicitudEmpleadoController extends Controller
{
    public function index()
    {
        $solicitudEmpleados = SolicitudEmpleado::with('solicitud.empresa', 'empleado')->get();
        
        return response()->json([
            'status' => true,
            'data' => $solicitudEmpleados
        ]);
    }

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
