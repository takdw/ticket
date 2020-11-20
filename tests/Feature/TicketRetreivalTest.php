<?php

namespace Tests\Feature;

use App\Models\DigitalTicket;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Testing\Assert;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TicketRetreivalTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function canRetrieveListOfTickets()
    {
        $vendor = Vendor::factory()->create();
        $vendorB = Vendor::factory()->create();
        $tickets = new Collection([
            $ticketA = Ticket::factory()->create(['vendor_id' => $vendor->id]),
            $ticketB = Ticket::factory()->create(['vendor_id' => $vendor->id]),
            $ticketC = Ticket::factory()->create(['vendor_id' => $vendor->id]),
            $ticketD = Ticket::factory()->create(['vendor_id' => $vendorB->id]),
            $ticketE = Ticket::factory()->create(['vendor_id' => $vendorB->id]),
        ]);

        $response = $this->getJson("/api/tickets")
            ->assertStatus(200)
            ->getData();

        $this->assertCount(5, $response);
        $this->assertEquals(collect($response)->pluck('id'), $tickets->pluck('id'));
    }

    /** @test */
    public function ticketsAreRetrievedInChronologicalOrder()
    {
        $vendor = Vendor::factory()->create();
        $tickets = new Collection([
            $ticketA = Ticket::factory()->create(['vendor_id' => $vendor->id, 'date' => now()->addDays(3)]),
            $ticketB = Ticket::factory()->create(['vendor_id' => $vendor->id, 'date' => now()->addDays(7)]),
            $ticketC = Ticket::factory()->create(['vendor_id' => $vendor->id, 'date' => now()->addDays(1)]),
        ]);

        $response = $this->getJson("/api/tickets")
            ->assertStatus(200)
            ->getData();

        $this->assertEquals($ticketC->id, $response[0]->id);
        $this->assertEquals($ticketA->id, $response[1]->id);
        $this->assertEquals($ticketB->id, $response[2]->id);
    }

    /** @test */
    public function canFetchSingleTickets()
    {
        $vendor = Vendor::factory()->create();
        $ticket = Ticket::factory()->create([
            'title' => 'A Test Event',
            'vendor_id' => $vendor->id,
        ]);

        $this->getJson("/api/tickets/{$ticket->id}")
            ->assertStatus(200)
            ->assertJson([
                'id' => 1,
                'vendor_id' => 1,
                'title' => 'A Test Event',
            ]);
    }

    /** @test */
    public function usersCanRetrieveTheTicketsTheyPurchased()
    {
        $userA = User::factory()->create();
        $vendor = Vendor::factory()->create();
        $ticket = Ticket::factory()->create([
            'title' => 'A Test Event',
            'vendor_id' => $vendor->id,
            'price' => 5,
        ]);
        $orderForA = Order::factory()->create([
            'confirmation_number' => 'MY_CONFIRMATION_NUMBER',
            'amount' => 15,
            'user_id' => $userA->id
        ]);
        $digitalTicketA = DigitalTicket::factory()->create([
            'order_id' => $orderForA->id,
            'ticket_id' => $ticket->id,
            'code' => 'SOME_CODE',
        ]);
        $digitalTicketB = DigitalTicket::factory()->create([
            'order_id' => $orderForA->id,
            'ticket_id' => $ticket->id,
            'code' => 'SOME_CODE',
        ]);
        $userB = User::factory()->create();
        $orderForB = Order::factory()->create([
            'confirmation_number' => 'MY_CONFIRMATION_NUMBER',
            'amount' => 15,
            'user_id' => $userB->id
        ]);
        $digitalTicketC = DigitalTicket::factory()->create([
            'order_id' => $orderForB->id,
            'ticket_id' => $ticket->id,
            'code' => 'SOME_CODE',
        ]);
        $digitalTicketC = DigitalTicket::factory()->create([
            'order_id' => $orderForB->id,
            'ticket_id' => $ticket->id,
            'code' => 'SOME_CODE',
        ]);

        Sanctum::actingAs($userA);

        $response = $this->getJson('/api/user/tickets')
            ->assertStatus(200)
            ->json();

        $this->assertCount(2, $response);
    }
}
