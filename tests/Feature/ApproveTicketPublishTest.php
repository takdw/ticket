<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApproveTicketPublishTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function adminsCanApproveTicketsToBePublished()
    {
        $admin = User::factory()->create();
        $role = Role::factory()->create(['name' => 'admin']);
        $admin->roles()->sync($role->id);
        $ticket = Ticket::factory()->create([
            'published_at' => now()->subDays(1),
            'approved_at' => null,
        ]);

        $this->actingAs($admin)
            ->postJson("/api/tickets/{$ticket->id}/approve")
            ->assertStatus(200);

        $this->assertNotNull($ticket->fresh()->approved_at);
    }

    /** @test */
    public function regularUsersCannotApproveTicketsToBePublished()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'published_at' => now()->subDays(1),
            'approved_at' => null,
        ]);

        $this->actingAs($user)
            ->postJson("/api/tickets/{$ticket->id}/approve")
            ->assertStatus(403);
        $this->assertNull($ticket->fresh()->approved_at);
    }

    /** @test */
    public function unauthenticatedUsersCannotApproveTickets()
    {
        $ticket = Ticket::factory()->create([
            'published_at' => now()->subDays(1),
            'approved_at' => null,
        ]);

        $this->postJson("/api/tickets/{$ticket->id}/approve")
            ->assertStatus(401);
        $this->assertNull($ticket->fresh()->approved_at);
    }
}
