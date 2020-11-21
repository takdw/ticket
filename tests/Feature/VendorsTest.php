<?php

namespace Tests\Feature;

use App\Models\Ticket;
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
}
