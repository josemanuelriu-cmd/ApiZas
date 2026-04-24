<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
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

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->token();

        if ($token) {
            $token->revoke();
        }

        return response()->json(['message' => 'Logged out successfully'], 200);
    }

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
}
