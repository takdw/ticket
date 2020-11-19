<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class WalletDepositController extends Controller
{
    public function store(User $user)
    {
        $this->authorize('deposit', $user);

        $wallet = $user->wallet;

        $wallet->amount += request()->amount;
        $wallet->save();

        return response()->json([], 204);
    }
}
