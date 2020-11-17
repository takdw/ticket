<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class PublishTicketsContorller extends Controller
{
    public function store(Ticket $ticket)
    {
        $this->authorize('publish', $ticket);
        
        $ticket->published_at = now();
        $ticket->save();

        return response()->json([], 204);
    }
}
