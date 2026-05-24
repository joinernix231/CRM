<?php

namespace Tests\Unit;

use App\Models\Client;
use App\Models\Contact;
use App\Models\User;
use App\Repositories\ContactRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactPrimaryConstraintTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_one_primary_contact_per_client(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $repository = app(ContactRepository::class);

        $first = $repository->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'name' => 'Primary One',
            'email' => 'primary1@example.com',
            'phone' => '3001111111',
            'position' => 'Manager',
            'is_primary' => true,
        ]);

        $second = $repository->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'name' => 'Primary Two',
            'email' => 'primary2@example.com',
            'phone' => '3002222222',
            'position' => 'Sales',
            'is_primary' => true,
        ]);

        $this->assertFalse($first->fresh()->is_primary);
        $this->assertTrue($second->fresh()->is_primary);
        $this->assertSame(
            1,
            Contact::query()
                ->where('client_id', $client->id)
                ->where('is_primary', true)
                ->count()
        );
    }

    public function test_update_can_switch_primary_contact(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $repository = app(ContactRepository::class);

        $primary = $repository->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'name' => 'Primary',
            'email' => 'primary@example.com',
            'phone' => '3001111111',
            'position' => 'Manager',
            'is_primary' => true,
        ]);

        $secondary = $repository->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'name' => 'Secondary',
            'email' => 'secondary@example.com',
            'phone' => '3002222222',
            'position' => 'Sales',
            'is_primary' => false,
        ]);

        $repository->update(['is_primary' => true], $secondary->id);

        $this->assertFalse($primary->fresh()->is_primary);
        $this->assertTrue($secondary->fresh()->is_primary);
    }
}
