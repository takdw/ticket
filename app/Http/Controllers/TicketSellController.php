<?php

namespace App\Http\Controllers;

use App\Facades\OrderConfirmation;
use App\Facades\TicketCode;
use App\Mail\OrderComplete;
use App\Models\DigitalTicket;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class TicketSellController extends Controller
{
    public function store(Ticket $ticket)
    {
        $user = auth()->user();

        $orderTotal = request()->quantity * $ticket->price;

        if ($user->wallet->amount < $orderTotal) {
            abort(422, 'Insufficent balane!');
        }

        $order = $user->orders()->create([
            'confirmation_number' => OrderConfirmation::generate(),
            'amount' => $orderTotal,
            'ticket_id' => $ticket->id,
        ]);

        $digitalTickets = collect();

        for ($i=0; $i < request()->quantity; $i++) { 
            $digitalTicket = new DigitalTicket;

            $digitalTicket->code = TicketCode::generate();
            $digitalTicket->order_id = $order->id;

            $digitalTickets->push($digitalTicket);
        }

        $user->digitalTickets()->saveMany($digitalTickets);
        $user->wallet->amount = $user->wallet->amount - $orderTotal;
        $user->wallet->save();

        Mail::to($user->email)->send(new OrderComplete($order));

        return response()->json(
            [
                'confirmation_number' => $order->confirmation_number,
                'amount' => $order->amount,
                'ticket_id' => $ticket->id,
                'digital_tickets' => $digitalTickets->pluck('code')->values(),
            ], 
            201
        );
    }
}
