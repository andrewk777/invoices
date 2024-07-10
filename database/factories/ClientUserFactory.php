<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientUser>
 */
class ClientUserFactory extends Factory
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
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password,
            'last_login' => $this->faker->dateTime,
            'system_access' => $this->faker->boolean,
        ];
    }
}
