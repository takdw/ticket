<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DepositIntoWalletTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function adminsCanDepositMoneyIntoUsersWallets()
    {
        $this->withoutExceptionHandling();

        $admin = User::factory()->create();
        $role = Role::factory()->create(['name' => 'admin']);
        $admin->roles()->attach($role->id);
        $user = User::factory()->create()->fresh();
        $user->wallet->amount = 12700;
        $user->wallet->save();

        Sanctum::actingAs($admin);

        $response = $this->postJson("/api/users/{$user->id}/deposit", [
            'amount' => 30000
        ]);

        $response->assertStatus(204);
        $this->assertEquals(42700, $user->fresh()->wallet->amount);
    }

    /** @test */
    public function nonAdminsCannotDepositMoneyIntoWallet()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'amount' => 12700,
        ]);

        $response = $this->actingAs($user)->postJson("/api/users/{$user->id}/deposit", [
            'amount' => 30000
        ]);

        $response->assertStatus(403);
        $this->assertEquals(12700, $wallet->fresh()->amount);
    }

    /** @test */
    public function unauthenticatedUsersCannotDepositMoneyIntoWallets()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'amount' => 12700,
        ]);

        $response = $this->postJson("/api/users/{$user->id}/deposit", [
            'amount' => 30000
        ]);

        $response->assertStatus(401);
        $this->assertEquals(12700, $wallet->fresh()->amount);
    }
}
