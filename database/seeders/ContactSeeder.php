<?php

namespace Database\Seeders;

use App\Models\Client;
use Database\Seeders\Concerns\SeedsContactsForClient;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    use SeedsContactsForClient;

    public function run(): void
    {
        Client::query()
            ->whereDoesntHave('contacts')
            ->each(function (Client $client) {
                $this->seedContactsForClient($client, fake()->numberBetween(2, 4));
            });
    }
}
