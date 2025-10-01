<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Empresa;
/**
 * @OA\Tag(
 *     name="Empresas",
 *     description="Endpoints para gestionar empresas"
 * )
 */
class EmpresaController extends Controller
{
        /**
     * @OA\Get(
     *     path="/api/empresas",
     *     tags={"Empresas"},
     *     summary="Listar todas las empresas",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de empresas",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Empresa"))
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/empresas",
     *     tags={"Empresas"},
     *     summary="Crear una nueva empresa",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre","nit","direccion","telefono","email"},
     *             @OA\Property(property="nombre", type="string", example="Constructora XYZ"),
     *             @OA\Property(property="nit", type="string", example="900123456-7"),
     *             @OA\Property(property="direccion", type="string", example="Calle 123 #45-67"),
     *             @OA\Property(property="telefono", type="string", example="3001234567"),
     *             @OA\Property(property="email", type="string", format="email", example="contacto@xyz.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Empresa creada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Empresa")
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
            'nit' => 'required|string|unique:empresas|max:20',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|unique:empresas|max:255',
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
     * @OA\Get(
     *     path="/api/empresas/{id}",
     *     tags={"Empresas"},
     *     summary="Obtener una empresa por ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la empresa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empresa encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Empresa")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empresa no encontrada"
     *     )
     * )
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
     * @OA\Put(
     *     path="/api/empresas/{id}",
     *     tags={"Empresas"},
     *     summary="Actualizar una empresa existente",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la empresa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="nombre", type="string", example="Constructora ABC"),
     *             @OA\Property(property="nit", type="string", example="900987654-3"),
     *             @OA\Property(property="direccion", type="string", example="Carrera 10 #20-30"),
     *             @OA\Property(property="telefono", type="string", example="3016549870"),
     *             @OA\Property(property="email", type="string", format="email", example="info@abc.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empresa actualizada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Empresa")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empresa no encontrada"
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/empresas/{id}",
     *     tags={"Empresas"},
     *     summary="Eliminar una empresa",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la empresa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empresa eliminada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empresa no encontrada"
     *     )
     * )
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
