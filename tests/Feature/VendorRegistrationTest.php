<?php

namespace Tests\Feature;

use App\Models\Vendor;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VendorRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function vendorsCanRegisterOnTheSystem()
    {
        $this->withoutExceptionHandling();

        Storage::fake();

        $this->postJson('/api/vendors', [
            'name' => 'Acme',
            'tin' => '0021234323',
            'license' => UploadedFile::fake()->image('license.jpg'),
            'logo' => UploadedFile::fake()->image('logo.jpg'),
            'image' => UploadedFile::fake()->image('image.jpg'),
            'password' => 'vendor-password',
            'password_confirmation' => 'vendor-password',
        ])->assertStatus(201);

        $this->assertDatabaseHas('vendors', [
            'name' => 'Acme',
            'tin' => '0021234323',
            'verified_at' => null,
        ]);
        $this->assertCount(1, Vendor::all());
        tap(Vendor::first(), function ($vendor) {
            $this->assertTrue(Hash::check('vendor-password', Vendor::first()->password));
            try {
                Storage::disk('public')->get($vendor->logo_path);
                Storage::disk('public')->get($vendor->license_path);
                Storage::disk('public')->get($vendor->image_path);
            } catch (FileNotFoundException $e) {
                $this->fail($e);
            }
        });
    }

    /** @test */
    public function nameIsRequired()
    {
        Storage::fake();

        $this->postJson('/api/vendors', [
            'tin' => '0021234323',
            'license' => UploadedFile::fake()->image('license.jpg'),
            'logo' => UploadedFile::fake()->image('logo.jpg'),
            'image' => UploadedFile::fake()->image('image.jpg'),
        ])->assertStatus(422)
            ->assertJsonValidationErrors('name');

        $this->assertCount(0, Vendor::all());
    }

    /** @test */
    public function tinIsRequired()
    {
        Storage::fake();

        $this->postJson('/api/vendors', [
            'name' => 'Choo Choo',
            'license' => UploadedFile::fake()->image('license.jpg'),
            'logo' => UploadedFile::fake()->image('logo.jpg'),
            'image' => UploadedFile::fake()->image('image.jpg'),
        ])->assertStatus(422)
            ->assertJsonValidationErrors('tin');

        $this->assertCount(0, Vendor::all());
    }

    /** @test */
    public function passwordIsRequired()
    {
        Storage::fake();

        $this->postJson('/api/vendors', [
            'name' => 'Acme',
            'tin' => '0021234323',
            'license' => UploadedFile::fake()->image('license.jpg'),
            'logo' => UploadedFile::fake()->image('logo.jpg'),
            'image' => UploadedFile::fake()->image('image.jpg'),
        ])->assertStatus(422)
            ->assertJsonValidationErrors('password');

        $this->assertCount(0, Vendor::all());
    }

    /** @test */
    public function passwordMustBeConfirmed()
    {
        Storage::fake();

        $this->postJson('/api/vendors', [
            'name' => 'Acme',
            'tin' => '0021234323',
            'license' => UploadedFile::fake()->image('license.jpg'),
            'logo' => UploadedFile::fake()->image('logo.jpg'),
            'image' => UploadedFile::fake()->image('image.jpg'),
            'password' => 'password',
            'password_confirmation' => 'not_matching_password',
        ])->assertStatus(422)
            ->assertJsonValidationErrors('password');

        $this->assertCount(0, Vendor::all());
    }
}
