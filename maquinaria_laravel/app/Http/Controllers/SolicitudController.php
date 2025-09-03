<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Solicitud;

class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = Solicitud::with('empresa', 'detallesSolicitud.mantenimiento', 'empleados')->get();
        
        return response()->json([
            'status' => true,
            'data' => $solicitudes
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|unique:solicituds|max:50',
            'fecha_solicitud' => 'required|date',
            'estado' => 'sometimes|in:pendiente,en_proceso,completada,cancelada',
            'observaciones' => 'nullable|string',
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
