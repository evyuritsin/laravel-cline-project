<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user()->load('inspections');
        
        return response()->json([
            'user' => $user,
            'stats' => [
                'total_inspections' => $user->inspections->count(),
                'month_inspections' => $user->inspections()
                    ->whereMonth('created_at', now()->month)
                    ->count(),
            ],
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $request->user()->id,
        ]);

        $user = $request->user();
        $user->update($request->only(['name', 'email']));

        return response()->json($user);
    }
}