<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 
use App\Http\Resources\AuthResource;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Username atau Password salah.',
            ], 401);
        }

        $user = User::where('username', $request->username)->firstOrFail();
        $token = $user->createToken('auth-token')->plainTextToken;

        return new AuthResource('success', 'Login berhasil', [
            'user' => $user,
            'token' => $token,
        ]);
        
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil',
        ]);
    }
}