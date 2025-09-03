<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Empresa;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresas = Empresa::with('representantes')->get();
        
        return response()->json([
            'status' => true,
            'data' => $empresas
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'nit' => 'required|string|unique:empresas|max:20',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|unique:empresas|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $empresa = Empresa::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Empresa creada exitosamente',
            'data' => $empresa
        ], 201);
    }

    public function show($id)
    {
        $empresa = Empresa::with('representantes', 'solicitudes')->find($id);

        if (!$empresa) {
            return response()->json([
                'status' => false,
                'message' => 'Empresa no encontrada'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $empresa
        ]);
    }

    public function update(Request $request, $id)
    {
        $empresa = Empresa::find($id);

        if (!$empresa) {
            return response()->json([
                'status' => false,
                'message' => 'Empresa no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'nit' => 'sometimes|required|string|max:20|unique:empresas,nit,' . $id,
            'direccion' => 'sometimes|required|string|max:255',
            'telefono' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|required|email|max:255|unique:empresas,email,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $empresa->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Empresa actualizada exitosamente',
            'data' => $empresa
        ]);
    }

    public function destroy($id)
    {
        $empresa = Empresa::find($id);

        if (!$empresa) {
            return response()->json([
                'status' => false,
                'message' => 'Empresa no encontrada'
            ], 404);
        }

        $empresa->delete();

        return response()->json([
            'status' => true,
            'message' => 'Empresa eliminada exitosamente'
        ]);
    }
}
