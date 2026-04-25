<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Boardgame;

class BoardgameController extends Controller
{
    /**
     * Listar juegos de mesa
     *
     * Devuelve todos los juegos de mesa disponibles.
     *
     * @group Juegos de mesa
     * 
     * @response 200 [
     *   {
     *     "id": 1,
     *     "name": "Juego A",
     *     "slug": "juego-a",
     *     "min_players": 2,
     *     "max_players": 10,
     *     "min_age": 8,
     *     "duration": 60,
     *     "description": "Descripción del juego A"
     *   }
     * ]
     * @response 401 {
     *   "message": "Unauthorized"
     * }
     */
    public function index(): JsonResponse
    {
        $boardgames = Boardgame::with(['types', 'owner'])->get();
        return response()->json($boardgames);
    }
    /**
     * Obtener detalle de un juego de mesa
     *
     * Devuelve la información completa de un juego de mesa específico.
     *
     * @group Juegos de mesa
     *
     * @urlParam id integer required El ID del juego de mesa. Ejemplo: 1
     *
     * @response 200 {
     *   "id": 1,
     *   "name": "Juego A",
     *   "slug": "juego-a",
     *   "min_players": 2,
     *   "max_players": 10,
     *   "min_age": 8,
     *   "duration": 60,
     *   "description": "Descripción del juego A"
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
        $boardgame = Boardgame::with(['types', 'owner'])->find($id);
        if ($boardgame ===null) { 
            return response()->json([
                'message' => 'Not Found'
            ], 404);
        }
        return response()->json($boardgame);
    }
    /**
     * Crear juego de mesa
     *
     * Crea un nuevo juego de mesa con los datos proporcionados.
     *
     * @group Juegos de mesa
     * 
     * @bodyParam name string required El nombre del juego de mesa. Ejemplo: Juego A
     * @bodyParam slug string optional El slug del juego de mesa. Ejemplo: juego-a
     * @bodyParam min_players integer required El número mínimo de jugadores. Ejemplo: 2
     * @bodyParam max_players integer required El número máximo de jugadores. Ejemplo: 10
     * @bodyParam min_age integer required La edad mínima recomendada. Ejemplo: 8
     * @bodyParam duration integer required La duración aproximada en minutos. Ejemplo: 60
     * @bodyParam description string optional La descripción del juego de mesa. Ejemplo: Descripción del juego A
     * @bodyParam owner_user_id integer optional El ID del usuario propietario del juego de mesa. Ejemplo: 1
     * @bodyParam types integer[] optional El array de IDs de tipos asociados al juego de mesa. Ejemplo: [1, 2]     
     * 
     * @response 201 {
     *   "id": 1,
     *   "name": "Juego A",
     *   "slug": "juego-a",
     *   "min_players": 2,
     *   "max_players": 10,
     *   "min_age": 8,
     *   "duration": 60,
     *   "description": "Descripción del juego A",
     *   "owner_user_id": null
     * }     
     * @response 401 {
     *   "message": "Unauthorized"
     * }
     * @response 403 {
     *   "message": "Forbidden"
     * }
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *    "name": [
     *      "El nombre es obligatorio."
     *    ],
     *   "min_players": [
     *      "El número mínimo de jugadores es obligatorio."
     *    ],
     *   "max_players": [
     *      "El número máximo de jugadores es obligatorio."
     *    ],
     *   "min_age": [
     *      "La edad mínima es obligatoria."
     *    ],
     *   "duration": [
     *      "La duración es obligatoria."
     *    ]
     *   }
     * }
     */
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
    /**
     * Eliminar juego de mesa
     *
     * Elimina un juego de mesa del sistema.
     *
     * @group Juegos de mesa
     * 
     * @urlParam id integer required El ID del juego de mesa. Ejemplo: 1
     * 
     * @response 200 {
     *   "message": "Boardgame deleted"
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
        $boardgames = Boardgame::find($id);

        if (!$boardgames) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        $boardgames->delete();
        return response()->json([
            'message' => 'Boardgame deleted',
        ]);
    }
    /**
     * Actualizar juego de mesa
     *
     * Actualiza la información de un juego de mesa específico.
     *
     * @group Juegos de mesa
     * 
     * @urlParam id integer required El ID del juego de mesa. Ejemplo: 1
     * 
     * @bodyParam name string optional El nombre del juego de mesa. Ejemplo: Juego A
     * @bodyParam slug string optional El slug del juego de mesa. Ejemplo: juego-a
     * @bodyParam min_players integer optional El número mínimo de jugadores. Ejemplo: 2
     * @bodyParam max_players integer optional El número máximo de jugadores. Ejemplo: 10
     * @bodyParam min_age integer optional La edad mínima recomendada. Ejemplo: 8
     * @bodyParam duration integer optional La duración aproximada en minutos. Ejemplo: 60
     * @bodyParam description string optional La descripción del juego de mesa. Ejemplo: Descripción del juego A
     * @bodyParam owner_user_id integer optional El ID del usuario propietario del juego de mesa. Ejemplo: 1
     * @bodyParam types integer[] optional El array de IDs de tipos asociados al juego de mesa. Ejemplo: [1, 2] 
     * 
     * @response 200 {
     *   "id": 1,
     *   "name": "Juego A",
     *   "slug": "juego-a",
     *   "min_players": 2,
     *   "max_players": 10,
     *   "min_age": 8,
     *   "duration": 60,
     *   "description": "Descripción del juego A"
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
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *    "name": [
     *      "El nombre es obligatorio."
     *    ],
     *   "min_players": [
     *      "El número mínimo de jugadores es obligatorio."
     *    ],
     *   "max_players": [
     *      "El número máximo de jugadores es obligatorio."
     *    ],
     *   "min_age": [
     *      "La edad mínima es obligatoria."
     *    ],
     *   "duration": [
     *      "La duración es obligatoria."
     *    ]
     *   }
     * }    
     */
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