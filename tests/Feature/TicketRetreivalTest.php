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
    public function usersCanOnlySeeListOfPublishedAndApprovedTickets()
    {
        $this->withoutExceptionHandling();

        $ticket = Ticket::factory()->create();
        $publishedTicket = Ticket::factory()->published()->create();
        $approvedTicket = Ticket::factory()->approved()->create();

        $response = $this->getJson("/api/tickets")
            ->assertStatus(200)
            ->getData()
            ->data;

        $this->assertCount(1, $response);
        $this->assertEquals($response[0]->id, $approvedTicket->id);
    }

    /** @test */
    public function ticketsAreRetrievedInChronologicalOrder()
    {
        $vendor = Vendor::factory()->create();
        $tickets = new Collection([
            $ticketA = Ticket::factory()->approved()->create(['vendor_id' => $vendor->id, 'date' => now()->addDays(3)]),
            $ticketB = Ticket::factory()->approved()->create(['vendor_id' => $vendor->id, 'date' => now()->addDays(7)]),
            $ticketC = Ticket::factory()->approved()->create(['vendor_id' => $vendor->id, 'date' => now()->addDays(1)]),
        ]);

        $response = $this->getJson("/api/tickets")
            ->assertStatus(200)
            ->getData()
            ->data;

        $this->assertEquals($ticketC->id, $response[0]->id);
        $this->assertEquals($ticketA->id, $response[1]->id);
        $this->assertEquals($ticketB->id, $response[2]->id);
    }

    /** @test */
    public function canViewApprovedTickets()
    {
        $vendor = Vendor::factory()->create();
        $ticket = Ticket::factory()->approved()->create([
            'title' => 'A Test Event',
            'vendor_id' => $vendor->id,
        ]);

        $this->getJson("/api/tickets/{$ticket->id}")
            ->assertStatus(200)
            ->assertJson([
                'id' => 1,
                'vendor_id' => $vendor->id,
                'title' => 'A Test Event',
            ]);
    }

    /** @test */
    public function cannotViewUnpublishedOrUnapprovedTickets()
    {
        $ticket = Ticket::factory()->create();

        $this->getJson("/api/tickets/{$ticket->id}")
            ->assertStatus(404);
    }

    /** @test */
    public function usersCanRetrieveTheTicketsTheyPurchased()
    {
        $userA = User::factory()->create();
        $vendor = Vendor::factory()->create();
        $ticketA = Ticket::factory()->create(['vendor_id' => $vendor->id]);
        $ticketB = Ticket::factory()->create(['vendor_id' => $vendor->id]);
        $orderA = Order::factory()->create(['user_id' => $userA->id]);
        DigitalTicket::factory()->create(['order_id' => $orderA->id, 'ticket_id' => $ticketA->id]);
        $orderB = Order::factory()->create(['user_id' => $userA->id]);
        DigitalTicket::factory()->count(2)->create(['order_id' => $orderB->id, 'ticket_id' => $ticketB->id]);

        Sanctum::actingAs($userA);

        $response = $this->getJson('/api/user/tickets')
            ->assertStatus(200)
            ->json();

        $this->assertCount(3, $response);
    }

    /** @test */
    public function numberOfReturnedTicketsCanBeLimited()
    {
        $this->withoutExceptionHandling();

        $ticket = Ticket::factory()->approved()->count(3)->create();

        $response = $this->getJson("/api/tickets?limit=2")
            ->assertStatus(200)
            ->json();

        $this->assertCount(2, $response);
    }

    /** @test */
    public function onlyUpcomingTicketsAreRetrievedOnTheHomePage()
    {
        $this->withoutExceptionHandling();

        $pastTicket = Ticket::factory()->approved()->create(['date' => now()->subMonths(1)]);
        $pastTicket2 = Ticket::factory()->approved()->create(['date' => now()->subDays(1)]);
        $upcomingTicket = Ticket::factory()->approved()->create(['date' => now()->addMonths(1)]);

        $response = $this->getJson("/api/tickets")
            ->assertStatus(200)
            ->getData()
            ->data;

        $this->assertCount(1, $response);
        $this->assertEquals($response[0]->id, $upcomingTicket->id);
    }
}
