<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PublishTicketTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function vendorsCanPublishTicketsForApprovalByAdmins()
    {
        $this->withoutExceptionHandling();

        $vendor = Vendor::factory()->create();
        $ticket = Ticket::factory()->create([
            'vendor_id' => $vendor->id,
            'published_at' => null,
        ]);

        $response = $this->actingAs($vendor)
            ->postJson("/api/tickets/{$ticket->id}/publish");

        $response->assertStatus(204);
        $this->assertNotNull($ticket->fresh()->published_at);
    }

    /** @test */
    public function unauthenticatedUsersCannotPublishTickets()
    {
        $response = $this->postJson("/api/tickets/1/publish");

        $response->assertStatus(401);
    }

    /** @test */
    public function vendorsCanOnlyPublishTheirOwnTickets()
    {
        $vendorA = Vendor::factory()->create();
        $vendorB = Vendor::factory()->create();
        $ticket = Ticket::factory()->create([
            'vendor_id' => $vendorA->id,
            'published_at' => null,
        ]);

        $response = $this->actingAs($vendorB)->postJson("/api/tickets/{$ticket->id}/publish");

        $response->assertStatus(403);
        $this->assertNull($ticket->fresh()->published_at);
    }
}
