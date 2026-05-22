<?php

namespace Database\Seeders\Concerns;

use App\Models\Client;
use App\Models\Contact;

trait SeedsContactsForClient
{
    protected function seedContactsForClient(Client $client, int $count): void
    {
        Contact::factory()
            ->primary()
            ->create([
                'client_id' => $client->id,
                'user_id' => $client->user_id,
            ]);

        if ($count > 1) {
            Contact::factory()
                ->count($count - 1)
                ->create([
                    'client_id' => $client->id,
                    'user_id' => $client->user_id,
                ]);
        }
    }
}
