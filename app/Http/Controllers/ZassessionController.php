<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Zassession;
use Illuminate\Support\Facades\Auth;

class ZassessionController extends Controller
{    

    public function index(): JsonResponse
    {
        $zassessions = Zassession::all();
        return response()->json($zassessions);
    }

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
    
    public function update(Request $request, $id): JsonResponse
    {
        $zassessions = Zassession::find($id);

        if (!$zassessions) {
            return response()->json(['message' => 'Not Found'], 404);
        }

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
        $zassessions->update($data);
        return response()->json($zassessions, 200);
    }
    
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
    
    public function allstats(): JsonResponse
    {
        $total_sessions = Zassession::count();
        $users_per_session = Zassession::withCount('users')->get()->pluck('users_count');
        $games_per_session = Zassession::withCount('games')->get()->pluck('games_count');
        //$users_per_game = Game::withCount('players')->get()->pluck('users_count');
        return response()->json([
            'total_sessions' => $total_sessions,
            'total_users_in_sessions' => $users_per_session->sum(),
            'users_per_session' => $users_per_session,
            'games_per_session' => $games_per_session,
            //'users_per_game' => $users_per_game
        ], 200);
    }   
}