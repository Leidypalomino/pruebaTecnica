<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
   
    
    /**
     * @OA\Post(
     * path="/api/auth/login",
     * tags={"Authentication"},
     * summary="Iniciar sesión",
     * description="Autentica un usuario y devuelve un token JWT.",
     * @OA\RequestBody(
     * required=true,
     * description="Credenciales del usuario",
     * @OA\JsonContent(
     * required={"email", "password"},
     * @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="password123")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Login exitoso",
     * @OA\JsonContent(
     * @OA\Property(property="access_token", type="string"),
     * @OA\Property(property="token_type", type="string", example="bearer"),
     * @OA\Property(property="expires_in", type="integer", example=3600)
     * )
     * ),
     * @OA\Response(response=401, description="No autorizado (credenciales incorrectas)")
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Usamos las credenciales validadas para intentar la autenticación
        $credentials = $request->only('email', 'password');

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    
 /**
     * @OA\Post(
     * path="/api/auth/register",
     * tags={"Authentication"},
     * summary="Registrar un nuevo usuario",
     * description="Crea una nueva cuenta de usuario (rol 'customer' por defecto).",
     * @OA\RequestBody(
     * required=true,
     * description="Datos del usuario para el registro",
     * @OA\JsonContent(
     * required={"name", "email", "password", "password_confirmation"},
     * @OA\Property(property="name", type="string", example="John Doe"),
     * @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="password123"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Usuario registrado exitosamente",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="User successfully registered"),
     * @OA\Property(property="user", ref="#/components/schemas/User")
     * )
     * ),
     * @OA\Response(response=400, description="Datos de entrada inválidos")
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            // Hashear la contraseña usando bcrypt (default de Laravel) 
            'password_hash' => Hash::make($request->password),
            'role' => 'customer' // Por defecto
        ]);

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

     /**
     * @OA\Get(
     * path="/api/auth/me",
     * tags={"Authentication"},
     * summary="Obtener perfil del usuario autenticado",
     * description="Devuelve los datos del usuario correspondiente al token JWT.",
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Perfil del usuario",
     * @OA\JsonContent(ref="#/components/schemas/User")
     * ),
     * @OA\Response(response=401, description="No autenticado")
     * )
     */

    public function me()
    {
        return response()->json(auth('api')->user());
    }
    /**
     * @OA\Post(
     * path="/api/auth/logout",
     * tags={"Authentication"},
     * summary="Cerrar sesión",
     * description="Invalida el token JWT actual del usuario.",
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Logout exitoso",
     * @OA\JsonContent(@OA\Property(property="message", type="string", example="Successfully logged out"))
     * ),
     * @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function logout()
    {
        auth('api')->logout(); // Invalida el token

        return response()->json(['message' => 'Successfully logged out']);
    }

     /**
     * @OA\Post(
     * path="/api/auth/refresh",
     * tags={"Authentication"},
     * summary="Refrescar token JWT",
     * description="Obtiene un nuevo token JWT a partir de uno válido (incluso si ha expirado).",
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="Token refrescado exitosamente")
     * )
     */

    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            // El tiempo de expiración se obtiene de config/jwt.php (default 60 mins)
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}