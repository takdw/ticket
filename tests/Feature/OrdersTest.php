<?php

namespace Tests\Feature;

use App\Models\DigitalTicket;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function itBelongsToUsers()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($order->user->is($user));
    }

    /** @test */
    public function hasManyDigitalTickets()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $ticket = Ticket::factory()->create();
        DigitalTicket::factory()->count(7)->create([
            'order_id' => $order->id,
            'ticket_id' => $ticket->id,
        ]);

        $this->assertCount(7, $order->digitalTickets);
    }
}
