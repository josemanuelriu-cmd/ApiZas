<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Boardgame;

class BoardgameController extends Controller
{
    
    public function index(): JsonResponse
    {
        $boardgames = Boardgame::with(['types', 'owner'])->get();
        return response()->json($boardgames);
    }
    
    public function detail($id): JsonResponse
    {
        $boardgame = Boardgame::with(['types', 'owner'])->find($id);
        if ($boardgame ===null) { 
            return response()->json([
                'message' => 'Not Found'
            ], 404);
        }
        return response()->json($boardgame);
    }
    
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string',
            'slug' => 'nullable|string',
            'min_players' => 'required|integer',
            'max_players' => 'required|integer',
            'min_age' => 'required|integer',
            'duration' => 'required|integer',
            'description' => 'nullable|string',
            'owner_user_id' => 'nullable|integer|exists:users,id',
            'types'         => 'nullable|array',
            'types.*'       => 'integer|exists:types,id'
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }

        $boardgames = Boardgame::create($data);
        $boardgames->types()->sync($request->input('types', []));

        return response()->json($boardgames->load('types', 'owner'), 201);
    }
    
    public function destroy($id): JsonResponse
    {
        $boardgames = Boardgame::find($id);

        if (!$boardgames) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        $boardgames->delete();
        return response()->json([
            'message' => 'Boardgame deleted',
        ]);
    }
    
    public function update(Request $request, $id): JsonResponse
    {
        $boardgames = Boardgame::find($id);

        if (!$boardgames) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $data = $request->validate([
            'name' => 'sometimes|string', 
            'slug' => 'sometimes|string', 
            'min_players' => 'sometimes|integer', 
            'max_players' => 'sometimes|integer', 
            'min_age' => 'sometimes|integer', 
            'duration' => 'sometimes|integer', 
            'description' => 'sometimes|string',
            'owner_user_id' => 'sometimes|nullable|integer|exists:users,id',
            'types'         => 'nullable|array',
            'types.*'       => 'integer|exists:types,id'
        ]);
        $boardgames->update($data);
        $boardgames->types()->sync($request->input('types', []));
        return response()->json($boardgames->load('types', 'owner'), 200);
    }    
}