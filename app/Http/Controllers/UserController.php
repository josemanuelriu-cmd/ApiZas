<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Iniciar sesión en la aplicación.
     *
     * Logea al usuario en la aplicación.
     *
     * @group Autenticación
     * 
     * @unauthenticated
     * 
     * @bodyParam email string required El correo electrónico del usuario. Ejemplo: john.doe@example.com
     * @bodyParam password string required La contraseña del usuario. Ejemplo: secret123 
     * 
     * @response 200 {
     *  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9!",
     *  "user": {
     *   "id": 1,
     *   "num_partner": 123,
     *   "nickname": "john_doe",
     *   "name": "John Doe",
     *   "email": "john.doe@example.com"
     *  }
     * }
     * @response 401 {
     *   "message": "User not autenticated"
     * }
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');    
    
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'User not autenticated'], 401);
        }

        if (app()->environment('testing')) {
            return response()->json([
                'token' => 'fake-token',
                'user' => $user
            ], 200);
        }

        /** @var \App\Models\User $user */
        $token = $user->createToken('api-token')->accessToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ], 200);
    }
    /**
     * Cerrar sesión en la aplicación.
     *
     * Saca al usuario de la aplicación.
     *
     * @group Autenticación
     * 
     * @response 200 {
     *   "message": "Logged out successfully"
     * }     
     * @response 401 {
     *   "message": "Unauthorized"
     * }
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->token();

        if ($token) {
            $token->revoke();
        }

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
    /**
     * Registrar un nuevo usuario.
     *
     * Crea un nuevo usuario en la aplicación.
     *
     * @group Autenticación
     * 
     * @unauthenticated
     * 
     * @response 201 {
     *  "token": "eyJ0eXAiOiJKV1QiLC",
     *  "user": {  
     *    "id": 1, 
     *    "num_partner": 123,
     *    "nickname": "john_doe",
     *    "name": "John Doe",
     *    "email": "john.doe@example.com"
     *   }
     * }
     * @response 422 scenario="Invalid data" {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "email": [
     *       "The email field is required."
     *     ],
     *     "password": [
     *       "The password field is required."
     *     ]
     *   }
     * }
     * @response 422 scenario="Email already registered" {
     *   "message": "The email has already been taken.",
     *   "errors": {
     *     "email": [
     *       "The email has already been taken."
     *     ]
     *   }
     * }
     *
    */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'num_partner' => 'nullable|integer',
            'nickname' => 'required|string',    
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'type' => 'required|in:admin,junta,partner,guest',
            'registration_date' => 'required|date',
            'telephone' => 'nullable|string',
            'age' => 'required|integer',
            'language' => 'required|in:en,es,ca',
        ]);

        if (!isset($data['num_partner'])) {
            $max = User::max('num_partner');
            $data['num_partner'] = $max ? $max + 1 : 1;
        }

        $user = User::create([
            'num_partner' => $data['num_partner'],
            'nickname' => $data['nickname'],
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'type' => $data['type'],
            'registration_date' => $data['registration_date'],
            'withdrawal_date' => $data['withdrawal_date'] ?? null,
            'telephone' => $data['telephone'] ?? null,
            'age' => $data['age'],
            'language' => $data['language'],
        ]);

        if (app()->environment('testing')) {
            return response()->json([
                'token' => 'fake-token',
                'user' => $user
            ], 201);
        }
        $token = $user->createToken('api-token')->accessToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }
    /**
     * Listar usuarios
     *
     * Devuelve todos los usuarios registrados.
     *
     * @group Usuarios
     * 
     * @response 200 [
     *   {
     *     "id": 1,
     *     "num_partner": 123,
     *     "nickname": "john_doe",
     *     "name": "John Doe",
     *     "email": "john.doe@example.com"
     *   }
     * ]
     * @response 401 {
     *   "message": "Unauthorized"
     * }
     *   
     */
    public function index(): JsonResponse
    {
        $users = User::all();
        return response()->json($users);
    }
    /**
     * Detalle de un usuario
     *
     * Devuelve la información de un usuario específico.
     *
     * @group Usuarios
     * 
     * @urlParam id integer required El ID del usuario. Ejemplo: 1
     * 
     * @response 200 {
     *     "id": 1,
     *     "num_partner": 123,
     *     "nickname": "john_doe",
     *     "name": "John Doe",
     *     "email": "john.doe@example.com"
     * }
     * @response 401 {
     *   "message": "User Not autenticated"
     * }
     * @response 404 {
     *   "message": "Not Found"
     * }
     */
    public function detail($id): JsonResponse
    {
        $user = User::find($id);
        if ($user ===null) { 
            return response()->json([
                'message' => 'Not Found'
            ], 404);
        }
        return response()->json($user);
    }
    /**
     * Crear usuarios
     *
     * Crea un nuevo usuario en la aplicación.
     *
     * @group Usuarios
     * 
     * @bodyParam num_partner integer nullable El número de socio del usuario. Ejemplo: 123
     * @bodyParam nickname string required El apodo del usuario. Ejemplo: john_doe
     * @bodyParam name string required El nombre completo del usuario. Ejemplo: John Doe
     * @bodyParam password string required La contraseña del usuario. Ejemplo: secret123
     * @bodyParam type string required El tipo de usuario. Puede ser "admin", "junta", "partner" o "guest". Ejemplo: partner
     * @bodyParam registration_date date required La fecha de registro del usuario. Ejemplo: 2024-01-01
     * @bodyParam email string El correo electrónico del usuario. Ejemplo: john.doe@example.com
     * @bodyParam telephone string El número de teléfono del usuario. Ejemplo: 123456789
     * @bodyParam age integer required La edad del usuario. Ejemplo: 30
     * @bodyParam language string required El idioma preferido del usuario. Puede ser "en", "es" o "ca". Ejemplo: es
     *     
     * @response 201 {
     *  "token": "eyJ0eXAiOiJKV1QiLC",
     *  "user": {  
     *    "id": 1, 
     *    "num_partner": 123,
     *    "nickname": "john_doe",
     *    "name": "John Doe",
     *    "email": "john.doe@example.com"
     *  }
     * }
     * @response 404 {
     *   "message": "User Not autenticated"
     * } 
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "num_partner": [
     *       "The num partner field is required."
     *     ],
     *     "nickname": [
     *       "The nickname field is required."
     *     ],
     *     "name": [
     *       "The name field is required."
     *     ],
     *     "password": [
     *       "The password field is required."
     *     ],
     *     "type": [
     *       "The type field is required."
     *     ],
     *     "registration_date": [
     *       "The registration date field is required."
     *     ],
     *     "age": [
     *       "The age field is required."
     *     ],
     *     "language": [
     *       "The language field is required."
     *     ]
     *   }
     * }    
    */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'num_partner' => 'nullable|integer',
            'nickname' => 'required|string',
            'name' => 'required|string',
            'password' => 'required|string|min:6',
            'type' => 'required|in:admin,junta,partner,guest',
            'registration_date' => 'required|date',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string',
            'age' => 'required|integer',
            'language' => 'required|in:en,es,ca',
        ]);
        
        if (!isset($data['num_partner'])) {
            $max = User::max('num_partner');
            $data['num_partner'] = $max ? $max + 1 : 1;
        }
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);

        return response()->json($user, 201);
    }
    /**
     * Actualizar usuarios
     *
     * Actualiza la información de un usuario específico.
     *
     * @group Usuarios
     * 
     * @urlParam id integer required El ID del usuario. Ejemplo: 1
     * 
     * @bodyParam num_partner integer El número de socio del usuario. Ejemplo: 123
     * @bodyParam nickname string El apodo del usuario. Ejemplo: john_doe
     * @bodyParam name string El nombre completo del usuario. Ejemplo: John Doe
     * @bodyParam password string La contraseña del usuario. Ejemplo: secret123
     * @bodyParam type string El tipo de usuario. Puede ser "admin", "junta", "partner" o "guest". Ejemplo: partner
     * @bodyParam registration_date date La fecha de registro del usuario. Ejemplo: 2024-01-01
     * @bodyParam email string El correo electrónico del usuario. Ejemplo: john.doe@example.com
     * @bodyParam telephone string El número de teléfono del usuario. Ejemplo: 123456789
     * @bodyParam age integer La edad del usuario. Ejemplo: 30
     * @bodyParam language string El idioma preferido del usuario. Puede ser "en", "es" o "ca". Ejemplo: es
     * 
     * @response 200 {
     *   "id": 1,
     *   "num_partner": 123,
     *   "nickname": "john_doe",
     *   "name": "John Doe",
     *   "email": "john.doe@example.com"
     * }
     * @response 401 {
     *   "message": "User Not autenticated"
     * }
     * @response 403 {
     *   "message": "Forbidden"
     * }
     * @response 404 {
     *   "message": "Not Found"
     * }  
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "num_partner": [
     *       "The num partner must be an integer."
     *     ],
     *     "nickname": [
     *       "The nickname must be a string."
     *     ],
     *     "name": [
     *       "The name must be a string."
     *     ],
     *     "password": [
     *       "The password must be at least 6 characters."
     *     ],
     *     "type": [
     *       "The type must be one of: admin, junta, partner, guest."
     *     ],
     *     "registration_date": [
     *       "The registration date is not a valid date."
     *     ],
     *     "email": [
     *       "The email must be a valid email address."
     *     ],
     *     "telephone": [
     *       "The telephone must be a string."
     *     ],
     *     "age": [
     *       "The age must be an integer."
     *     ],
     *     "language": [
     *       "The language must be one of: en, es, ca."
     *     ]
     *   }
     * }   
    */
    public function update(Request $request, $id): JsonResponse
    {
        $authUser = Auth::user();
        $user = User::find($id); 

        if (!$user) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        if ($authUser->type !== 'admin' && $authUser->id != $id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'num_partner' => 'sometimes|integer',
            'nickname' => 'sometimes|string',
            'name' => 'sometimes|string',
            'password' => 'sometimes|string|min:6',
            'type' => 'sometimes|in:admin,junta,partner,guest',
            'registration_date' => 'sometimes|date',
            'withdrawal_date' => 'nullable|date',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string',
            'age' => 'sometimes|integer',
            'language' => 'sometimes||in:en,es,ca',
        ]);

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        return response()->json($user, 200);
    }
    /**
     * Dar de baja a un usuario
     *
     * Marca a un usuario como dado de baja en la aplicación.
     *
     * @group Usuarios
     * 
     * @urlParam id integer required El ID del usuario. Ejemplo: 1
     * 
     * @response 200 {
     *   "message": "User deleted"
     * }
     * @response 401 {
     *   "message": "User Not autenticated"
     * }
     * @response 403 {
     *   "message": "Forbidden"
     * }
     * @response 404 {
     *   "message": "Not Found"
     * }
     */
    public function destroy($id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $user->update([
            'withdrawal_date' => now(),
        ]);

        return response()->json([
            'message' => 'User deleted',
        ]);
    }
}