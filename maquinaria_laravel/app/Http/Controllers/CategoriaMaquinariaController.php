<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CategoriaMaquinaria;

/**
 * @OA\Tag(
 *     name="Categorias",
 *     description="Operaciones relacionadas con las categorías de maquinaria"
 * )
 */
class CategoriaMaquinariaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/categorias",
     *     tags={"Categorias"},
     *     summary="Listar todas las categorías",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de categorías con sus tipos de maquinaria",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/CategoriaMaquinaria")
     *             )
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/categorias",
     *     tags={"Categorias"},
     *     summary="Crear una nueva categoría",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre"},
     *             @OA\Property(property="nombre", type="string", example="Excavadoras"),
     *             @OA\Property(property="descripcion", type="string", example="Máquinas para excavación")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Categoría creada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/CategoriaMaquinaria")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/categorias/{id}",
     *     tags={"Categorias"},
     *     summary="Obtener una categoría por ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la categoría",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoría encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/CategoriaMaquinaria")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Categoría no encontrada"
     *     )
     * )
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
     * @OA\Put(
     *     path="/api/categorias/{id}",
     *     tags={"Categorias"},
     *     summary="Actualizar una categoría existente",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la categoría a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nombre", type="string", example="Grúas"),
     *             @OA\Property(property="descripcion", type="string", example="Maquinaria de elevación")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoría actualizada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/CategoriaMaquinaria")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Categoría no encontrada"
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/categorias/{id}",
     *     tags={"Categorias"},
     *     summary="Eliminar una categoría",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la categoría a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoría eliminada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Categoría no encontrada"
     *     )
     * )
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
