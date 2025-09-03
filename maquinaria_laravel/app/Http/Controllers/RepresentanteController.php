<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Representante;

class RepresentanteController extends Controller
{
    public function index()
    {
        $representantes = Representante::with('empresa')->get();
        
        return response()->json([
            'status' => true,
            'data' => $representantes
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'documento' => 'required|string|unique:representantes|max:20',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|unique:representantes|max:255',
            'empresa_id' => 'required|exists:empresas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $representante = Representante::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Representante creado exitosamente',
            'data' => $representante->load('empresa')
        ], 201);
    }

    public function show($id)
    {
        $representante = Representante::with('empresa')->find($id);

        if (!$representante) {
            return response()->json([
                'status' => false,
                'message' => 'Representante no encontrado'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $representante
        ]);
    }

    public function update(Request $request, $id)
    {
        $representante = Representante::find($id);

        if (!$representante) {
            return response()->json([
                'status' => false,
                'message' => 'Representante no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'apellido' => 'sometimes|required|string|max:255',
            'documento' => 'sometimes|required|string|max:20|unique:representantes,documento,' . $id,
            'telefono' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|required|email|max:255|unique:representantes,email,' . $id,
            'empresa_id' => 'sometimes|required|exists:empresas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $representante->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Representante actualizado exitosamente',
            'data' => $representante->load('empresa')
        ]);
    }

    public function destroy($id)
    {
        $representante = Representante::find($id);

        if (!$representante) {
            return response()->json([
                'status' => false,
                'message' => 'Representante no encontrado'
            ], 404);
        }

        $representante->delete();

        return response()->json([
            'status' => true,
            'message' => 'Representante eliminado exitosamente'
        ]);
    }
}
