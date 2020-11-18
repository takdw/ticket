<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketApproveController extends Controller
{
    public function store(Ticket $ticket)
    {
        $this->authorize('approve', $ticket);
        
        $ticket->approved_at = now();
        $ticket->save();

        return response()->json([], 200);
    }
}
