<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorTicketsController extends Controller
{
    public function store(Vendor $vendor)
    {
        $this->authorize('createTicket', $vendor);

        $ticket = $vendor->tickets()->create([
            'title' => request()->title,
            'subtitle' => request()->subtitle,
            'date' => request()->date,
            'venue' => request()->venue,
            'city' => request()->city,
            'price' => request()->price,
            'additional_info' => request()->additional_info,
        ]);

        return response()->json($ticket, 201);
    }
}
