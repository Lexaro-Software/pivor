<?php

namespace Database\Factories;

use App\Models\User;
use App\Modules\Clients\Models\Client;
use App\Modules\Communications\Models\Communication;
use App\Modules\Contacts\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CommunicationFactory extends Factory
{
    protected $model = Communication::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'client_id' => Client::factory(),
            'contact_id' => null,
            'type' => fake()->randomElement(['email', 'phone', 'meeting', 'note', 'task']),
            'direction' => fake()->randomElement(['inbound', 'outbound', 'internal']),
            'subject' => fake()->sentence(),
            'content' => fake()->paragraphs(2, true),
            'status' => fake()->randomElement(['pending', 'in_progress', 'completed', 'cancelled']),
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent']),
            'due_at' => fake()->optional()->dateTimeBetween('now', '+1 month'),
            'completed_at' => null,
            'created_by' => User::factory(),
        ];
    }
}
