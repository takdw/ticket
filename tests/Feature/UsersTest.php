<?php

namespace Tests\Feature;

use App\Models\DigitalTicket;
use App\Models\Order;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itHasOrders()
    {
        $user = User::factory()->create();
        Order::factory()->count(4)->create(['user_id' => $user->id]);

        $this->assertCount(4, $user->orders);
    }

    /** @test */
    public function itHasDigitalTickets()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $ticket = Ticket::factory()->create();
        $digitalTickets = [
            DigitalTicket::factory()->create(['order_id' => $order->id, 'ticket_id' => $ticket->id]),
            DigitalTicket::factory()->create(['order_id' => $order->id, 'ticket_id' => $ticket->id]),
            DigitalTicket::factory()->create(['order_id' => $order->id, 'ticket_id' => $ticket->id]),
        ];

        $this->assertCount(3, $user->digitalTickets);
    }

    /** @test */
    public function itIncludesTheRolesInTheJsonOutput()
    {
        $this->withoutExceptionHandling();

        $admin = User::factory()->create();

        $role = Role::factory()->create(['name' => 'admin']);
        $admin->roles()->attach($admin);

        Sanctum::actingAs($admin->fresh());

        $response = $this->getJson("/api/user")
            ->assertStatus(200)
            ->assertJsonStructure([
                'roles_list',
            ]);
    }
}
