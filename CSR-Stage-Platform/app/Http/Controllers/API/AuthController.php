<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;

class AuthController extends Controller
{
    /** @return JWTGuard */
    private function guard(): JWTGuard
    {
        $guard = auth('api');
        assert($guard instanceof JWTGuard);
        return $guard;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $token = $this->guard()->attempt($request->only('email', 'password'));

        if (!$token) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = $this->guard()->user();

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->getTTL() * 60,
        ], 200);
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }

    public function refresh()
    {
        $token = $this->guard()->refresh();
        $user = $this->guard()->user();

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->getTTL() * 60,
        ], 200);
    }
}