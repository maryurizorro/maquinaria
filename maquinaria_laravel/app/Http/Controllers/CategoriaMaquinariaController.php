<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CategoriaMaquinaria;

class CategoriaMaquinariaController extends Controller
{
    public function index()
    {
        $categorias = CategoriaMaquinaria::with('tiposMaquinaria')->get();
        
        return response()->json([
            'status' => true,
            'data' => $categorias
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $categoria = CategoriaMaquinaria::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Categoría creada exitosamente',
            'data' => $categoria
        ], 201);
    }

    public function show($id)
    {
        $categoria = CategoriaMaquinaria::with('tiposMaquinaria')->find($id);

        if (!$categoria) {
            return response()->json([
                'status' => false,
                'message' => 'Categoría no encontrada'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $categoria
        ]);
    }

    public function update(Request $request, $id)
    {
        $categoria = CategoriaMaquinaria::find($id);

        if (!$categoria) {
            return response()->json([
                'status' => false,
                'message' => 'Categoría no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $categoria->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Categoría actualizada exitosamente',
            'data' => $categoria
        ]);
    }

    public function destroy($id)
    {
        $categoria = CategoriaMaquinaria::find($id);

        if (!$categoria) {
            return response()->json([
                'status' => false,
                'message' => 'Categoría no encontrada'
            ], 404);
        }

        $categoria->delete();

        return response()->json([
            'status' => true,
            'message' => 'Categoría eliminada exitosamente'
        ]);
    }
}
