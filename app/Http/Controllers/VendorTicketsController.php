<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorTicketsController extends Controller
{
    public function index()
    {
        $tickets = auth()->user()->tickets()->paginate(10);

        return response()->json($tickets, 200);
    }

    public function show(Ticket $ticket)
    {
        return response()->json($ticket, 200);
    }

    public function store(Vendor $vendor)
    {
        $this->authorize('createTicket', $vendor);

        $published_at = request()->publishNow === 'true' ? now() : null;

        if (is_null($published_at)) {
            request()->validate([
                'title' => ['required'],
            ]);
        } else {
            request()->validate([
                'title' => ['required'],
                'subtitle' => ['required'],
                'date' => ['required'],
                'venue' => ['required'],
                'city' => ['required'],
                'price' => ['required'],
                'poster' => ['required'],
            ]);
        }


        $ticket = $vendor->tickets()->create([
            'title' => request()->title,
            'subtitle' => request()->subtitle,
            'date' => request()->date,
            'venue' => request()->venue,
            'city' => request()->city,
            'price' => request()->price,
            'additional_info' => request()->additional_info,
            'poster' => request()->poster->store('posters', 'public'),
            'published_at' => $published_at,
        ]);

        return response()->json($ticket, 201);
    }

    public function update(Ticket $ticket)
    {
        $ticket->update([
            'title' => request()->title,
            'subtitle' => request()->subtitle,
            'date' => request()->date,
            'venue' => request()->venue,
            'city' => request()->city,
            'price' => request()->price,
            'additional_info' => request()->additional_info,
        ]);

        return response()->json([], 204);
    }
}
