<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ManageProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function usersCanUpdateTheirPassword()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        Sanctum::actingAs($user);

        $this->patchJson('/api/user', [
            'old_password' => 'old-password',
            'new_password' => 'new-password',
            'new_password_confirmation' => 'new-password',
        ])->assertStatus(200);

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    /** @test */
    public function oldPasswordIsRequiredWhileUpdatingPassword()
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        Sanctum::actingAs($user);

        $this->patchJson('/api/user', [
            'new_password' => 'new-password',
            'new_password_confirmation' => 'new-password',
        ])->assertStatus(422)
            ->assertJsonValidationErrors('old_password');

        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }

    /** @test */
    public function oldPasswordProvidedMustMatchTheCurrentPassword()
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        Sanctum::actingAs($user);

        $this->patchJson('/api/user', [
            'old_password' => 'not-the-old-password',
            'new_password' => 'new-password',
            'new_password_confirmation' => 'new-password',
        ])->assertStatus(422)
            ->assertJsonValidationErrors('old_password');

        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }

    /** @test */
    public function newPasswordIsRequired()
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        Sanctum::actingAs($user);

        $this->patchJson('/api/user', [
            'old_password' => 'old-password',
            'new_password_confirmation' => 'new-password',
        ])->assertStatus(422)
            ->assertJsonValidationErrors('new_password');

        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }

    /** @test */
    public function theNewPasswordMustBeConfirmed()
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        Sanctum::actingAs($user);

        $this->patchJson('/api/user', [
            'old_password' => 'old-password',
            'new_password' => 'new-password',
            'new_password_confirmation' => 'doesnt-match',
        ])->assertStatus(422)
            ->assertJsonValidationErrors('new_password');

        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }

    /** @test */
    public function usersCanUpdateThierName()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'name' => 'Abebe Balcha',
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/user', [
            'name' => 'Kurabachew Demsis',
        ])->assertStatus(200);
        $this->assertEquals('Kurabachew Demsis', $user->fresh()->name);
    }

    /** @test */
    public function usersCanUpdateThierPhoneNumber()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'phone_number' => '0911223344',
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/user', [
            'phone_number' => '0911998877',
        ])->assertStatus(200);
        $this->assertEquals('0911998877', $user->fresh()->phone_number);
    }

    /** @test */
    public function unauthenticatedUsersCannotUpdateProfile()
    {
        $this->patchJson('/api/user')
            ->assertStatus(401);
    }
}
