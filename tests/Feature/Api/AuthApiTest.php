<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\AuthenticatesApiUsers;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use AuthenticatesApiUsers;
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'seller@example.com',
            'password' => Hash::make('secret'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'seller@example.com',
            'password' => 'secret',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['access_token', 'user' => ['id', 'name', 'email']]])
            ->assertJsonPath('data.user.email', $user->email);

        $this->assertNotEmpty($response->json('data.access_token'));
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'seller@example.com',
            'password' => Hash::make('secret'),
        ]);

        $this->postJson('/api/login', [
            'email' => 'seller@example.com',
            'password' => 'wrong-password',
        ])
            ->assertStatus(400)
            ->assertJsonPath('success', false);
    }

    public function test_authenticated_user_can_fetch_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAsApi($user)
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_protected_routes_require_bearer_token(): void
    {
        $this->getJson('/api/clients')
            ->assertUnauthorized()
            ->assertJsonPath('success', false);
    }
}
