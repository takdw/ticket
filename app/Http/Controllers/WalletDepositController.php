<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WalletDepositController extends Controller
{
    public function store()
    {
        $wallet = auth()->user()->wallet;

        $wallet->amount += request()->amount;
        $wallet->save();

        return response()->json([], 204);
    }
}
