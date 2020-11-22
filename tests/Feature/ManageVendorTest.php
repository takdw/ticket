<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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

        $this->postJson('/api/vendor/edit', [
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

        $this->postJson('/api/vendor/edit', [
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

        $this->postJson('/api/vendor/edit', [
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

        $this->postJson('/api/vendor/edit', [
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

        $this->postJson('/api/vendor/edit', [
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

        $response = $this->postJson('/api/vendor/edit', [
            'name' => 'Kurabachew Demsis',
        ])->assertStatus(200);
        $this->assertEquals('Kurabachew Demsis', $vendor->fresh()->name);
    }

    /** @test */
    public function vendorsCanUpdateThierLogo()
    {
        Storage::fake();
        $this->withoutExceptionHandling();

        $vendor = Vendor::factory()->create([
            'logo_path' => 'logos/old-logo.jpg',
        ]);

        Sanctum::actingAs($vendor);

        $response = $this->postJson('/api/vendor/edit', [
            'logo' => UploadedFile::fake()->image('new-logo.jpg'),
        ])->assertStatus(200);
        $this->assertEquals('logos/new-logo.jpg', $vendor->fresh()->logo_path);
    }

    /** @test */
    public function vendorsCanUpdateThierImage()
    {
        Storage::fake();
        $this->withoutExceptionHandling();

        $vendor = Vendor::factory()->create([
            'image_path' => 'images/old-image.jpg',
        ]);

        Sanctum::actingAs($vendor);

        $response = $this->postJson('/api/vendor/edit', [
            'image' => UploadedFile::fake()->image('new-image.jpg'),
        ])->assertStatus(200);
        $this->assertEquals('images/new-image.jpg', $vendor->fresh()->image_path);
    }

    /** @test */
    public function vendorsCanUpdateThierLicense()
    {
        Storage::fake();
        $this->withoutExceptionHandling();

        $vendor = Vendor::factory()->create([
            'license_path' => 'licenses/old-license.jpg',
        ]);

        Sanctum::actingAs($vendor);

        $response = $this->postJson('/api/vendor/edit', [
            'license' => UploadedFile::fake()->image('new-license.jpg'),
        ])->assertStatus(200);
        $this->assertEquals('licenses/new-license.jpg', $vendor->fresh()->license_path);
    }

    /** @test */
    public function unauthenticatedUsersCannotUpdateProfile()
    {
        $this->postJson('/api/vendor/edit')
            ->assertStatus(401);
    }

    /** @test */
    public function authenticatedUsersCannotUpdateVendors()
    {
        $this->actingAs(User::factory()->create())->postJson('/api/vendor/edit')
            ->assertStatus(403);
    }
}
