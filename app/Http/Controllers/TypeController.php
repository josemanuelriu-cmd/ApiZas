<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Type;
use Illuminate\Support\Facades\Auth;

class TypeController extends Controller
{
    public function index(): JsonResponse
    {
        $types = Type::all();
        return response()->json($types);
    }

    public function detail($id): JsonResponse
    {
        $types = Type::find($id);
        if ($types ===null) { 
            return response()->json([
                'message' => 'Not Found'
            ], 404);
        }
        return response()->json($types);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => 'required|string', 
            'description' => 'required|string',
        ]);
        $types = Type::create($data);

        return response()->json($types, 201);
    }

    public function destroy($id): JsonResponse
    {
        $types = Type::find($id);

        if (!$types) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $types->delete();

        return response()->json([
            'message' => 'Type deleted',
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $types = Type::find($id);

        if (!$types) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $data = $request->validate([
            'type' => 'sometimes|string',            
            'description' => 'sometimes|string',           
        ]);
        $types->update($data);
        return response()->json($types, 200);
    }
}