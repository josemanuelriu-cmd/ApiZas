<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Zassession;
use App\Models\Game;
use Illuminate\Support\Facades\Auth;

class ZassessionController extends Controller
{    
    /**
     * Listar sesiones
     *
     * Devuelve todas las sesiones de juego disponibles.
     *
     * @group Sesiones
     * 
     * @response 200 [
     *   {
     *     "id": 1,
     *     "name": "Sesión Viernes",
     *     "event_name": "Noche de juegos",
     *     "date": "2026-04-10",
     *     "max_users": 10
     *  }
     * ]
     * @response 401 {
     *   "message": "Unauthorized"
     * }
     */
    public function index(): JsonResponse
    {
        $zassessions = Zassession::all();
        return response()->json($zassessions);
    }
    /**
     * Obtener detalle de una sesión
     *
     * Devuelve la información completa de una sesión específica.
     *
     * @group Sesiones
     *
     * @urlParam id integer required El ID de la sesión. Ejemplo: 1
     *
     * @response 200 {
     *   "id": 1,
     *   "name": "Sesión Viernes",
     *   "event_name": "Noche de juegos",
     *   "date": "2026-04-10",
     *   "max_users": 10
     * }     
     * @response 401 {
     *   "message": "Unauthorized"
     * }
     * @response 404 {
     *   "message": "Not Found"
     * }
     */
    public function detail($id): JsonResponse
    {
        $zassessions = Zassession::find($id);
        if ($zassessions === null) { 
            return response()->json([
                'message' => 'Not Found'
            ], 404);
        }
        return response()->json($zassessions);
    }
    /**
     * Crear sesión
     *
     * Crea una nueva sesión de juego con límite de jugadores y localización.
     *
     * @group Sesiones
     * 
     * @bodyParam name string required El nombre de la sesión. Ejemplo: Sesión Viernes
     * @bodyParam event_name string required El nombre del evento. Ejemplo: Noche de juegos
     * @bodyParam date date required La fecha de la sesión. Ejemplo: 2026-04-10
     * @bodyParam start_time string required La hora de inicio de la sesión. Ejemplo: 18:00:00
     * @bodyParam end_time string required La hora de fin de la sesión. Ejemplo: 22:00:00
     * @bodyParam max_users integer required El número máximo de usuarios permitidos. Ejemplo: 10
     * @bodyParam direction string required La dirección de la sesión. Ejemplo: Calle Principal 123
     * @bodyParam latitude numeric required La latitud de la ubicación. Ejemplo: 40.7128
     * @bodyParam longitude numeric required La longitud de la ubicación. Ejemplo: -74.0060
     *
     * @response 201 {
     *   "id": 1,
     *   "name": "Sesión Viernes",
     *   "event_name": "Noche de juegos",
     *   "date": "2026-04-10",
     *   "max_users": 10
     * }
     * @response 401 {
     *   "message": "Unauthorized"
     * }
     * @response 403 {
     *   "message": "Forbidden"
     * }
     * @response 404 {
     *   "message": "User Not Found"
     * }
     * @response 422 {
     *   "message": "The given data was invalid.",
     *  "errors": {
     *    "name": [
     *      "El nombre es obligatorio."
     *    ],
     *    "event_name": [
     *      "El nombre del evento es obligatorio."
     *    ],
     *    "date": [
     *      "La fecha es obligatoria."
     *    ],
     *    "start_time": [
     *      "La hora de inicio es obligatoria."
     *    ],
     *    "end_time": [
     *      "La hora de fin es obligatoria."
     *    ],
     *    "max_users": [
     *      "El número máximo de usuarios es obligatorio."
     *    ],
     *    "direction": [
     *      "La dirección es obligatoria."
     *    ],
     *    "latitude": [
     *      "La latitud es obligatoria."
     *    ],
     *    "longitude": [
     *      "La longitud es obligatoria."
     *    ]
     *  }
     * }     
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'event_name' => 'required|string|min:3|max:255',
            'date' => 'required|date|after_or_equal:today|unique:zassessions,date',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
            'max_users' => 'required|integer|min:1|max:100',
            'direction' => 'required|string',
            'latitude' => 'required|numeric|min:-90|max:90',
            'longitude' => 'required|numeric|min:-180|max:180',
        ]);
        $zassessions = Zassession::create($data);

        return response()->json($zassessions, 201);
    }
    /**
     * Eliminar sesión
     *
     * Elimina una sesión del sistema.
     *
     * @group Sesiones
     *
     * @urlParam id integer required El ID de la sesión. Ejemplo: 1
     * 
     * @response 200 {
     *   "message": "Zassession deleted"
     * }     
     * @response 401 {
     *   "message": "Unauthorized"
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
        $zassessions = Zassession::find($id);

        if (!$zassessions) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $zassessions->delete();

        return response()->json([
            'message' => 'Zassession deleted',
        ]);
    }
    /**
     * Actualizar sesión
     *
     * Actualiza los datos de una sesión existente.
     *
     * @group Sesiones
     *
     * @urlParam id integer required El ID de la sesión. Ejemplo: 1
     * 
     * @bodyParam name string optional El nombre de la sesión. Ejemplo: Sesión Viernes
     * @bodyParam event_name string optional El nombre del evento. Ejemplo: Noche de juegos
     * @bodyParam date date optional La fecha de la sesión. Ejemplo: 2026-04-10
     * @bodyParam start_time string optional La hora de inicio de la sesión. Ejemplo: 18:00:00
     * @bodyParam end_time string optional La hora de fin de la sesión. Ejemplo: 22:00:00
     * @bodyParam max_users integer optional El número máximo de usuarios permitidos. Ejemplo: 10
     * @bodyParam direction string optional La dirección de la sesión. Ejemplo: Calle Principal 123
     * @bodyParam latitude numeric optional La latitud de la ubicación. Ejemplo: 40.7128
     * @bodyParam longitude numeric optional La longitud de la ubicación. Ejemplo: -74.0060
     * 
     * @response 200 {
     *   "id": 1,
     *   "name": "Sesión Viernes",
     *   "event_name": "Noche de juegos",
     *   "date": "2026-04-10",
     *   "max_users": 10
     * }
     * @response 401 {
     *   "message": "Unauthorized"
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
     *    "name": [
     *      "El nombre es obligatorio."
     *    ],
     *    "event_name": [
     *      "El nombre del evento es obligatorio."
     *    ],
     *    "date": [
     *      "La fecha es obligatoria."
     *    ],
     *    "start_time": [
     *      "La hora de inicio es obligatoria."
     *    ],
     *    "end_time": [
     *      "La hora de fin es obligatoria."
     *    ],
     *    "max_users": [
     *      "El número máximo de usuarios es obligatorio."
     *    ],
     *    "direction": [
     *      "La dirección es obligatoria."
     *    ],
     *    "latitude": [
     *      "La latitud es obligatoria."
     *    ],
     *    "longitude": [
     *      "La longitud es obligatoria."
     *    ]
     *   }
     * }     
     */
    public function update(Request $request, $id): JsonResponse
    {
        $zassessions = Zassession::find($id);

        if (!$zassessions) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $data = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'event_name' => 'required|string|min:3|max:255',
            'date' => 'required|date|after_or_equal:today|unique:zassessions,date,'. $id,
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
            'max_users' => 'required|integer|min:1|max:100',
            'direction' => 'required|string',
            'latitude' => 'required|numeric|min:-90|max:90',
            'longitude' => 'required|numeric|min:-180|max:180',
        ]);
        $zassessions->update($data);
        return response()->json($zassessions, 200);
    }
    /**
     * Unirse a una sesión
     *
     * Permite al usuario autenticado unirse a una sesión si hay plazas disponibles.
     *
     * @group Sesiones
     *
     * @urlParam session_id integer required ID de la sesión. Ejemplo: 1
     *
     * @response 200 {
     *   "message": "User joined the session"
     * }
     * @response 401 {
     *   "message": "Unauthorized"
     * }
     * @response 403 {
     *   "message": "Forbidden"
     * }
     * @response 404 {
     *   "message": "Not Found"
     * }     
     * @response 409 scenario="Sesión llena" {
     *   "message": "Session is full"
     * }
     * @response 409 scenario="Usuario ya inscrito" {
     *   "message": "User already joined this session"
     * }
     */
    public function join($session_id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user ===null) { 
            return response()->json([
                'message' => 'User Not autenticated'
            ], 401);
        } 
        $zassession = Zassession::find($session_id);
        if (!$zassession) { 
            return response()->json([
                'message' => 'Not Found'
            ], 404);
        }
        if ($zassession->users()->count() >= $zassession->max_users) {
            return response()->json([
                'message' => 'Session is full'
            ], 409);
        }
        if ($zassession->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'User already joined this session'
            ], 409);
        }
        $zassession->users()->attach($user->id);
        return response()->json([
            'message' => 'User joined the session'
        ], 200);
    }
    /**
     * Salir de una sesión
     *
     * Permite al usuario abandonar una sesión en la que está inscrito.
     *
     * @group Sesiones
     *
     * @urlParam session_id integer required ID de la sesión. Ejemplo: 1
     * 
     * @response 200 {
     *   "message": "User left the session"
     * }
     * @response 409 {
     *   "message": "User is not joined to this session"
     * }
     * @response 401 {
     *   "message": "Unauthorized"
     * }
     * @response 403 {
     *   "message": "Forbidden"
     * }
     * @response 404 {
     *   "message": "Session Not Found"
     * }     
     */
    public function leave($session_id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user ===null) { 
            return response()->json([
                'message' => 'User Not autenticated'
            ], 401);
        } 
        $zassession = Zassession::find($session_id);
        if ($zassession ===null) { 
            return response()->json([
                'message' => 'Not Found'
            ], 404);
        }
        if (!$zassession->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'User is not joined to this session'
            ], 409);
        }
        $zassession->users()->detach($user->id);
        return response()->json([
            'message' => 'User left the session'
        ], 200);
    }
    /**
     * Obtener usuarios de una sesión
     *
     * Devuelve todos los usuarios que se han unido a una sesión.
     *
     * @group Sesiones
     *
     * @urlParam session_id integer required ID de la sesión. Ejemplo: 1
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
     * @response 400 {
     *   "message": "No users joined to this session"
     * }
     * @response 401 {
     *   "message": "Unauthorized"
     * }
     * @response 403 {
     *   "message": "Forbidden"
     * }
     * @response 404 {
     *   "message": "Not Found"
     * }     
     */
    public function getUsers($session_id): JsonResponse
    {
        $zassession = Zassession::find($session_id);
        if ($zassession ===null) { 
            return response()->json([
                'message' => 'Not Found'
            ], 404);
        }
         if ($zassession->users()->count() == 0) {
            return response()->json([
                'message' => 'No users joined to this session'
            ], 400);
        }
        return response()->json($zassession->users, 200);
    }
    /**
     * Estadísticas de una sesión
     *
     * Devuelve información agregada de una sesión:
     * número de usuarios, plazas disponibles, si está llena, etc.
     *
     * @group Sesiones
     *
     * @urlParam session_id integer required ID de la sesión. Ejemplo: 1
     * @response 200 {
     *   "session_id": 1,
     *   "session_name": "Sesión Viernes",
     *   "max_users": 10,
     *   "users_count": 5,
     *   "available_slots": 5,
     *   "is_full": false,
     *   "start_time": "18:00:00",
     *   "end_time": "23:00:00",
     *   "games_count": 3
     * }      
     * @response 401 {
     *   "message": "Unauthorized"
     * }
     * @response 403 {
     *   "message": "Forbidden"
     * }
     * @response 404 {
     *   "message": "Not Found"
     * }
     */
    public function sessionStats($session_id): JsonResponse
    {
        $zassession = Zassession::find($session_id);
        if (!$zassession) { 
            return response()->json([
                'message' => 'Not Found'
            ], 404);
        }
        $usersCount = $zassession->users()->count();
        $maxUsers = $zassession->max_users;

        return response()->json([
            'session_id' => $zassession->id,
            'session_name' => $zassession->name,
            'max_users' => $maxUsers,
            'users_count' => $usersCount,
            'available_slots' => $maxUsers - $usersCount,
            'is_full' => $usersCount >= $maxUsers,            
            'start_time' => $zassession->start_time->format('H:i:s'),
            'end_time' => $zassession->end_time->format('H:i:s'),
            'games_count' => $zassession->games()->count()
        ], 200);
    }
    /**
     * Estadísticas globales
     *
     * Devuelve métricas generales del sistema:
     * sesiones totales, usuarios por sesión y juegos.
     *
     * @group Sesiones
     * 
     * @response 200 {
     *   "total_sessions": 5,
     *   "total_users_in_sessions": 25,
     *   "users_per_session": [5, 10, 3, 7, 0],
     *   "games_per_session": [2, 4, 1, 3, 0],
     *   "users_per_game": [4, 6, 2, 5, 0]
     * }
     * @response 401 {
     *   "message": "Unauthorized"
     * }
     * @response 403 {
     *   "message": "Forbidden"
     * }
     */
    public function allstats(): JsonResponse
    {
        $total_sessions = Zassession::count();
        $users_per_session = Zassession::withCount('users')->get()->pluck('users_count');
        $games_per_session = Zassession::withCount('games')->get()->pluck('games_count');
        $users_per_game = Game::withCount('players')->get()->pluck('users_count');
        return response()->json([
            'total_sessions' => $total_sessions,
            'total_users_in_sessions' => $users_per_session->sum(),
            'users_per_session' => $users_per_session,
            'games_per_session' => $games_per_session,
            'users_per_game' => $users_per_game
        ], 200);
    }   
}