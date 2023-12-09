<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    // Database will migrated every time before running the test
    use RefreshDatabase;

    // 
    public function testUserCanLoginWithCorrectCredentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(201);
    }

    public function testUserCannotLoginWithIncorrectCredentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'wrong_password'
        ]);

        $response->assertStatus(422);
    }

    public function testUserCanRegistrationWithCorrectCredentials()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201)->assertJsonStructure([
            'access_token'
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@doe.com'
        ]);
    }

    public function testUserCannotRegistrationWithIncorrectCredentials()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => 'password',
            'password_confirmation' => 'worng_password'
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseMissing('users', [
            'name' => 'John Doe',
            'email' => 'john@doe.com'
        ]);
    }
}
