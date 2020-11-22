<?php

namespace Tests\Feature;

use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VendorMediaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function canGetTheVendorMedia()
    {
        Storage::fake();

        $this->withoutExceptionHandling();

        $logo = UploadedFile::fake()->image('logo.jpg');
        $logoPath = $logo->storeAs('logos', $logo->getClientOriginalName());

        $license = UploadedFile::fake()->image('license.jpg');
        $licensePath = $license->storeAs('licenses', $license->getClientOriginalName());

        $image = UploadedFile::fake()->image('image.jpg');
        $imagePath = $image->storeAs('images', $image->getClientOriginalName());

        $vendor = Vendor::factory()->create([
            'logo_path' => $logoPath,
            'license_path' => $licensePath,
            'image_path' => $imagePath,
        ]);

        Sanctum::actingAs($vendor);

        $response = $this->getJson('/api/vendor/getMedia')
            ->assertStatus(200)
            ->assertJsonStructure([
                'logo' => [
                    'filename',
                    'preview'
                ],
                'license' => [
                    'filename',
                    'preview'
                ],
                'image' => [
                    'filename',
                    'preview'
                ],
            ]);
    }
}
