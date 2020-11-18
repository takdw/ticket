<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function usersCanRegisterOnTheSystem()
    {
        $this->postJson("/api/users", [
            'name' => 'Abebe Balcha',
            'email' => 'abe_gudegnaw@midroc.com',
            'phone_number' => '0911223344',
            'country' => 'Ethiopia',
            'password' => 'my-password',
            'password_confirmation' => "my-password",
        ])->assertStatus(201);

        $this->assertCount(1, User::all());
        $this->assertDatabaseHas('users', [
            'name' => 'Abebe Balcha',
            'email' => 'abe_gudegnaw@midroc.com',
            'phone_number' => '0911223344',
            'country' => 'Ethiopia',
        ]);
        $this->assertTrue(Hash::check('my-password', User::first()->password));
    }

    /** @test */
    public function emailIsRequired()
    {
        $this->postJson("/api/users", [
            'name' => 'Abebe Balcha',
            'phone_number' => '0911223344',
            'country' => 'Ethiopia',
            'password' => 'my-password',
            'password_confirmation' => "my-password",
        ])->assertStatus(422)
            ->assertJsonValidationErrors('email');

        $this->assertCount(0, User::all());
    }

    /** @test */
    public function emailMustBeValidEmailAddress()
    {
        $this->postJson("/api/users", [
            'name' => 'Abebe Balcha',
            'email' => 'not-an-email-address',
            'phone_number' => '0911223344',
            'country' => 'Ethiopia',
            'password' => 'my-password',
            'password_confirmation' => "my-password",
        ])->assertStatus(422)
            ->assertJsonValidationErrors('email');

        $this->assertCount(0, User::all());
    }

    /** @test */
    public function emailMustBeUnique()
    {
        User::factory()->create([
            'email' => 'abe_gudegnaw@midroc.com',
        ]);

        $this->postJson("/api/users", [
            'name' => 'Abebe Balcha',
            'email' => 'abe_gudegnaw@midroc.com',
            'phone_number' => '0911223344',
            'country' => 'Ethiopia',
            'password' => 'my-password',
            'password_confirmation' => "my-password",
        ])->assertStatus(422)
            ->assertJsonValidationErrors('email');

        $this->assertCount(1, User::all());
    }

    /** @test */
    public function passwordIsRequired()
    {
        $this->postJson("/api/users", [
            'name' => 'Abebe Balcha',
            'email' => 'abe_gudegnaw@midroc.com',
            'phone_number' => '0911223344',
            'country' => 'Ethiopia',
            'password_confirmation' => "my-password",
        ])->assertStatus(422)
            ->assertJsonValidationErrors('password');

        $this->assertCount(0, User::all());
    }

    /** @test */
    public function passwordMustBeConfirmed()
    {
        $this->postJson("/api/users", [
            'name' => 'Abebe Balcha',
            'email' => 'abe_gudegnaw@midroc.com',
            'phone_number' => '0911223344',
            'country' => 'Ethiopia',
            'password' => 'my-password',
            'password_confirmation' => "this-wont-match",
        ])->assertStatus(422)
            ->assertJsonValidationErrors('password');

        $this->assertCount(0, User::all());
    }
}
