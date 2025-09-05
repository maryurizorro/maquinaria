<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\DetalleSolicitud;
use App\Models\Mantenimiento;

class DetalleSolicitudController extends Controller
{
    public function index()
    {
        $detalles = DetalleSolicitud::with('solicitud.empresa', 'mantenimiento')->get();
        
        return response()->json([
            'status' => true,
            'data' => $detalles
        ]);
    }

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

        // Calcular el costo total
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

        // Recalcular el costo total si cambia la cantidad o el mantenimiento
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

    public function destroy(string $id)
    {
        $solicitudDetalle = DetalleSolicitud::find($id);

        if (!$solicitudDetalle) {
            return response()->json(['error' => 'Registro no encontrado'], 404);
        }

        // Eliminar imagen asociada
        $subirImagenController = new SubirImagenController();
        $subirImagenController->EliminarImagen($solicitudDetalle->url_imagen);

        // Eliminar registro
        $solicitudDetalle->delete();

        return response()->json(['message' => 'Registro eliminado correctamente']);
    }

}
