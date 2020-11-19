<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ManageVendorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function vendorsCanUpdateTheirPassword()
    {
        $this->withoutExceptionHandling();

        $vendor = Vendor::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        Sanctum::actingAs($vendor);

        $this->patchJson('/api/vendor', [
            'old_password' => 'old-password',
            'new_password' => 'new-password',
            'new_password_confirmation' => 'new-password',
        ])->assertStatus(200);

        $this->assertTrue(Hash::check('new-password', $vendor->fresh()->password));
    }

    /** @test */
    public function oldPasswordIsRequiredWhileUpdatingPassword()
    {
        $vendor = Vendor::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        Sanctum::actingAs($vendor);

        $this->patchJson('/api/vendor', [
            'new_password' => 'new-password',
            'new_password_confirmation' => 'new-password',
        ])->assertStatus(422)
            ->assertJsonValidationErrors('old_password');

        $this->assertTrue(Hash::check('old-password', $vendor->fresh()->password));
    }

    /** @test */
    public function oldPasswordProvidedMustMatchTheCurrentPassword()
    {
        $vendor = Vendor::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        Sanctum::actingAs($vendor);

        $this->patchJson('/api/vendor', [
            'old_password' => 'not-the-old-password',
            'new_password' => 'new-password',
            'new_password_confirmation' => 'new-password',
        ])->assertStatus(422)
            ->assertJsonValidationErrors('old_password');

        $this->assertTrue(Hash::check('old-password', $vendor->fresh()->password));
    }

    /** @test */
    public function newPasswordIsRequired()
    {
        $vendor = Vendor::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        Sanctum::actingAs($vendor);

        $this->patchJson('/api/vendor', [
            'old_password' => 'old-password',
            'new_password_confirmation' => 'new-password',
        ])->assertStatus(422)
            ->assertJsonValidationErrors('new_password');

        $this->assertTrue(Hash::check('old-password', $vendor->fresh()->password));
    }

    /** @test */
    public function theNewPasswordMustBeConfirmed()
    {
        $vendor = Vendor::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        Sanctum::actingAs($vendor);

        $this->patchJson('/api/vendor', [
            'old_password' => 'old-password',
            'new_password' => 'new-password',
            'new_password_confirmation' => 'doesnt-match',
        ])->assertStatus(422)
            ->assertJsonValidationErrors('new_password');

        $this->assertTrue(Hash::check('old-password', $vendor->fresh()->password));
    }

    /** @test */
    public function vendorsCanUpdateThierName()
    {
        $this->withoutExceptionHandling();

        $vendor = Vendor::factory()->create([
            'name' => 'Abebe Balcha',
        ]);

        Sanctum::actingAs($vendor);

        $response = $this->patchJson('/api/vendor', [
            'name' => 'Kurabachew Demsis',
        ])->assertStatus(200);
        $this->assertEquals('Kurabachew Demsis', $vendor->fresh()->name);
    }

    /** @test */
    public function unauthenticatedUsersCannotUpdateProfile()
    {
        $this->patchJson('/api/vendor')
            ->assertStatus(401);
    }

    /** @test */
    public function authenticatedUsersCannotUpdateVendors()
    {
        $this->actingAs(User::factory()->create())->patchJson('/api/vendor')
            ->assertStatus(403);
    }
}
