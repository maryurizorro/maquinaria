<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TipoMaquinaria;

class TipoMaquinariaController extends Controller
{
       /**
     * Listar todos los tipos de maquinaria.
     *
     * @group Tipos de Maquinaria
     *
     * Este endpoint devuelve la lista completa de tipos de maquinaria registrados, junto con su categoría y mantenimientos relacionados.
     *
     * @response 200 {
     *   "status": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "nombre": "Excavadora",
     *       "descripcion": "Maquinaria usada para excavación",
     *       "categoria": {"id": 1, "nombre": "Pesada"},
     *       "mantenimientos": []
     *     }
     *   ]
     * }
     */
    public function index()
    {
        $tipos = TipoMaquinaria::with('categoria', 'mantenimientos')->get();
        
        return response()->json([
            'status' => true,
            'data' => $tipos
        ]);
    }
 /**
     * Crear un nuevo tipo de maquinaria.
     *
     * @group Tipos de Maquinaria
     *
     * Crea un nuevo tipo de maquinaria dentro de una categoría existente.
     *
     * @bodyParam nombre string required Nombre del tipo de maquinaria. Ejemplo: Excavadora.
     * @bodyParam descripcion string Descripción opcional. Ejemplo: Maquinaria pesada usada para excavación.
     * @bodyParam categoria_id integer required ID de la categoría a la que pertenece. Ejemplo: 1.
     *
     * @response 201 {
     *   "status": true,
     *   "message": "Tipo de maquinaria creado exitosamente",
     *   "data": {
     *     "id": 5,
     *     "nombre": "Excavadora",
     *     "descripcion": "Maquinaria pesada usada para excavación",
     *     "categoria_id": 1
     *   }
     * }
     *
     * @response 422 {
     *   "status": false,
     *   "message": "Error de validación",
     *   "errors": {
     *     "nombre": ["El campo nombre es obligatorio."],
     *     "categoria_id": ["El campo categoria_id es obligatorio."]
     *   }
     * }
     */
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
/**
     * Obtener un tipo de maquinaria por ID.
     *
     * @group Tipos de Maquinaria
     *
     * Muestra los detalles de un tipo de maquinaria específico.
     *
     * @urlParam id integer required ID del tipo de maquinaria. Ejemplo: 1.
     *
     * @response 200 {
     *   "status": true,
     *   "data": {
     *     "id": 1,
     *     "nombre": "Excavadora",
     *     "descripcion": "Equipo de excavación",
     *     "categoria": {"id": 1, "nombre": "Pesada"},
     *     "mantenimientos": []
     *   }
     * }
     *
     * @response 404 {
     *   "status": false,
     *   "message": "Tipo de maquinaria no encontrado"
     * }
     */
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

    /**
     * Actualizar un tipo de maquinaria.
     *
     * @group Tipos de Maquinaria
     *
     * Permite modificar la información de un tipo de maquinaria existente.
     *
     * @urlParam id integer required ID del tipo de maquinaria a actualizar. Ejemplo: 2.
     * @bodyParam nombre string Nombre del tipo de maquinaria. Ejemplo: Retroexcavadora.
     * @bodyParam descripcion string Descripción del tipo de maquinaria. Ejemplo: Equipo para remover tierra.
     * @bodyParam categoria_id integer ID de la categoría. Ejemplo: 2.
     *
     * @response 200 {
     *   "status": true,
     *   "message": "Tipo de maquinaria actualizado exitosamente",
     *   "data": {
     *     "id": 2,
     *     "nombre": "Retroexcavadora",
     *     "descripcion": "Equipo para remover tierra",
     *     "categoria_id": 2
     *   }
     * }
     *
     * @response 404 {
     *   "status": false,
     *   "message": "Tipo de maquinaria no encontrado"
     * }
     */
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
/**
     * Eliminar un tipo de maquinaria.
     *
     * @group Tipos de Maquinaria
     *
     * Elimina un tipo de maquinaria específico del sistema.
     *
     * @urlParam id integer required ID del tipo de maquinaria a eliminar. Ejemplo: 3.
     *
     * @response 200 {
     *   "status": true,
     *   "message": "Tipo de maquinaria eliminado exitosamente"
     * }
     *
     * @response 404 {
     *   "status": false,
     *   "message": "Tipo de maquinaria no encontrado"
     * }
     */
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
