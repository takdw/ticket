<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketsController extends Controller
{
    public function index()
    {
        $tickets = Ticket::orderBy('date')->get();

        return response()->json($tickets, 200);
    }

    public function show(Ticket $ticket)
    {
        return response()->json($ticket->load('vendor'), 200);
    }
}
