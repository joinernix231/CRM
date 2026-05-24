<?php

namespace Tests\Feature\Api;

use App\Models\Client;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\AuthenticatesApiUsers;
use Tests\TestCase;

class ClientApiTest extends TestCase
{
    use AuthenticatesApiUsers;
    use RefreshDatabase;

    public function test_user_can_list_only_their_clients(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Client::factory()->count(2)->create(['user_id' => $user->id]);
        Client::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAsApi($user)->getJson('/api/clients');

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_user_can_filter_clients_by_status(): void
    {
        $user = User::factory()->create();

        Client::factory()->active()->create(['user_id' => $user->id, 'name' => 'Active Co']);
        Client::factory()->prospect()->create(['user_id' => $user->id, 'name' => 'Prospect Co']);

        $response = $this->actingAsApi($user)->getJson('/api/clients?status=active');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertSame('active', $response->json('data.0.status'));
    }

    public function test_user_can_create_client(): void
    {
        $user = User::factory()->create();

        $payload = [
            'name' => 'Acme Corp',
            'email' => 'acme@example.com',
            'phone' => '3001234567',
            'company' => 'Acme',
        ];

        $response = $this->actingAsApi($user)->postJson('/api/clients', $payload);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Acme Corp');

        $this->assertDatabaseHas('clients', [
            'user_id' => $user->id,
            'email' => 'acme@example.com',
        ]);
    }

    public function test_create_client_validation_errors(): void
    {
        $user = User::factory()->create();

        $this->actingAsApi($user)
            ->postJson('/api/clients', ['name' => ''])
            ->assertStatus(400)
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['data' => ['email', 'phone', 'company']]);
    }

    public function test_user_cannot_access_another_users_client(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $owner->id]);

        $this->actingAsApi($intruder)
            ->getJson("/api/clients/{$client->id}")
            ->assertStatus(400)
            ->assertJsonPath('success', false);
    }

    public function test_user_can_update_and_delete_client(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create([
            'user_id' => $user->id,
            'name' => 'Old Name',
            'status' => 'prospect',
        ]);

        $this->actingAsApi($user)
            ->putJson("/api/clients/{$client->id}", [
                'name' => 'New Name',
                'status' => 'active',
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'New Name')
            ->assertJsonPath('data.status', 'active');

        $this->actingAsApi($user)
            ->deleteJson("/api/clients/{$client->id}")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    public function test_user_can_download_client_detail_pdf(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);

        Contact::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'is_primary' => true,
        ]);

        $response = $this->actingAsApi($user)->get("/api/clients/{$client->id}/pdf");

        $response
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_user_cannot_download_pdf_for_foreign_client(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $owner->id]);

        $this->actingAsApi($intruder)
            ->get("/api/clients/{$client->id}/pdf")
            ->assertStatus(400)
            ->assertJsonPath('success', false);
    }
}
