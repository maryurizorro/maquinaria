<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CategoriaMaquinaria;

class CategoriaMaquinariaController extends Controller
{
    /**
     * @group Categorías
     * 
     * Listar todas las categorías
     * 
     * Muestra todas las categorías de maquinaria con sus respectivos tipos de maquinaria.
     * 
     * @response 200 {
     *   "status": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "nombre": "Excavadoras",
     *       "descripcion": "Máquinas para excavación",
     *       "tiposMaquinaria": [
     *         {
     *           "id": 1,
     *           "nombre": "Excavadora hidráulica"
     *         }
     *       ]
     *     }
     *   ]
     * }
     */
    public function index()
    {
        $categorias = CategoriaMaquinaria::with('tiposMaquinaria')->get();
        
        return response()->json([
            'status' => true,
            'data' => $categorias
        ]);
    }

    /**
     * @group Categorías
     * 
     * Crear una nueva categoría
     * 
     * Crea una nueva categoría de maquinaria.
     * 
     * @bodyParam nombre string required Nombre de la categoría. Example: Excavadoras
     * @bodyParam descripcion string Descripción de la categoría. Example: Máquinas para excavación
     * 
     * @response 201 {
     *   "status": true,
     *   "message": "Categoría creada exitosamente",
     *   "data": {
     *     "id": 3,
     *     "nombre": "Excavadoras",
     *     "descripcion": "Máquinas para excavación"
     *   }
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "Error de validación",
     *   "errors": {
     *     "nombre": ["El campo nombre es obligatorio."]
     *   }
     * }
     */
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

    /**
     * @group Categorías
     * 
     * Mostrar una categoría por ID
     * 
     * Retorna los datos de una categoría específica junto con sus tipos de maquinaria.
     * 
     * @urlParam id integer required ID de la categoría. Example: 1
     * 
     * @response 200 {
     *   "status": true,
     *   "data": {
     *     "id": 1,
     *     "nombre": "Excavadoras",
     *     "descripcion": "Máquinas para excavación",
     *     "tiposMaquinaria": []
     *   }
     * }
     * @response 404 {
     *   "status": false,
     *   "message": "Categoría no encontrada"
     * }
     */
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

    /**
     * @group Categorías
     * 
     * Actualizar una categoría
     * 
     * Permite actualizar el nombre o descripción de una categoría existente.
     * 
     * @urlParam id integer required ID de la categoría a actualizar. Example: 1
     * @bodyParam nombre string Nombre de la categoría. Example: Grúas
     * @bodyParam descripcion string Descripción de la categoría. Example: Maquinaria de elevación
     * 
     * @response 200 {
     *   "status": true,
     *   "message": "Categoría actualizada exitosamente",
     *   "data": {
     *     "id": 1,
     *     "nombre": "Grúas",
     *     "descripcion": "Maquinaria de elevación"
     *   }
     * }
     * @response 404 {
     *   "status": false,
     *   "message": "Categoría no encontrada"
     * }
     */
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

    /**
     * @group Categorías
     * 
     * Eliminar una categoría
     * 
     * Elimina una categoría existente por su ID.
     * 
     * @urlParam id integer required ID de la categoría a eliminar. Example: 1
     * 
     * @response 200 {
     *   "status": true,
     *   "message": "Categoría eliminada exitosamente"
     * }
     * @response 404 {
     *   "status": false,
     *   "message": "Categoría no encontrada"
     * }
     */
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
