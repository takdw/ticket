<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function canFetchAllTickets()
    {
        $this->withoutExceptionHandling();

        $admin = User::factory()->create();
        $role = Role::factory()->create(['name' => 'admin']);
        $admin->roles()->sync($admin->id);

        $ticket = Ticket::factory()->create();
        $publishedTicket = Ticket::factory()->create(['published_at' => now()]);
        $approvedTicket = Ticket::factory()->create(['published_at' => now(), 'approved_at' => now()]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/getTickets')
            ->assertStatus(200)
            ->json();

        $this->assertCount(3, $response['data']);
    }

    /** @test */
    public function onlyAdminsCanAccessTheAdminController()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->getJson('/api/getTickets')
            ->assertStatus(403);
    }

    /** @test */
    public function unauthenticatedUsersCannotAccessAdminController()
    {
        $this->getJson('/api/getTickets')
            ->assertStatus(401);
    }
}
