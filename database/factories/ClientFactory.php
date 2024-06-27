<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hash' => $this->faker->unique()->regexify('[A-Za-z0-9]{50}'),
            'my_company_id' => $this->faker->numberBetween(1, 10),
            'company_name' => $this->faker->company,
            'company_address' => $this->faker->address,
            'company_phone' => $this->faker->phoneNumber,
            'company_email' => $this->faker->unique()->safeEmail,
            'main_contact_first_name' => $this->faker->firstName,
            'main_contact_last_name' => $this->faker->lastName,
            'main_contact_phone' => $this->faker->phoneNumber,
            'main_contact_email' => $this->faker->unique()->safeEmail,
            'ap_first_name' => $this->faker->firstName,
            'ap_last_name' => $this->faker->lastName,
            'ap_phone' => $this->faker->phoneNumber,
            'ap_email' => $this->faker->unique()->safeEmail,
            'notes' => $this->faker->text,
        ];
    }
}
