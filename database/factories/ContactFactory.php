<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'client_id' => Client::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('3#########'),
            'position' => fake()->jobTitle(),
            'is_primary' => false,
        ];
    }

    public function primary(): static
    {
        return $this->state(fn () => ['is_primary' => true]);
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Contact $contact) {
            if (! $contact->user_id && $contact->client_id) {
                $contact->user_id = Client::query()
                    ->whereKey($contact->client_id)
                    ->value('user_id');
            }
        });
    }
}
