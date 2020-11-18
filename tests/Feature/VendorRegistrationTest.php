<?php

namespace Tests\Feature;

use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VendorRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function vendorsCanRegisterOnTheSystem()
    {
        Storage::fake();

        $this->postJson('/api/vendors', [
            'name' => 'Acme',
            'tin' => '0021234323',
            'license' => UploadedFile::fake()->image('license.jpg'),
            'logo' => UploadedFile::fake()->image('logo.jpg'),
            'image' => UploadedFile::fake()->image('image.jpg'),
        ])->assertStatus(201);

        $this->assertDatabaseHas('vendors', [
            'name' => 'Acme',
            'tin' => '0021234323',
            'license_path' => 'licenses/license.jpg',
            'logo_path' => 'logos/logo.jpg',
            'image_path' => 'images/image.jpg',
            'approved_at' => null,
        ]);
        $this->assertCount(1, Vendor::all());
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
}
