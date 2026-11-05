<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->e164PhoneNumber(),
            'company' => fake()->optional()->company(),
            'message' => fake()->optional()->sentence(12),
            'status' => fake()->randomElement(Lead::STATUSES),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
