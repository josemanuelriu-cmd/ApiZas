<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Type;
use Illuminate\Support\Facades\Auth;

class TypeController extends Controller
{
    /**
     * Listar tipos
     *
     * Devuelve todos los tipos disponibles.
     *
     * @group Tipos
     * 
     * @response 200 [
     *   {
     *     "id": 1,
     *     "type": "Tipo A",
     *     "description": "Descripción del tipo A"
     *   }
     * ]
     * @response 401 {
     *   "message": "Unauthorized"
     * }
     * @response 403 {
     *   "message": "Forbidden"
     * }
     */
    public function index(): JsonResponse
    {
        $types = Type::all();
        return response()->json($types);
    }
    /**
     * Obtener detalle de un tipo
     *
     * Devuelve la información completa de un tipo específico.
     *
     * @group Tipos
     *
     * @urlParam id integer required El ID del tipo. Ejemplo: 1
     *
     * @response 200 {
     *   "id": 1,
     *   "type": "Tipo A",
     *   "description": "Descripción del tipo A"
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
    /**
     * Crear tipo
     *
     * Crea un nuevo tipo con nombre y descripción.
     *
     * @group Tipos
     * 
     * @bodyParam type string required El nombre del tipo. Ejemplo: Tipo A
     * @bodyParam description string required La descripción del tipo. Ejemplo: Descripción del tipo A
     *
     * @response 201 {
     *   "id": 1,
     *   "type": "Tipo A",
     *   "description": "Descripción del tipo A"
     * }
     * @response 401 {
     *   "message": "Unauthorized"
     * }
     * @response 403 {
     *   "message": "Forbidden"
     * }
     * @response 422 {
     *   "message": "Validation Error",
     *   "errors": {
     *     "type": [
     *       "The type field is required."
     *     ],
     *     "description": [
     *       "The description field is required."
     *     ]
     *   }
     * }     
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => 'required|string', 
            'description' => 'required|string',
        ]);
        $types = Type::create($data);

        return response()->json($types, 201);
    }
    /**
     * Eliminar tipo
     *
     * Elimina un tipo del sistema.
     *
     * @group Tipos
     *
     * @urlParam id integer required El ID del tipo. Ejemplo: 1
     * 
     * @response 200 {
     *   "message": "Type deleted"
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
        $types = Type::find($id);

        if (!$types) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $types->delete();

        return response()->json([
            'message' => 'Type deleted',
        ]);
    }
    /**
     * Actualizar tipo
     *
     * Actualiza los datos de un tipo existente.
     *
     * @group Tipos
     *
     * @urlParam id integer required El ID del tipo. Ejemplo: 1 
     * 
     * @bodyParam type string optional El nombre del tipo. Ejemplo: Tipo A
     * @bodyParam description string optional La descripción del tipo. Ejemplo: Descripción del tipo A
     * 
     * @response 200 {
     *   "id": 1,
     *   "type": "Tipo A",
     *   "description": "Descripción del tipo A"
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
     *   "message": "Validation Error",
     *   "errors": {
     *     "type": [
     *       "The type field must be a string."
     *     ],
     *     "description": [
     *       "The description field must be a string."
     *     ]
     *   }
     * }
     */
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