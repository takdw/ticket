<?php

namespace Tests\Feature;

use App\Models\DigitalTicket;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DigitalTicketsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itBelongsToAnOrder()
    {
        $ticket = Ticket::factory()->create();
        $order = Order::factory()->create();
        $digitalTicket = DigitalTicket::factory()->create([
            'order_id' => $order->id,
            'ticket_id' => $ticket->id,
        ]);

        $this->assertTrue($digitalTicket->order->is($order));
    }

    /** @test */
    public function itBelongsToATicket()
    {
        $order = Order::factory()->create();
        $ticket = Ticket::factory()->create();
        $digitalTicket = DigitalTicket::factory()->create([
            'ticket_id' => $ticket->id,
            'order_id' => $order->id,
        ]);

        $this->assertTrue($digitalTicket->ticket->is($ticket));
    }
}
