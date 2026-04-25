<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Game;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    /**
    * Listar todas las partidas
    *
    * Devuelve todas las partidas disponibles.
    *
    * @group Partidas
    *
    * @response 200 [
    *   {
    *     "id": 1,
    *     "zassession_id": 1,
    *     "boardgame_id": 1,
    *     "host_user_id": 1,
    *     "max_players": 10,
    *     "start_time": "14:30:00",
    *     "status": "open",
    *     "necesary_know_how": true
    *   }
    * ]
    * @response 401 {
    *   "message": "Unauthorized"
    * }
    */
    public function indexAll(): JsonResponse
    {
        $games = Game::all();
        return response()->json($games);
    }
    /**
    * Listar partidas de una sesión
    *
    * Devuelve todas las partidas disponibles de una sesión específica.
    *
    * @group Partidas
    *
    * @urlParam session_id integer required El ID de la sesión. Ejemplo: 1
    *
    * @response 200 [
    *   {
    *     "id": 1,
    *     "zassession_id": 1,
    *     "boardgame_id": 1,
    *     "host_user_id": 1,
    *     "max_players": 10,
    *     "start_time": "14:30:00",
    *     "status": "open",
    *     "necesary_know_how": true
    *   }
    * ]
    * @response 401 {
    *   "message": "Unauthorized"
    * }
    */
    public function indexSession($session_id): JsonResponse
    {
        $games = Game::where('zassession_id', $session_id)->get();
        return response()->json($games);
    }
    /**
    * Obtener detalle de una partida
    *
    * Devuelve la información completa de una partida específica.
    *
    * @group Partidas
    *
    * @urlParam id integer required El ID de la partida. Ejemplo: 1
    *
    * @response 200 {
    *   "id": 1,
    *   "zassession_id": 1,
    *   "boardgame_id": 1,
    *   "host_user_id": 1,
    *   "max_players": 10,
    *   "start_time": "14:30:00",
    *   "status": "open",
    *   "necesary_know_how": true
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
        $games = Game::find($id);
        if ($games ===null) { 
            return response()->json([
                'message' => 'Not Found'
            ], 404);
        }
        return response()->json($games);
    }
    /**
    * Crear partida
    *
    * Crea una nueva partida con los datos proporcionados.
    *
    * @group Partidas
    * 
    * @bodyParam zassession_id integer required El ID de la sesión a la que pertenece la partida. Ejemplo: 1
    * @bodyParam boardgame_id integer required El ID del juego de mesa que se va a jugar. Ejemplo: 1
    * @bodyParam host_user_id integer required El ID del usuario que será el anfitrión de la partida. Ejemplo: 1
    * @bodyParam max_players integer required El número máximo de jugadores permitidos en la partida. Ejemplo: 10
    * @bodyParam start_time string required La hora de inicio de la partida en formato HH:mm:ss. Ejemplo: 14:30:00
    * @bodyParam status string required El estado actual de la partida (por ejemplo, "open", "en curso", "finalizada"). Ejemplo: open
    * @bodyParam necesary_know_how boolean required Indica si es necesario tener conocimientos previos para jugar la partida. Ejemplo: true
    *
    * @response 201 {
    *   "id": 1,
    *   "zassession_id": 1,
    *   "boardgame_id": 1,
    *   "host_user_id": 1,
    *   "max_players": 10,
    *   "start_time": "14:30:00",
    *   "status": "open",
    *   "necesary_know_how": true
    * }
    * @response 401 {
    *   "message": "Unauthorized"
    * }
    * @response 403 {
    *   "message": "Forbidden"
    * }
    */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'zassession_id' => 'required|integer',
            'boardgame_id' => 'required|integer',
            'host_user_id' => 'required|integer',
            'max_players' => 'required|integer',
            'start_time' => 'required|date_format:H:i:s',
            'status' => 'required|string',
            'necesary_know_how' => 'required|boolean',
        ]);
        $games = Game::create($data);
        return response()->json($games, 201);
    }
    /**
    * Eliminar partida
    *
    * Elimina una partida específica.
    *
    * @group Partidas
    *
    * @urlParam id integer required El ID de la partida. Ejemplo: 1
    *
    * @response 200 {
    *   "message": "Game deleted"
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
        $games = Game::find($id);

        if (!$games) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        $games->delete();
        return response()->json([
            'message' => 'Game deleted',
        ]);
    }
    /**
    * Actualizar partidas
    *
    * Actualiza la información de una partida específica.
    *
    * @group Partidas
    *
    * @urlParam id integer required El ID de la partida. Ejemplo: 1
    *
    * @bodyParam zassession_id integer optional El ID de la sesión a la que pertenece la partida. Ejemplo: 1
    * @bodyParam boardgame_id integer optional El ID del juego de mesa que se va a jugar. Ejemplo: 1
    * @bodyParam host_user_id integer optional El ID del usuario que será el anfitrión de la partida. Ejemplo: 1
    * @bodyParam max_players integer optional El número máximo de jugadores permitidos en la partida. Ejemplo: 10
    * @bodyParam start_time string optional La hora de inicio de la partida en formato HH:mm:ss. Ejemplo: 14:30:00
    * @bodyParam status string optional El estado actual de la partida (por ejemplo, "open", "en curso", "finalizada"). Ejemplo: open
    * @bodyParam necesary_know_how boolean optional Indica si es necesario tener conocimientos previos para jugar la partida. Ejemplo: true
    *
    * @response 200 {
    *   "id": 1,
    *   "zassession_id": 1,
    *   "boardgame_id": 1,
    *   "host_user_id": 1,
    *   "max_players": 10,
    *   "start_time": "14:30:00",
    *   "status": "limited",
    *   "necesary_know_how": true
    * }
    * @response 404 {
    *   "message": "Not Found"
    * }
    * @response 401 {
    *   "message": "Unauthorized"
    * }
    * @response 403 {
    *   "message": "Forbidden"
    * }
    */
    public function update(Request $request, $id): JsonResponse
    {
        $games = Game::find($id);

        if (!$games) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $data = $request->validate([
            'zassession_id' => 'sometimes|integer',
            'boardgame_id' => 'sometimes|integer',
            'host_user_id' => 'sometimes|integer',
            'max_players' => 'sometimes|integer',
            'start_time' => 'sometimes|string',
            'status' => 'sometimes|string',
            'necesary_know_how' => 'sometimes|boolean',
        ]);
        $games->update($data);
        return response()->json($games, 200);
    }
    /**
    * Unirse a partida
    *
    * Permite a un usuario unirse a una partida específica.
    *
    * @group Partidas
    *
    * @urlParam game_id integer required El ID de la partida. Ejemplo: 1
    *
    * @response 200 {
    *   "message": "User joined the game"
    * }
    * @response 401 {
    *   "message": "User not autenticated"
    * }
    * @response 404 {
    *   "message": "Not Found"
    * }
    * @response 409 scenario="Game is full" {
    *   "message": "Game is full"
    * }
    * @response 409 scenario="User already joined this game" {
    *   "message": "User already joined this game"
    * }
    */
    public function join($game_id): JsonResponse
    {
        /** @var \App\Models\User $user */        
        $user = Auth::guard('api')->user();
        if (!$user) { 
            return response()->json([
                'message' => 'User not autenticated'
            ], 401);
        } 
        $game = Game::find($game_id);
        if (!$game) { 
            return response()->json([
                'message' => 'Not Found'
            ], 404);
        }
        if ($game->players()->count() >= $game->max_players) {
            return response()->json([
                'message' => 'Game is full'
            ], 409);
        }
        if ($game->players()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'User already joined this game'
            ], 409);
        }
        $game->players()->attach($user->id);
        return response()->json([
            'message' => 'User joined the game'
        ], 200);
    }
    /**
    * Salir de una partida
    *
    * Permite a un usuario salir de una partida específica.
    *
    * @group Partidas
    *
    * @urlParam game_id integer required El ID de la partida. Ejemplo: 1
    *
    * @response 200 {
    *   "message": "User left the game"
    * }
    * @response 401 {
    *   "message": "User not autenticated"
    * }
    * @response 404 {
    *   "message": "Not Found"
    * }
    * @response 409 {
    *   "message": "User is not joined to this game"
    * }    
    */
    public function leave($game_id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user ===null) { 
            return response()->json([
                'message' => 'User not autenticated'
            ], 401);
        } 
        $game = Game::find($game_id);
        if (!$game) { 
            return response()->json([
                'message' => 'Not Found'
            ], 404);
        }
        
        if (!$game->players()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'User is not joined to this game'
            ], 409);
        }
        $game->players()->detach($user->id);
        return response()->json([
            'message' => 'User left the game'
        ], 200);
    }
    /**
    * Obtener jugadores de una partida
    *
    * Devuelve la lista de jugadores que se han unido a una partida específica.
    *
    * @group Partidas
    *
    * @urlParam game_id integer required El ID de la partida. Ejemplo: 1
    *
    * @response 200 [
    *   {
    *     "id": 1,
    *     "num_partner": 123,
    *     "name": "John Doe",
    *     "nickname": "johnny",
    *     "email": "john.doe@Ejemplo.com"
    *   }
    * ]
    * @response 400 {
    *   "message": "No players joined to this game"
    * }    
    * @response 401 {
    *   "message": "User not autenticated"
    * }
    * @response 404 {
    *   "message": "Not Found"
    * }
    */
    public function getUsers($game_id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user ===null) { 
            return response()->json([
                'message' => 'User not autenticated'
            ], 401);
        } 
        $game = Game::find($game_id);
        if ($game ===null) { 
            return response()->json([
                'message' => 'Not Found'
            ], 404);
        }
         if ($game->players()->count() == 0) {
            return response()->json([
                'message' => 'No players joined to this game'
            ], 400);
        }
        return response()->json($game->players, 200);
    }
    /**
     * Estadísticas de una partida
     *
     * Devuelve las estadísticas de una partida específica.
     *
     * @group Partidas
     * 
     * @urlParam game_id integer required El ID de la partida. Ejemplo: 1
     * 
     * @response 200 {
     *   "game_id": 1,
     *   "zassession_id": 1,
     *   "boardgame_id": 1,
     *   "host_user_id": 1,
     *   "max_players": 10,
     *   "total_players": 5,
     *   "start_time": "14:30:00",
     *   "status": "playing",
     *   "necesary_know_how": true
     * }
     * @response 404 {
     *   "message": "Not Found"
     * }    
     * @response 401 {
     *   "message": "Unauthorized"
     * }
     * @response 403 {
     *   "message": "Forbidden"
     * }
     */
    public function gameStats($game_id): JsonResponse
    {
        $game = Game::find($game_id);
        if (!$game) { 
            return response()->json([
                'message' => 'Not Found'
            ], 404);
        }
        return response()->json([
            'game_id' => $game->id,
            'zassession_id' => $game->zassession_id,
            'boardgame_id' => $game->boardgame_id,
            'host_user_id' => $game->host_user_id,
            'max_players' => $game->max_players,
            'total_players' => $game->players()->count(),
            'start_time' => $game->start_time,            
            'status' => $game->status,
            'necesary_know_how' => $game->necesary_know_how,
        ], 200);
    }
}