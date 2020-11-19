<?php

namespace Tests\Feature;

use App\Facades\OrderConfirmation;
use App\Facades\TicketCode;
use App\Mail\OrderComplete;
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
        ]);
        $vendor = Vendor::factory()->create();
        $ticket = Ticket::factory()->create([
            'vendor_id' => $vendor->id,
            'price' => 3000,
        ]);

        $response = $this->actingAs($user)->postJson("/api/tickets/{$ticket->id}/buy", [
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
        $this->assertTrue($user->orders->first()->ticket->is($ticket));
        Mail::assertSent(OrderComplete::class, function ($mail) {
            return $mail->hasTo('kura_kurabachew@sewlesew.com') &&
                    $mail->order->confirmation_number === 'CX45VGHJ7630HKUDCX45VGHJ7630';
        });
    }
}
