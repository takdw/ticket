<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{
    public function login()
    {
        $user = User::where('email', request()->email)->first();

        if ($user && Hash::check(request()->password, $user->password)) {
            if (!is_null($user->deactivated_at)) {
                return response()->json([
                    'inactive' => 'Account is inactive. Please contact the administrators.',
                ], 422);
            }

            $user->tokens()->delete();

            $token = $user->createToken(request()->email);

            return response()->json([
                'token' => $token->plainTextToken,
                'user' => $user->toArray(),
            ], 200);
        }

        return response()->json([
            'email' => ['The provided credentials are incorrect.'],
        ], 401);
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json([], 204);
    }
}
