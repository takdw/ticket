<?php

namespace Tests\Feature;

use App\Facades\OrderConfirmation;
use App\Facades\TicketCode;
use App\Mail\OrderComplete;
use App\Models\DigitalTicket;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PurchasingDigitalTicketsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function canPurchaseDigitalTickets()
    {
        Mail::fake();
        $this->withoutExceptionHandling();

        TicketCode::shouldReceive('generate')->andReturn('CXVBVF', 'ERTYJL', 'YUODIY', 'POZTYI');
        OrderConfirmation::shouldReceive('generate')->andReturn('CX45VGHJ7630HKUDCX45VGHJ7630');

        $user = User::factory()->create([
            'email' => 'kura_kurabachew@sewlesew.com',
        ])->fresh();
        $user->wallet->amount = 20000;
        $user->wallet->save();
        $vendor = Vendor::factory()->create();
        $ticket = Ticket::factory()->create([
            'vendor_id' => $vendor->id,
            'price' => 3000,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/tickets/{$ticket->id}/buy", [
            'quantity' => 4,
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'confirmation_number' => 'CX45VGHJ7630HKUDCX45VGHJ7630',
                    'amount' => 12000,
                    'ticket_id' => $ticket->id,
                    'digital_tickets' => [
                        'CXVBVF',
                        'ERTYJL',
                        'YUODIY',
                        'POZTYI'
                    ],
                ]);
        $this->assertCount(4, $user->digitalTickets);
        $this->assertCount(1, $user->orders);
        $this->assertEquals(8000, $user->fresh()->wallet->amount);
        $this->assertCount(4, $user->orders->first()->digitalTickets);
        Mail::assertSent(OrderComplete::class, function ($mail) {
            return $mail->hasTo('kura_kurabachew@sewlesew.com') &&
                    $mail->order->confirmation_number === 'CX45VGHJ7630HKUDCX45VGHJ7630';
        });
    }

    /** @test */
    public function usersMustHaveEnoughBalanceInTheirWalletsToPurchaseATicket()
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'kura_kurabachew@sewlesew.com',
        ])->fresh();
        $user->wallet->amount = 5000;
        $user->wallet->save();
        $vendor = Vendor::factory()->create();
        $ticket = Ticket::factory()->create([
            'vendor_id' => $vendor->id,
            'price' => 3000,
        ]);

        Sanctum::actingAs($user->fresh());

        $response = $this->postJson("/api/tickets/{$ticket->id}/buy", [
            'quantity' => 2,
        ]);

        $response->assertStatus(422);
        $this->assertCount(0, $user->digitalTickets);
        $this->assertCount(0, $user->orders);
        $this->assertEquals(5000, $user->fresh()->wallet->amount);
        $this->assertCount(0, Order::all());
        $this->assertCount(0, DigitalTicket::all());
        Mail::assertNotSent(OrderComplete::class);
    }

    /** @test */
    public function unauthenticatedUsersCanNotPurchaseTickets()
    {
        $this->postJson("/api/tickets/1/buy", [
            'quantity' => 2,
        ])->assertStatus(401);

        $this->assertCount(0, Order::all());
        $this->assertCount(0, DigitalTicket::all());
    }

    /** @test */
    public function vendorsCanNotPurchaseTickets()
    {
        $vendor = Vendor::factory()->create();
        $ticket = Ticket::factory()->create([
            'vendor_id' => $vendor->id,
            'price' => 3000,
        ]);

        Sanctum::actingAs($vendor);

        $this->postJson("/api/tickets/1/buy", [
            'quantity' => 2,
        ])->assertStatus(403);

        $this->assertCount(0, Order::all());
        $this->assertCount(0, DigitalTicket::all());
    }
}
