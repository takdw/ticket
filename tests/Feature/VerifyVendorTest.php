<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VerifyVendorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function canVerifyVendors()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $role = Role::factory()->create(['name' => 'admin']);
        $user->roles()->attach($role->id);

        $vendor = Vendor::factory()->create([
            'verified_at' => null,
        ]);

        $this->actingAs($user)->postJson("/api/vendors/{$vendor->id}/verify")
            ->assertStatus(200);
        $this->assertNotNull($vendor->fresh()->verified_at);
    }

    /** @test */
    public function unauthenticatedUsersCannotVerifyVendors()
    {
        $vendor = Vendor::factory()->create([
            'verified_at' => null,
        ]);

        $this->postJson("/api/vendors/{$vendor->id}/verify")
            ->assertStatus(401);
        $this->assertNull($vendor->fresh()->verified_at);
    }

    /** @test */
    public function onlyAdminsCanVerifyVendors()
    {
        $user = User::factory()->create();
        $vendor = Vendor::factory()->create([
            'verified_at' => null,
        ]);

        $this->actingAs($user)->postJson("/api/vendors/{$vendor->id}/verify")
            ->assertStatus(403);
        $this->assertNull($vendor->fresh()->verified_at);
    }
}
