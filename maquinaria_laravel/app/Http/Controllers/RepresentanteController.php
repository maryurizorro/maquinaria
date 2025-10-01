<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Representante;

class RepresentanteController extends Controller
{
        /**
     * @OA\Get(
     *     path="/api/representantes",
     *     summary="Listar representantes",
     *     tags={"Representantes"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de representantes con su empresa"
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/representantes",
     *     summary="Crear un nuevo representante",
     *     tags={"Representantes"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre","apellido","documento","telefono","email","empresa_id"},
     *             @OA\Property(property="nombre", type="string", example="Juan"),
     *             @OA\Property(property="apellido", type="string", example="Pérez"),
     *             @OA\Property(property="documento", type="string", example="12345678"),
     *             @OA\Property(property="telefono", type="string", example="3001234567"),
     *             @OA\Property(property="email", type="string", example="juan@example.com"),
     *             @OA\Property(property="empresa_id", type="integer", example=1),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Representante creado exitosamente"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
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
     * @OA\Get(
     *     path="/api/representantes/{id}",
     *     summary="Obtener un representante",
     *     tags={"Representantes"},
     *     @OA\Parameter(
     *         name="id",
     *         description="ID del representante",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Representante encontrado"),
     *     @OA\Response(response=404, description="Representante no encontrado")
     * )
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
     * @OA\Put(
     *     path="/api/representantes/{id}",
     *     summary="Actualizar un representante",
     *     tags={"Representantes"},
     *     @OA\Parameter(
     *         name="id",
     *         description="ID del representante",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="nombre", type="string", example="Carlos"),
     *             @OA\Property(property="apellido", type="string", example="Ramírez"),
     *             @OA\Property(property="documento", type="string", example="98765432"),
     *             @OA\Property(property="telefono", type="string", example="3209876543"),
     *             @OA\Property(property="email", type="string", example="carlos@example.com"),
     *             @OA\Property(property="empresa_id", type="integer", example=2),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Representante actualizado exitosamente"),
     *     @OA\Response(response=404, description="Representante no encontrado")
     * )
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
     * @OA\Delete(
     *     path="/api/representantes/{id}",
     *     summary="Eliminar un representante",
     *     tags={"Representantes"},
     *     @OA\Parameter(
     *         name="id",
     *         description="ID del representante",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Representante eliminado exitosamente"),
     *     @OA\Response(response=404, description="Representante no encontrado")
     * )
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
