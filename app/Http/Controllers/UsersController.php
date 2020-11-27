<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function store()
    {
        request()->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed'],
        ]);

        if (request()->has('profile_picture')) {
            $profilePicturePath = request()->profile_picture->store('profile_pictures', 'public');
        }

        $user = User::create([
            'name' => request()->name,
            'email' => request()->email,
            'phone_number' => request()->phone_number,
            'country' => request()->country,
            'password' => Hash::make(request()->password),
            'profile_picture' => isset($profilePicturePath) ? $profilePicturePath : null,
        ]);

        return response()->json([], 201);
    }
}
