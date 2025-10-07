<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

/**
 * @group Autenticación
 *
 * Endpoints para registrar, iniciar sesión, cerrar sesión y obtener el perfil del usuario.
 */
class AuthController extends Controller
{
    /**
     * Registrar un nuevo usuario
     *
     * Permite crear un nuevo usuario con rol, email y contraseña.
     *
     * @bodyParam name string required Nombre completo del usuario. Example: Maryuri Zorro
     * @bodyParam email string required Correo electrónico válido. Example: maryuri@example.com
     * @bodyParam password string required Contraseña del usuario (mínimo 6 caracteres). Example: 123456
     * @bodyParam rol string required Rol del usuario. Debe ser uno de: admin, empleado, supervisor. Example: empleado
     *
     * @response 201 {
     *   "status": true,
     *   "message": "Usuario registrado exitosamente",
     *   "data": {
     *      "user": {
     *          "id": 1,
     *          "name": "Maryuri Zorro",
     *          "email": "maryuri@example.com",
     *          "rol": "empleado"
     *      },
     *      "token": "1|Xyz123abc456...",
     *      "token_type": "Bearer"
     *   }
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "Error de validación",
     *   "errors": {
     *      "email": ["El campo email ya ha sido registrado."]
     *   }
     * }
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|min:6',
            'rol' => 'required|in:admin,empleado,supervisor',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Usuario registrado exitosamente',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }

    /**
     * Iniciar sesión
     *
     * Permite iniciar sesión en el sistema con credenciales válidas y obtener un token.
     *
     * @bodyParam email string required Correo electrónico del usuario. Example: maryuri@example.com
     * @bodyParam password string required Contraseña del usuario. Example: 123456
     *
     * @response 200 {
     *   "status": true,
     *   "message": "Login exitoso",
     *   "data": {
     *      "user": {
     *          "id": 1,
     *          "name": "Maryuri Zorro",
     *          "email": "maryuri@example.com",
     *          "rol": "empleado"
     *      },
     *      "token": "1|Xyz123abc456...",
     *      "token_type": "Bearer"
     *   }
     * }
     * @response 401 {
     *   "status": false,
     *   "message": "Credenciales inválidas"
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "Error de validación"
     * }
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login exitoso',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    /**
     * Cerrar sesión
     *
     * Revoca el token actual del usuario autenticado.
     *
     * @authenticated
     * @response 200 {
     *   "status": true,
     *   "message": "Logout exitoso"
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout exitoso'
        ]);
    }

    /**
     * Obtener perfil del usuario autenticado
     *
     * Devuelve la información del usuario que ha iniciado sesión.
     *
     * @authenticated
     * @response 200 {
     *   "status": true,
     *   "data": {
     *      "id": 1,
     *      "name": "Maryuri Zorro",
     *      "email": "maryuri@example.com",
     *      "rol": "empleado"
     *   }
     * }
     */
    public function me(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => $request->user()
        ]);
    }
}
