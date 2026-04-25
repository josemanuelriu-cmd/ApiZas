<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Game;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function indexAll(): JsonResponse
    {
        $games = Game::all();
        return response()->json($games);
    }
    
    public function indexSession($session_id): JsonResponse
    {
        $games = Game::where('zassession_id', $session_id)->get();
        return response()->json($games);
    }
    
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