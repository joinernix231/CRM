<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('email', UserSeeder::DEMO_EMAIL)->first();

        if (! $user) {
            return;
        }

        Client::factory()
            ->count(10)
            ->create(['user_id' => $user->id]);
    }
}
