<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TipoMaquinaria;

class TipoMaquinariaController extends Controller
{
        /**
     * @OA\Get(
     *     path="/api/tipos-maquinaria",
     *     summary="Listar todos los tipos de maquinaria",
     *     tags={"Tipos de Maquinaria"},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de tipos de maquinaria",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/TipoMaquinaria"))
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/tipos-maquinaria",
     *     summary="Crear un nuevo tipo de maquinaria",
     *     tags={"Tipos de Maquinaria"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre","categoria_id"},
     *             @OA\Property(property="nombre", type="string", example="Excavadora"),
     *             @OA\Property(property="descripcion", type="string", example="Maquinaria pesada usada para excavación"),
     *             @OA\Property(property="categoria_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tipo de maquinaria creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Tipo de maquinaria creado exitosamente"),
     *             @OA\Property(property="data", ref="#/components/schemas/TipoMaquinaria")
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/tipos-maquinaria/{id}",
     *     summary="Obtener un tipo de maquinaria por ID",
     *     tags={"Tipos de Maquinaria"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del tipo de maquinaria",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de maquinaria encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/TipoMaquinaria")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tipo de maquinaria no encontrado"
     *     )
     * )
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
     * @OA\Put(
     *     path="/api/tipos-maquinaria/{id}",
     *     summary="Actualizar un tipo de maquinaria",
     *     tags={"Tipos de Maquinaria"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del tipo de maquinaria",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nombre", type="string", example="Retroexcavadora"),
     *             @OA\Property(property="descripcion", type="string", example="Equipo usado para remover tierra"),
     *             @OA\Property(property="categoria_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de maquinaria actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Tipo de maquinaria actualizado exitosamente"),
     *             @OA\Property(property="data", ref="#/components/schemas/TipoMaquinaria")
     *         )
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/tipos-maquinaria/{id}",
     *     summary="Eliminar un tipo de maquinaria",
     *     tags={"Tipos de Maquinaria"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del tipo de maquinaria",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de maquinaria eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Tipo de maquinaria eliminado exitosamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tipo de maquinaria no encontrado"
     *     )
     * )
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
