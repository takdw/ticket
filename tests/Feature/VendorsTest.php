<?php

namespace Tests\Feature;

use App\Models\DigitalTicket;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
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

    /** @test */
    public function vendorsCanUpdateTheirTickets()
    {
        $this->withoutExceptionHandling();

        $vendor = Vendor::factory()->create();
        $ticket = Ticket::factory()->create([
            'vendor_id' => $vendor->id,
            'title' => 'Test Ticket',
            'subtitle' => 'A very tasty ticket',
            'date' => '20-11-2020 12:00PM',
            'venue' => 'Some outlandish location',
            'city' => 'Addis Ababa',
            'price' => 10000,
            'additional_info' => 'No kids allowed',
        ]);

        Sanctum::actingAs($vendor);

        $this->postJson("/api/vendor/tickets/{$ticket->id}/edit", [
            'title' => 'Updated Ticket Title',
            'subtitle' => 'Updated Ticket Subtitle',
            'date' => '22-12-2020 6:00PM',
            'venue' => 'Updated Venue',
            'city' => 'Updated City',
            'price' => 12300,
            'additional_info' => 'Updated Additional Info',
        ])->assertStatus(204);

        tap($ticket->fresh(), function ($ticket) {
            $this->assertEquals('Updated Ticket Title', $ticket->title);
            $this->assertEquals('Updated Ticket Subtitle', $ticket->subtitle);
            $this->assertTrue(Carbon::parse('22-12-2020 6:00PM')->equalTo($ticket->date));
            $this->assertEquals('Updated Venue', $ticket->venue);
            $this->assertEquals('Updated City', $ticket->city);
            $this->assertEquals(12300, $ticket->price);
            $this->assertEquals('Updated Additional Info', $ticket->additional_info);
        });
    }
}
