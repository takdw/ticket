<?php

namespace Tests\Feature;

use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateTicketsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function vendorsCanCreateTickets()
    {
        Storage::fake();
        $this->withoutExceptionHandling();

        $vendor = Vendor::factory()->create();

        Sanctum::actingAs($vendor);

        $this->postJson("/api/vendors/{$vendor->id}/tickets", [
            'title' => 'Test Ticket',
            'subtitle' => 'A very tasty ticket',
            'date' => '20-11-2020 12:00PM',
            'venue' => 'Some outlandish location',
            'city' => 'Addis Ababa',
            'price' => 10000,
            'additional_info' => 'No kids allowed',
            'poster' => UploadedFile::fake()->image('poster.jpg'),
        ])->assertStatus(201);

        $this->assertCount(1, $vendor->tickets);
        tap($vendor->tickets->first(), function ($ticket) {
            $this->assertEquals('Test Ticket', $ticket->title);
            $this->assertEquals('A very tasty ticket', $ticket->subtitle);
            $this->assertTrue(Carbon::parse('20-11-2020 12:00PM')->equalTo($ticket->date));
            $this->assertEquals('Some outlandish location', $ticket->venue);
            $this->assertEquals('Addis Ababa', $ticket->city);
            $this->assertEquals(10000, $ticket->price);
            $this->assertEquals('No kids allowed', $ticket->additional_info);
            $this->assertNull($ticket->published_at);
            $this->assertNull($ticket->approved_at);
            Storage::disk('public')->assertExists($ticket->poster);
        });
    }

    /** @test */
    public function vendorsCanOnlyCreateTicketsForThemselves()
    {
        $vendor = Vendor::factory()->create();
        $otherVendor = Vendor::factory()->create();

        Sanctum::actingAs($otherVendor);

        $this->postJson("/api/vendors/{$vendor->id}/tickets", [
            'title' => 'Test Ticket',
            'subtitle' => 'A very tasty ticket',
            'date' => '20-11-2020 12:00PM',
            'venue' => 'Some outlandish location',
            'city' => 'Addis Ababa',
            'price' => 10000,
            'additional_info' => 'No kids allowed',
            'poster' => UploadedFile::fake()->image('poster.jpg'),
        ])->assertStatus(403);

        $this->assertCount(0, $vendor->tickets);
    }

    /** @test */
    public function unauthenticatedUsersCannotCreateTickets()
    {
        $this->postJson("/api/vendors/1/tickets")->assertStatus(401);
    }

    /** @test */
    public function vendorsCanPublishTicketWheyTheyAreCreated()
    {
        $this->withoutExceptionHandling();

        $vendor = Vendor::factory()->create();

        Sanctum::actingAs($vendor);

        $this->postJson("/api/vendors/{$vendor->id}/tickets", [
            'title' => 'Test Ticket',
            'subtitle' => 'A very tasty ticket',
            'date' => '20-11-2020 12:00PM',
            'venue' => 'Some outlandish location',
            'city' => 'Addis Ababa',
            'price' => 10000,
            'additional_info' => 'No kids allowed',
            'poster' => UploadedFile::fake()->image('poster.jpg'),
            'publishNow' => true,
        ])->assertStatus(201);

        $this->assertNotNull($vendor->tickets->first()->published_at);
    }
}
