<?php

namespace Database\Factories;

use App\Modules\Clients\Models\Client;
use App\Modules\Contacts\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'client_id' => Client::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'mobile' => fake()->optional()->phoneNumber(),
            'job_title' => fake()->optional()->jobTitle(),
            'department' => fake()->optional()->randomElement(['Sales', 'Marketing', 'Finance', 'Operations', 'IT']),
            'is_primary_contact' => false,
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary_contact' => true,
        ]);
    }
}
