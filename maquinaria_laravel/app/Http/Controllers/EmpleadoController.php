<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Empleado;

class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados = Empleado::with('solicitudes')->get();
        
        return response()->json([
            'status' => true,
            'data' => $empleados
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'documento' => 'required|string|unique:empleados|max:20',
            'email' => 'required|email|unique:empleados|max:255',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'rol' => 'sometimes|in:admin,empleado,supervisor',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $empleado = Empleado::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Empleado creado exitosamente',
            'data' => $empleado
        ], 201);
    }

    public function show($id)
    {
        $empleado = Empleado::with('solicitudes')->find($id);

        if (!$empleado) {
            return response()->json([
                'status' => false,
                'message' => 'Empleado no encontrado'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $empleado
        ]);
    }

    public function update(Request $request, $id)
    {
        $empleado = Empleado::find($id);

        if (!$empleado) {
            return response()->json([
                'status' => false,
                'message' => 'Empleado no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'apellido' => 'sometimes|required|string|max:255',
            'documento' => 'sometimes|required|string|max:20|unique:empleados,documento,' . $id,
            'email' => 'sometimes|required|email|max:255|unique:empleados,email,' . $id,
            'direccion' => 'sometimes|required|string|max:255',
            'telefono' => 'sometimes|required|string|max:20',
            'rol' => 'sometimes|in:admin,empleado,supervisor',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $empleado->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Empleado actualizado exitosamente',
            'data' => $empleado
        ]);
    }

    public function destroy($id)
    {
        $empleado = Empleado::find($id);

        if (!$empleado) {
            return response()->json([
                'status' => false,
                'message' => 'Empleado no encontrado'
            ], 404);
        }

        $empleado->delete();

        return response()->json([
            'status' => true,
            'message' => 'Empleado eliminado exitosamente'
        ]);
    }
}
