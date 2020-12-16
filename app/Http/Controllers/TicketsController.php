<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketsController extends Controller
{
    public function index()
    {
        $tickets = Ticket::approved()
                        ->upcoming()
                        ->orderBy('date');

        $limit = request()->limit;

        if (request()->has('search')) {
            $tickets->where('title', 'like', '%'.request()->search.'%');
        }

        $tickets = $limit && intval($limit)
                    ? $tickets->take($limit)->get()
                    : $tickets->paginate(10);                

        return response()->json($tickets, 200);
    }

    public function show(Ticket $ticket)
    {
        if ($ticket->isApproved()) {
            return response()->json($ticket->load('vendor'), 200);
        }

        abort(404, 'Ticket doesnot exists.');
    }
}
