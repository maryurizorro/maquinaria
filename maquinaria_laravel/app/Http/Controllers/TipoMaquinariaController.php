<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TipoMaquinaria;

class TipoMaquinariaController extends Controller
{
    public function index()
    {
        $tipos = TipoMaquinaria::with('categoria', 'mantenimientos')->get();
        
        return response()->json([
            'status' => true,
            'data' => $tipos
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categoria_maquinarias,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $tipo = TipoMaquinaria::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Tipo de maquinaria creado exitosamente',
            'data' => $tipo->load('categoria')
        ], 201);
    }

    public function show($id)
    {
        $tipo = TipoMaquinaria::with('categoria', 'mantenimientos')->find($id);

        if (!$tipo) {
            return response()->json([
                'status' => false,
                'message' => 'Tipo de maquinaria no encontrado'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $tipo
        ]);
    }

    public function update(Request $request, $id)
    {
        $tipo = TipoMaquinaria::find($id);

        if (!$tipo) {
            return response()->json([
                'status' => false,
                'message' => 'Tipo de maquinaria no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'sometimes|required|exists:categoria_maquinarias,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $tipo->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Tipo de maquinaria actualizado exitosamente',
            'data' => $tipo->load('categoria')
        ]);
    }

    public function destroy($id)
    {
        $tipo = TipoMaquinaria::find($id);

        if (!$tipo) {
            return response()->json([
                'status' => false,
                'message' => 'Tipo de maquinaria no encontrado'
            ], 404);
        }

        $tipo->delete();

        return response()->json([
            'status' => true,
            'message' => 'Tipo de maquinaria eliminado exitosamente'
        ]);
    }
}
