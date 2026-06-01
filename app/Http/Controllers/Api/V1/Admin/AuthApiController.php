<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            return response()->json(['status' => false, 'message' => 'Invalid login details.'], 422);
        }

        $user = $request->user();

        return response()->json([
            'status' => true,
            'message' => 'Login successful.',
            'data' => [
                'token' => $user->createToken('hostel-api')->plainTextToken,
                'user' => $user,
            ],
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json(['status' => true, 'message' => 'Success', 'data' => $request->user()]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['status' => true, 'message' => 'Logout successful.', 'data' => null]);
    }
}
