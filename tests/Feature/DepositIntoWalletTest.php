<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DepositIntoWalletTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function usersCanDepositMoneyIntoTheirWallets()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'amount' => 12700,
        ]);

        $response = $this->actingAs($user)->postJson('/api/wallet/deposit', [
            'amount' => 30000
        ]);

        $response->assertStatus(204);
        $this->assertEquals(42700, $wallet->fresh()->amount);
    }
}
