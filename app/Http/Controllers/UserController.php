<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function update()
    {
        $validated = request()->validate([
            'old_password' => [
                'required_with:new_password',
                function ($attribute, $value, $fail) {
                    if (! Hash::check($value, auth()->user()->password)) {
                        $fail("Invalid data provided.");
                    }
                },
            ],
            'new_password' => ['required_with:old_password', 'confirmed'],
            'name' => ['sometimes', 'required'],
            'phone_number' => ['sometimes', 'required'],
        ]);

        $user = auth()->user();

        if (!($user instanceof User)) {
            abort(403, 'You are unauthorized for this action.');
        }

        $allowedUpdates = ['name', 'new_password', 'phone_number'];

        foreach ($validated as $key => $value) {
            if (array_search($key, $allowedUpdates) !== false) {
                if ($key == 'new_password') {
                    $user->password = Hash::make($value);
                } else {
                    $user->{$key} = $value;
                }
            }
        }

        if ($user->isDirty()) {
            $user->save();
        }

        return response()->json([], 200);
    }
}
