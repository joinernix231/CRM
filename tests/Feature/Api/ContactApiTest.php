<?php

namespace Tests\Feature\Api;

use App\Models\Client;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\AuthenticatesApiUsers;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use AuthenticatesApiUsers;
    use RefreshDatabase;

    public function test_user_can_list_contacts_for_owned_client(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);

        Contact::factory()->count(2)->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
        ]);

        $response = $this->actingAsApi($user)->getJson("/api/clients/{$client->id}/contacts");

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_user_cannot_list_contacts_for_foreign_client(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $owner->id]);

        $this->actingAsApi($intruder)
            ->getJson("/api/clients/{$client->id}/contacts")
            ->assertStatus(400)
            ->assertJsonPath('success', false);
    }

    public function test_user_can_create_contact_for_client(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);

        $payload = [
            'name' => 'Jane Cooper',
            'email' => 'jane@acme.test',
            'phone' => '3001112233',
            'position' => 'Manager',
            'is_primary' => true,
        ];

        $response = $this->actingAsApi($user)
            ->postJson("/api/clients/{$client->id}/contacts", $payload);

        $response
            ->assertCreated()
            ->assertJsonPath('data.name', 'Jane Cooper')
            ->assertJsonPath('data.is_primary', true);

        $this->assertDatabaseHas('contacts', [
            'client_id' => $client->id,
            'email' => 'jane@acme.test',
            'is_primary' => true,
        ]);
    }

    public function test_create_contact_validation_errors(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);

        $this->actingAsApi($user)
            ->postJson("/api/clients/{$client->id}/contacts", [])
            ->assertStatus(400)
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['data' => ['name', 'email', 'phone', 'position']]);
    }

    public function test_only_latest_primary_contact_remains_primary_via_api(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);

        $firstResponse = $this->actingAsApi($user)->postJson("/api/clients/{$client->id}/contacts", [
            'name' => 'Primary One',
            'email' => 'primary1@example.com',
            'phone' => '3001111111',
            'position' => 'Manager',
            'is_primary' => true,
        ]);

        $firstResponse->assertCreated()->assertJsonPath('data.is_primary', true);
        $firstId = $firstResponse->json('data.id');

        $secondResponse = $this->actingAsApi($user)->postJson("/api/clients/{$client->id}/contacts", [
            'name' => 'Primary Two',
            'email' => 'primary2@example.com',
            'phone' => '3002222222',
            'position' => 'Sales',
            'is_primary' => true,
        ]);

        $secondResponse->assertCreated()->assertJsonPath('data.is_primary', true);
        $secondId = $secondResponse->json('data.id');

        $this->assertDatabaseHas('contacts', [
            'id' => $firstId,
            'is_primary' => false,
        ]);
        $this->assertDatabaseHas('contacts', [
            'id' => $secondId,
            'is_primary' => true,
        ]);
        $this->assertSame(
            1,
            Contact::query()
                ->where('client_id', $client->id)
                ->where('is_primary', true)
                ->count()
        );
    }

    public function test_user_can_delete_contact(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $contact = Contact::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
        ]);

        $this->actingAsApi($user)
            ->deleteJson("/api/clients/{$client->id}/contacts/{$contact->id}")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }
}
