<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VendorAuthController extends Controller
{
    public function login()
    {
         $vendor = Vendor::where('tin', request()->tin)->first();

        if ($vendor && Hash::check(request()->password, $vendor->password)) {
            $vendor->tokens()->delete();

            $token = $vendor->createToken(request()->tin);

            return response()->json([
                'token' => $token->plainTextToken,
                'vendor' => $vendor->toArray(),
            ], 200);
        }

        return response()->json([
            'tin' => ['The provided credentials are incorrect.'],
        ], 401);
    }
}
