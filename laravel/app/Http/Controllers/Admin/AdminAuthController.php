<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required','email'], 'password' => ['required','string']]);
        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) throw ValidationException::withMessages(['email' => 'The email or password is incorrect.']);
        $user->tokens()->where('name', 'admin-dashboard')->delete();
        return response()->json(['token' => $user->createToken('admin-dashboard', ['admin'])->plainTextToken, 'user' => $user->only(['id','name','email'])]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();
        return response()->json(['message' => 'Signed out.']);
    }
}
