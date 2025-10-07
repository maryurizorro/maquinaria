<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Empresa;

class EmpresaController extends Controller
{
    /**
     * @group Empresas
     * 
     * Listar todas las empresas
     * 
     * Retorna una lista completa de empresas junto con sus representantes.
     * 
     * @response 200 {
     *   "status": true,
     *   "data": [
     *      {
     *         "id": 1,
     *         "nombre": "Constructora XYZ",
     *         "nit": "900123456-7",
     *         "direccion": "Calle 123 #45-67",
     *         "telefono": "3001234567",
     *         "email": "contacto@xyz.com",
     *         "created_at": "2025-10-06T15:00:00Z",
     *         "updated_at": "2025-10-06T15:00:00Z"
     *      }
     *   ]
     * }
     */
    public function index()
    {
        $empresas = Empresa::with('representantes')->get();
        
        return response()->json([
            'status' => true,
            'data' => $empresas
        ]);
    }

    /**
     * @group Empresas
     * 
     * Crear una nueva empresa
     * 
     * Crea una empresa con la información proporcionada.
     * 
     * @bodyParam nombre string required Nombre de la empresa. Example: Constructora XYZ
     * @bodyParam nit string required Número de identificación tributaria único. Example: 900123456-7
     * @bodyParam direccion string required Dirección principal. Example: Calle 123 #45-67
     * @bodyParam telefono string required Teléfono de contacto. Example: 3001234567
     * @bodyParam email string required Correo electrónico. Example: contacto@xyz.com
     * 
     * @response 201 {
     *   "status": true,
     *   "message": "Empresa creada exitosamente",
     *   "data": {
     *       "id": 1,
     *       "nombre": "Constructora XYZ",
     *       "nit": "900123456-7",
     *       "direccion": "Calle 123 #45-67",
     *       "telefono": "3001234567",
     *       "email": "contacto@xyz.com"
     *   }
     * }
     * 
     * @response 422 {
     *   "status": false,
     *   "message": "Error de validación",
     *   "errors": {
     *       "email": ["El campo email ya está en uso."]
     *   }
     * }
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'nit' => 'required|string|unique:empresas|max:20',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|unique:empresas|max:255',
            'ciudad' => 'required|string|max:100',
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

    /**
     * @group Empresas
     * 
     * Obtener una empresa por ID
     * 
     * Devuelve los datos de una empresa específica, junto con sus representantes y solicitudes.
     * 
     * @urlParam id integer required ID de la empresa. Example: 1
     * 
     * @response 200 {
     *   "status": true,
     *   "data": {
     *       "id": 1,
     *       "nombre": "Constructora XYZ",
     *       "nit": "900123456-7",
     *       "direccion": "Calle 123 #45-67",
     *       "telefono": "3001234567",
     *       "email": "contacto@xyz.com"
     *   }
     * }
     * 
     * @response 404 {
     *   "status": false,
     *   "message": "Empresa no encontrada"
     * }
     */
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

    /**
     * @group Empresas
     * 
     * Actualizar una empresa existente
     * 
     * Permite actualizar los datos de una empresa ya registrada.
     * 
     * @urlParam id integer required ID de la empresa. Example: 1
     * @bodyParam nombre string Nombre de la empresa. Example: Constructora ABC
     * @bodyParam nit string Número tributario. Example: 900987654-3
     * @bodyParam direccion string Dirección. Example: Carrera 10 #20-30
     * @bodyParam telefono string Teléfono. Example: 3016549870
     * @bodyParam email string Correo electrónico. Example: info@abc.com
     * 
     * @response 200 {
     *   "status": true,
     *   "message": "Empresa actualizada exitosamente",
     *   "data": {
     *       "id": 1,
     *       "nombre": "Constructora ABC",
     *       "nit": "900987654-3"
     *   }
     * }
     * 
     * @response 404 {
     *   "status": false,
     *   "message": "Empresa no encontrada"
     * }
     */
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

    /**
     * @group Empresas
     * 
     * Eliminar una empresa
     * 
     * Elimina una empresa registrada del sistema.
     * 
     * @urlParam id integer required ID de la empresa. Example: 1
     * 
     * @response 200 {
     *   "status": true,
     *   "message": "Empresa eliminada exitosamente"
     * }
     * 
     * @response 404 {
     *   "status": false,
     *   "message": "Empresa no encontrada"
     * }
     */
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
