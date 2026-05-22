<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public const DEMO_EMAIL = 'Joiner@email.com';

    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => self::DEMO_EMAIL],
            [
                'name' => 'Joiner Davila',
                'password' => Hash::make('secret'),
                'email_verified_at' => now(),
            ]
        );
    }
}
