<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Empleado;
   /**
     * @OA\Get(
     *     path="/api/empleados",
     *     summary="Listar empleados",
     *     tags={"Empleados"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de empleados con solicitudes",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Empleado"))
     *     )
     * )
     */
class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados = Empleado::with('solicitudes')->get();
        
        return response()->json([
            'status' => true,
            'data' => $empleados
        ]);
    }
   /**
     * @OA\Post(
     *     path="/api/empleados",
     *     summary="Crear un nuevo empleado",
     *     tags={"Empleados"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Empleado")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Empleado creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Empleado")
     *     ),
     *     @OA\Response(response=422, description="Error de validación")
     * )
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
     * @OA\Get(
     *     path="/api/empleados/{id}",
     *     summary="Obtener un empleado por ID",
     *     tags={"Empleados"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del empleado",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Empleado encontrado", @OA\JsonContent(ref="#/components/schemas/Empleado")),
     *     @OA\Response(response=404, description="Empleado no encontrado")
     * )
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
     * @OA\Put(
     *     path="/api/empleados/{id}",
     *     summary="Actualizar un empleado",
     *     tags={"Empleados"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del empleado",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Empleado")
     *     ),
     *     @OA\Response(response=200, description="Empleado actualizado exitosamente", @OA\JsonContent(ref="#/components/schemas/Empleado")),
     *     @OA\Response(response=404, description="Empleado no encontrado"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
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
     * @OA\Delete(
     *     path="/api/empleados/{id}",
     *     summary="Eliminar un empleado",
     *     tags={"Empleados"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del empleado",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Empleado eliminado exitosamente"),
     *     @OA\Response(response=404, description="Empleado no encontrado")
     * )
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
