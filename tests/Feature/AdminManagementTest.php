<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function adminsCanManageVendors()
    {
        $admin = User::factory()->create();
        $role = Role::factory()->create(['name' => 'admin']);
        $admin->roles()->attach($role->id);
        $vendor = Vendor::factory()->create(['name' => 'Vendor Name']);

        Sanctum::actingAs($admin);

        $this->patchJson('/api/vendor', [
            'name' => 'New Vendor Name',
            'vendor_id' => $vendor->id,
        ])->assertStatus(200);
        $this->assertEquals('New Vendor Name', $vendor->fresh()->name);
    }
    
    /** @test */
    public function adminsCanManageUsers()
    {
        $this->withoutExceptionHandling();

        $admin = User::factory()->create();
        $role = Role::factory()->create(['name' => 'admin']);
        $admin->roles()->attach($role->id);
        $user = User::factory()->create(['name' => 'Asfaw Meshesha']);

        Sanctum::actingAs($admin);

        $this->patchJson('/api/user', [
            'name' => 'Sefiw Meshesha',
            'user_id' => $user->id,
        ])->assertStatus(200);
        $this->assertEquals('Sefiw Meshesha', $user->fresh()->name);
    }

    /** @test */
    public function adminsCanDeactivateUsers()
    {
        $this->withoutExceptionHandling();

        $admin = User::factory()->create();
        $role = Role::factory()->create(['name' => 'admin']);
        $admin->roles()->attach($role->id);
        $user = User::factory()->create(['deactivated_at' => null]);

        Sanctum::actingAs($admin);

        $this->postJson("/api/users/{$user->id}/deactivate")
            ->assertStatus(200);
        $this->assertNotNull($user->fresh()->deactivated_at);
    }

    /** @test */
    public function nonAdminUsersCannotDeactivateUsers()
    {
        $user = User::factory()->create(['deactivated_at' => null]);

        Sanctum::actingAs($user);

        $this->postJson("/api/users/{$user->id}/deactivate")
            ->assertStatus(403);
        $this->assertNull($user->fresh()->deactivated_at);
    }

    /** @test */
    public function unauthenticatedUsersCannotDeactivateUsers()
    {
        $user = User::factory()->create(['deactivated_at' => null]);

        $this->postJson("/api/users/{$user->id}/deactivate")
            ->assertStatus(401);
        $this->assertNull($user->fresh()->deactivated_at);
    }

    /** @test */
    public function adminsCanDeactivateVendors()
    {
        $this->withoutExceptionHandling();

        $admin = User::factory()->create();
        $role = Role::factory()->create(['name' => 'admin']);
        $admin->roles()->attach($role->id);
        $vendor = Vendor::factory()->create(['deactivated_at' => null]);

        Sanctum::actingAs($admin);

        $this->postJson("/api/vendors/{$vendor->id}/deactivate")
            ->assertStatus(200);
        $this->assertNotNull($vendor->fresh()->deactivated_at);
    }

    /** @test */
    public function vendorsCannotDeactivateTheirAccounts()
    {
        $vendor = Vendor::factory()->create(['deactivated_at' => null]);

        Sanctum::actingAs($vendor);

        $this->postJson("/api/vendors/{$vendor->id}/deactivate")
            ->assertStatus(403);
        $this->assertNull($vendor->fresh()->deactivated_at);
    }

    /** @test */
    public function nonAdminUsersCannotDeactivateVendors()
    {
        $vendor = Vendor::factory()->create(['deactivated_at' => null]);

        Sanctum::actingAs(User::factory()->create());

        $this->postJson("/api/vendors/{$vendor->id}/deactivate")
            ->assertStatus(403);
        $this->assertNull($vendor->fresh()->deactivated_at);
    }

    /** @test */
    public function unauthenticatedUsersCannotDeactivateVendors()
    {
        $vendor = Vendor::factory()->create(['deactivated_at' => null]);

        $this->postJson("/api/vendors/{$vendor->id}/deactivate")
            ->assertStatus(401);
        $this->assertNull($vendor->fresh()->deactivated_at);
    }
}
