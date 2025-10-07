<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Empleado;

class EmpleadoController extends Controller
{
    /**
     * Listar empleados
     *
     * Devuelve la lista completa de empleados junto con sus solicitudes asociadas.
     *
     * @group Empleados
     * @response 200 {
     *  "status": true,
     *  "data": [
     *      {
     *          "id": 1,
     *          "nombre": "Juan",
     *          "apellido": "Pérez",
     *          "documento": "12345678",
     *          "email": "juan.perez@example.com",
     *          "direccion": "Calle 10 #23-45",
     *          "telefono": "3104567890",
     *          "rol": "empleado",
     *          "solicitudes": []
     *      }
     *  ]
     * }
     */
    public function index()
    {
        $empleados = Empleado::with('solicitudes')->get();

        return response()->json([
            'status' => true,
            'data' => $empleados
        ]);
    }

    /**
     * Crear un nuevo empleado
     *
     * Crea un nuevo registro de empleado con todos sus datos básicos.
     *
     * @group Empleados
     * @bodyParam nombre string required Nombre del empleado. Example: Juan
     * @bodyParam apellido string required Apellido del empleado. Example: Pérez
     * @bodyParam documento string required Documento único del empleado. Example: 12345678
     * @bodyParam email string required Correo electrónico único del empleado. Example: juan.perez@example.com
     * @bodyParam direccion string required Dirección del empleado. Example: Calle 10 #23-45
     * @bodyParam telefono string required Teléfono del empleado. Example: 3104567890
     * @bodyParam rol string Rol del empleado (admin, empleado, supervisor). Example: empleado
     * @response 201 {
     *  "status": true,
     *  "message": "Empleado creado exitosamente",
     *  "data": {
     *      "id": 5,
     *      "nombre": "Juan",
     *      "apellido": "Pérez",
     *      "documento": "12345678",
     *      "email": "juan.perez@example.com",
     *      "direccion": "Calle 10 #23-45",
     *      "telefono": "3104567890",
     *      "rol": "empleado"
     *  }
     * }
     * @response 422 {
     *  "status": false,
     *  "message": "Error de validación",
     *  "errors": {
     *      "email": ["El campo email ya ha sido registrado."]
     *  }
     * }
     */
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

    /**
     * Mostrar un empleado
     *
     * Obtiene la información de un empleado por su ID junto con sus solicitudes.
     *
     * @group Empleados
     * @urlParam id integer required ID del empleado. Example: 2
     * @response 200 {
     *  "status": true,
     *  "data": {
     *      "id": 2,
     *      "nombre": "Ana",
     *      "apellido": "Gómez",
     *      "documento": "98765432",
     *      "email": "ana.gomez@example.com",
     *      "direccion": "Carrera 15 #45-12",
     *      "telefono": "3204567890",
     *      "rol": "supervisor",
     *      "solicitudes": []
     *  }
     * }
     * @response 404 {
     *  "status": false,
     *  "message": "Empleado no encontrado"
     * }
     */
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

    /**
     * Actualizar un empleado
     *
     * Modifica la información de un empleado existente.
     *
     * @group Empleados
     * @urlParam id integer required ID del empleado. Example: 3
     * @bodyParam nombre string Nombre del empleado. Example: Carlos
     * @bodyParam apellido string Apellido del empleado. Example: Ramírez
     * @bodyParam documento string Documento del empleado. Example: 11223344
     * @bodyParam email string Correo electrónico. Example: carlos.ramirez@example.com
     * @bodyParam direccion string Dirección. Example: Calle 45 #23-10
     * @bodyParam telefono string Teléfono. Example: 3115678901
     * @bodyParam rol string Rol (admin, empleado, supervisor). Example: supervisor
     * @response 200 {
     *  "status": true,
     *  "message": "Empleado actualizado exitosamente",
     *  "data": {
     *      "id": 3,
     *      "nombre": "Carlos",
     *      "apellido": "Ramírez",
     *      "email": "carlos.ramirez@example.com"
     *  }
     * }
     * @response 404 {
     *  "status": false,
     *  "message": "Empleado no encontrado"
     * }
     */
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

    /**
     * Eliminar un empleado
     *
     * Elimina un registro de empleado del sistema.
     *
     * @group Empleados
     * @urlParam id integer required ID del empleado. Example: 4
     * @response 200 {
     *  "status": true,
     *  "message": "Empleado eliminado exitosamente"
     * }
     * @response 404 {
     *  "status": false,
     *  "message": "Empleado no encontrado"
     * }
     */
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
