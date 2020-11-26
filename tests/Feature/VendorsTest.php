<?php

namespace Tests\Feature;

use App\Models\DigitalTicket;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VendorsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function canRetreiveListOfTickets()
    {
        $this->withoutExceptionHandling();

        $vendor = Vendor::factory()->create();
        $tickets = collect([
            $ticketA = Ticket::factory()->create(['vendor_id' => $vendor->id]),
            $ticketB = Ticket::factory()->create(['vendor_id' => $vendor->id]),
            $ticketC = Ticket::factory()->create(['vendor_id' => $vendor->id]),
        ]);

        Sanctum::actingAs($vendor);

        $response = $this->getJson('/api/vendor/tickets')
                        ->assertStatus(200)
                        ->json();
        $this->assertCount(3, $response['data']);
        $this->assertEquals($response['data'], $tickets->map(function ($ticket) { return $ticket->fresh(); })->toArray());
    }

    /** @test */
    public function canGetStats()
    {
        $this->withoutExceptionHandling();

        $vendor = Vendor::factory()->create();
        $tickets = Ticket::factory()->count(23)->create(['vendor_id' => $vendor->id, 'price' => 12500]);
        $user1 = User::factory()->create();
        $order1 = Order::factory()->create(['user_id' => $user1->id]);
        $digital_tickets1 = DigitalTicket::factory()->count(12)->create([
            'order_id' => $order1->id,
            'ticket_id' => $tickets->first()->id,
        ]);
        $user2 = User::factory()->create();
        $order2 = Order::factory()->create(['user_id' => $user2->id]);
        $digital_tickets1 = DigitalTicket::factory()->count(3)->create([
            'order_id' => $order2->id,
            'ticket_id' => $tickets->toArray()[12]['id'],
        ]);

        Sanctum::actingAs($vendor);

        $this->getJson('/api/vendor/stats')
            ->assertStatus(200)
            ->assertJson([
                'total_events' => 23,
                'total_tickets_sold' => 15,
                'total_revenue' => 187500,
            ]);
    }

    /** @test */
    public function hasDigitalTickets()
    {
        $vendor = Vendor::factory()->create();
        $ticket = Ticket::factory()->create(['vendor_id' => $vendor->id]);
        $order = Order::factory()->create();
        $digitalTickets = DigitalTicket::factory()->count(12)->create([
            'order_id' => $order->id,
            'ticket_id' => $ticket->id,
        ]);

        $this->assertCount(12, $vendor->digitalTickets);
    }
}
