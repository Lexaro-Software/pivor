<?php

namespace Database\Factories;

use App\Modules\Clients\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'name' => fake()->company(),
            'trading_name' => fake()->optional()->company(),
            'registration_number' => fake()->optional()->numerify('########'),
            'vat_number' => fake()->optional()->numerify('VAT#########'),
            'type' => fake()->randomElement(['company', 'individual', 'organisation']),
            'status' => fake()->randomElement(['active', 'inactive', 'prospect', 'archived']),
            'email' => fake()->optional()->companyEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'website' => fake()->optional()->url(),
            'address_line_1' => fake()->optional()->streetAddress(),
            'city' => fake()->optional()->city(),
            'postcode' => fake()->optional()->postcode(),
            'country' => fake()->optional()->countryCode(),
            'industry' => fake()->optional()->randomElement(['Technology', 'Finance', 'Healthcare', 'Retail', 'Manufacturing']),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function prospect(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'prospect',
        ]);
    }
}
