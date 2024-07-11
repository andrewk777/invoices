<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubscriptionItem>
 */
class SubscriptionItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hash' => $this->faker->uuid,
            'description' => $this->faker->sentence,
            'qty' => $this->faker->randomNumber('1'),
            'rate' => $this->faker->randomFloat('2', '0', '500'),
            'tax' => $this->faker->randomFloat('2', '0', '40'),
        ];
    }
}
