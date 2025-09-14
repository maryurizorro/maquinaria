<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Mantenimiento;

class MantenimientoController extends Controller
{
    public function index()
    {
        $mantenimientos = Mantenimiento::with('tipoMaquinaria')->get();
        
        return response()->json([
            'status' => true,
            'data' => $mantenimientos
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|unique:mantenimientos|max:50',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'costo' => 'required|numeric|min:0',
            'tiempo_estimado' => 'nullable|numeric|min:0',
            'manual_procedimiento' => 'nullable|string|max:1000',
            'tipo_maquinaria_id' => 'required|exists:tipo_maquinarias,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $mantenimiento = Mantenimiento::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Mantenimiento creado exitosamente',
            'data' => $mantenimiento->load('tipoMaquinaria')
        ], 201);
    }

    public function show($id)
    {
        $mantenimiento = Mantenimiento::with('tipoMaquinaria')->find($id);

        if (!$mantenimiento) {
            return response()->json([
                'status' => false,
                'message' => 'Mantenimiento no encontrado'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $mantenimiento
        ]);
    }

    public function update(Request $request, $id)
    {
        $mantenimiento = Mantenimiento::find($id);

        if (!$mantenimiento) {
            return response()->json([
                'status' => false,
                'message' => 'Mantenimiento no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'codigo' => 'sometimes|required|string|max:50|unique:mantenimientos,codigo,' . $id,
            'nombre' => 'sometimes|required|string|max:255',
            'descripcion' => 'sometimes|required|string',
            'costo' => 'sometimes|required|numeric|min:0',
            'tipo_maquinaria_id' => 'sometimes|required|exists:tipo_maquinarias,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $mantenimiento->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Mantenimiento actualizado exitosamente',
            'data' => $mantenimiento->load('tipoMaquinaria')
        ]);
    }

    public function destroy($id)
    {
        $mantenimiento = Mantenimiento::find($id);

        if (!$mantenimiento) {
            return response()->json([
                'status' => false,
                'message' => 'Mantenimiento no encontrado'
            ], 404);
        }

        $mantenimiento->delete();

        return response()->json([
            'status' => true,
            'message' => 'Mantenimiento eliminado exitosamente'
        ]);
    }
}
