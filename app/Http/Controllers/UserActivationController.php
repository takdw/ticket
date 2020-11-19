<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserActivationController extends Controller
{
    public function store(User $user)
    {
        $this->authorize('deactivate', $user);
        
        $user->deactivated_at = now();
        $user->save();

        return response()->json([], 200);
    }
}
