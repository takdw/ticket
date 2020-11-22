<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create an Admin Role
        $adminRole = Role::factory()->create(['name' => 'admin']);

        // Create an Admin
        $admin = User::factory()->create(['email' => 'admin@admin.com', 'name' => 'Admin']);
        $admin->roles()->sync($adminRole->id);

        // Create a User
        $user = User::factory()->create(['email' => 'mamushassefa@gmail.com']);

        // Add balance to the user's wallet
        Wallet::where('user_id', $user->id)->first()->update(['amount' => 200000]);

        // Create a Vendor
        $vendor = Vendor::factory()->create(['tin' => '0022334455', 'name' => 'Sheger Events and Promotions']);

        // Create a Ticket for the Vendor
        Ticket::factory()->create(['vendor_id' => $vendor->id]);
    }
}
