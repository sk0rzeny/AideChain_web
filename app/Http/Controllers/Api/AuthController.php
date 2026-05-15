<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::with(['role', 'ong'])->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Identifiants incorrects.'],
            ]);
        }

        if ($user->role?->name !== 'ong_agent') {
            return response()->json([
                'message' => 'Cette API est réservée aux agents terrain.',
            ], 403);
        }

        $user->tokens()->where('name', 'mobile')->delete();

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'user'  => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                ],
                'ong' => $user->ong ? [
                    'id'  => $user->ong->id,
                    'nom' => $user->ong->nom,
                ] : null,
            ],
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnecté avec succès.']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['role', 'ong']);

        return response()->json([
            'data' => [
                'user' => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                ],
                'ong' => $user->ong ? [
                    'id'  => $user->ong->id,
                    'nom' => $user->ong->nom,
                ] : null,
            ],
        ]);
    }
}
