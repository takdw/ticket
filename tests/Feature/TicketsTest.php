<?php

namespace Tests\Feature;

use App\Models\DigitalTicket;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TicketsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itHasDigitalTickets()
    {
        $ticket = Ticket::factory()->create();
        $digitalTickets = [
            DigitalTicket::factory()->create(['ticket_id' => $ticket->id]),
            DigitalTicket::factory()->create(['ticket_id' => $ticket->id]),
            DigitalTicket::factory()->create(['ticket_id' => $ticket->id]),
        ];

        $this->assertCount(3, $ticket->digitalTickets);
    }
}
