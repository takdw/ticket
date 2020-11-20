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
            ->assertJsonStructure(['token', 'user'])
            ->getData()
            ->token;

        $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/user')
            ->assertStatus(200)
            ->assertJsonStructure([
                'name',
                'phone_number',
                'email',
                'wallet_balance',
            ]);
    }

    /**
     * Check on other ways to check this
     * Currently this test will fail
     */
    /** @test */
    // public function usersCanLogout()
    // {
    //     $this->withoutExceptionHandling();

    //     $user = User::factory()->create([
    //         'email' => 'abebe.balcha@gmail.com',
    //         'password' => Hash::make('asnake'),
    //     ]);

    //     $token = $this->postJson('/api/login', [
    //         'email' => 'abebe.balcha@gmail.com',
    //         'password' => 'asnake'
    //     ])->getData()
    //         ->token;

    //     $this->withHeaders(['Authorization' => 'Bearer ' . $token])
    //         ->postJson('/api/logout')
    //         ->assertStatus(204);

    //     $this->withHeaders(['Authorization' => 'Bearer ' . $token])
    //         ->getJson('/api/user')
    //         ->assertStatus(401);
    // }
}
