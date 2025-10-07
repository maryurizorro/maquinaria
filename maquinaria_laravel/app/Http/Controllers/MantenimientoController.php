<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Mantenimiento;

class MantenimientoController extends Controller
{
    /**
     * @group Mantenimientos
     * 
     * Listar todos los mantenimientos
     * 
     * Este endpoint retorna todos los mantenimientos registrados junto con la información del tipo de maquinaria asociado.
     * 
     * @response 200 {
     *  "status": true,
     *  "data": [
     *      {
     *          "id": 1,
     *          "codigo": "MT-001",
     *          "nombre": "Cambio de aceite",
     *          "descripcion": "Mantenimiento preventivo para motor",
     *          "costo": 150.00,
     *          "tiempo_estimado": 2,
     *          "manual_procedimiento": "Revisar aceite SAE 15W40",
     *          "tipo_maquinaria_id": 1,
     *          "created_at": "2025-10-06T14:22:00.000000Z",
     *          "updated_at": "2025-10-06T14:22:00.000000Z",
     *          "tipo_maquinaria": {
     *              "id": 1,
     *              "nombre": "Excavadora"
     *          }
     *      }
     *  ]
     * }
     */
    public function index()
    {
        $mantenimientos = Mantenimiento::with('tipoMaquinaria')->get();
        
        return response()->json([
            'status' => true,
            'data' => $mantenimientos
        ]);
    }

    /**
     * @group Mantenimientos
     * 
     * Crear un nuevo mantenimiento
     * 
     * Este endpoint permite registrar un nuevo mantenimiento en el sistema.
     * 
     * @bodyParam codigo string required Código único del mantenimiento. Example: MT-001
     * @bodyParam nombre string required Nombre del mantenimiento. Example: Cambio de aceite
     * @bodyParam descripcion string required Descripción detallada. Example: Mantenimiento preventivo de motor
     * @bodyParam costo numeric required Costo estimado del mantenimiento. Example: 150.50
     * @bodyParam tiempo_estimado numeric Tiempo estimado en horas. Example: 2
     * @bodyParam manual_procedimiento string Manual o descripción del procedimiento. Example: Revisar filtro y nivel de aceite
     * @bodyParam tipo_maquinaria_id integer required ID del tipo de maquinaria asociada. Example: 1
     * 
     * @response 201 {
     *  "status": true,
     *  "message": "Mantenimiento creado exitosamente",
     *  "data": {
     *      "id": 1,
     *      "codigo": "MT-001",
     *      "nombre": "Cambio de aceite",
     *      "descripcion": "Mantenimiento preventivo de motor",
     *      "costo": 150.50,
     *      "tipo_maquinaria_id": 1
     *  }
     * }
     */
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

    /**
     * @group Mantenimientos
     * 
     * Mostrar un mantenimiento por ID
     * 
     * Este endpoint devuelve la información de un mantenimiento específico.
     * 
     * @urlParam id integer required ID del mantenimiento. Example: 1
     * 
     * @response 200 {
     *  "status": true,
     *  "data": {
     *      "id": 1,
     *      "codigo": "MT-001",
     *      "nombre": "Cambio de aceite",
     *      "descripcion": "Mantenimiento preventivo de motor",
     *      "costo": 150.00
     *  }
     * }
     * 
     * @response 404 {
     *  "status": false,
     *  "message": "Mantenimiento no encontrado"
     * }
     */
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

    /**
     * @group Mantenimientos
     * 
     * Actualizar un mantenimiento existente
     * 
     * Este endpoint permite modificar los datos de un mantenimiento.
     * 
     * @urlParam id integer required ID del mantenimiento a actualizar. Example: 1
     * @bodyParam codigo string Código del mantenimiento. Example: MT-001
     * @bodyParam nombre string Nombre del mantenimiento. Example: Cambio de aceite
     * @bodyParam descripcion string Descripción detallada. Example: Mantenimiento preventivo
     * @bodyParam costo numeric Costo del mantenimiento. Example: 180.00
     * @bodyParam tipo_maquinaria_id integer ID del tipo de maquinaria asociada. Example: 1
     * 
     * @response 200 {
     *  "status": true,
     *  "message": "Mantenimiento actualizado exitosamente",
     *  "data": {
     *      "id": 1,
     *      "codigo": "MT-001",
     *      "nombre": "Cambio de aceite actualizado"
     *  }
     * }
     */
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

    /**
     * @group Mantenimientos
     * 
     * Eliminar un mantenimiento
     * 
     * Este endpoint elimina un mantenimiento según su ID.
     * 
     * @urlParam id integer required ID del mantenimiento a eliminar. Example: 1
     * 
     * @response 200 {
     *  "status": true,
     *  "message": "Mantenimiento eliminado exitosamente"
     * }
     * 
     * @response 404 {
     *  "status": false,
     *  "message": "Mantenimiento no encontrado"
     * }
     */
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
