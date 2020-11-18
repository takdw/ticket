<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function usersCanBeAuthenticated()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'email' => 'abebe.balcha@gmail.com',
            'password' => Hash::make('asnake'),
        ]);

        $token = $this->postJson('/api/login', [
            'email' => 'abebe.balcha@gmail.com',
            'password' => 'asnake'
        ])->assertStatus(200)
            ->assertJsonStructure(['token'])
            ->getData()
            ->token;

        $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/user')
            ->assertStatus(200)
            ->assertJsonStructure([
                'name',
                'phone_number',
                'email'
            ]);
    }
}
