<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserTicketsController extends Controller
{
    public function index()
    {
        $tickets = auth()->user()->digitalTickets->load('ticket');

        return response()->json($tickets, 200);
    }
}
