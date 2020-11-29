<?php

namespace Tests\Feature;

use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VendorAuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function vendorsCanBeAuthenticated()
    {
        $this->withoutExceptionHandling();

        $vendor = Vendor::factory()->create([
            'tin' => '0012312323',
            'password' => Hash::make('password'),
        ]);

        $token = $this->postJson('/api/vendors/login', [
            'tin' => '0012312323',
            'password' => 'password'
        ])->assertStatus(200)
            ->assertJsonStructure(['token', 'vendor'])
            ->getData()
            ->token;

        $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/vendor')
            ->assertStatus(200)
            ->assertJsonStructure([
                'name',
            ]);
    }

    /** @test */
    public function deactivatedVendorsCannotLogin()
    {
        $vendor = Vendor::factory()->create([
            'tin' => '0123456789',
            'password' => Hash::make('super-strong-password'),
            'deactivated_at' => now()->subDays(1),
        ]);

        $this->postJson('/api/vendors/login', [
            'tin' => '0123456789',
            'password' => 'super-strong-password',
        ])->assertStatus(422)
            ->assertJson([
                'inactive' => 'Your account has been blocked. Please contact the administrators.',
            ]);
    }
}
