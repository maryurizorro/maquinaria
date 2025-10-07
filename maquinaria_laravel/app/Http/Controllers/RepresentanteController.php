<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Representante;

class RepresentanteController extends Controller
{
    /**
     * @group Representantes
     * 
     * Listar todos los representantes
     * 
     * Devuelve una lista con todos los representantes y la información de su empresa asociada.
     * 
     * @response 200 {
     *  "status": true,
     *  "data": [
     *      {
     *          "id": 1,
     *          "nombre": "Juan",
     *          "apellido": "Pérez",
     *          "documento": "12345678",
     *          "telefono": "3001234567",
     *          "email": "juan@example.com",
     *          "empresa_id": 1,
     *          "empresa": {
     *              "id": 1,
     *              "nombre": "Empresa ABC"
     *          }
     *      }
     *  ]
     * }
     */
    public function index()
    {
        $representantes = Representante::with('empresa')->get();
        
        return response()->json([
            'status' => true,
            'data' => $representantes
        ]);
    }

    /**
     * @group Representantes
     * 
     * Crear un nuevo representante
     * 
     * Registra un nuevo representante y lo asocia a una empresa existente.
     * 
     * @bodyParam nombre string required Nombre del representante. Example: Juan
     * @bodyParam apellido string required Apellido del representante. Example: Pérez
     * @bodyParam documento string required Documento único del representante. Example: 12345678
     * @bodyParam telefono string required Teléfono de contacto. Example: 3001234567
     * @bodyParam email string required Correo electrónico único. Example: juan@example.com
     * @bodyParam empresa_id integer required ID de la empresa asociada. Example: 1
     * 
     * @response 201 {
     *  "status": true,
     *  "message": "Representante creado exitosamente",
     *  "data": {
     *      "id": 1,
     *      "nombre": "Juan",
     *      "apellido": "Pérez",
     *      "documento": "12345678",
     *      "telefono": "3001234567",
     *      "email": "juan@example.com",
     *      "empresa": {
     *          "id": 1,
     *          "nombre": "Empresa ABC"
     *      }
     *  }
     * }
     * 
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

    /**
     * @group Representantes
     * 
     * Mostrar un representante por ID
     * 
     * Retorna los detalles de un representante y su empresa asociada.
     * 
     * @urlParam id integer required ID del representante. Example: 1
     * 
     * @response 200 {
     *  "status": true,
     *  "data": {
     *      "id": 1,
     *      "nombre": "Juan",
     *      "apellido": "Pérez",
     *      "documento": "12345678",
     *      "telefono": "3001234567",
     *      "email": "juan@example.com",
     *      "empresa": {
     *          "id": 1,
     *          "nombre": "Empresa ABC"
     *      }
     *  }
     * }
     * 
     * @response 404 {
     *  "status": false,
     *  "message": "Representante no encontrado"
     * }
     */
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

    /**
     * @group Representantes
     * 
     * Actualizar un representante existente
     * 
     * Permite modificar los datos de un representante existente.
     * 
     * @urlParam id integer required ID del representante a actualizar. Example: 1
     * @bodyParam nombre string Nombre del representante. Example: Carlos
     * @bodyParam apellido string Apellido del representante. Example: Ramírez
     * @bodyParam documento string Documento del representante. Example: 98765432
     * @bodyParam telefono string Teléfono de contacto. Example: 3209876543
     * @bodyParam email string Correo electrónico. Example: carlos@example.com
     * @bodyParam empresa_id integer ID de la empresa asociada. Example: 2
     * 
     * @response 200 {
     *  "status": true,
     *  "message": "Representante actualizado exitosamente",
     *  "data": {
     *      "id": 1,
     *      "nombre": "Carlos",
     *      "apellido": "Ramírez",
     *      "email": "carlos@example.com",
     *      "empresa": {
     *          "id": 2,
     *          "nombre": "Empresa XYZ"
     *      }
     *  }
     * }
     * 
     * @response 404 {
     *  "status": false,
     *  "message": "Representante no encontrado"
     * }
     */
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

    /**
     * @group Representantes
     * 
     * Eliminar un representante
     * 
     * Elimina un representante existente según su ID.
     * 
     * @urlParam id integer required ID del representante a eliminar. Example: 1
     * 
     * @response 200 {
     *  "status": true,
     *  "message": "Representante eliminado exitosamente"
     * }
     * 
     * @response 404 {
     *  "status": false,
     *  "message": "Representante no encontrado"
     * }
     */
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
