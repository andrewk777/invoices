<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CreditCard>
 */
class CreditCardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hash' => $this->faker->sha1,
            'client_id' => Client::factory()->create()->id,
            'cc_provider' => $this->faker->randomElement(['Stripe', 'Paypal', 'Braintree', 'Authorize']),
            'cc_number' => $this->faker->creditCardNumber(),
            'cc_exp_month' => $this->faker->numberBetween('1', '12'),
            'cc_exp_year' => $this->faker->numberBetween('2022', '2030'),
            'cc_provider_customer_id' => $this->faker->numberBetween('1', '20'),
            'cc_provider_card_id' => $this->faker->numberBetween('1', '20'),
            'cc_currencies' => $this->faker->randomElement(['CAD', 'USD']),
            'cc_type' => $this->faker->randomElement(['VISA', 'MASTER CARD', 'AMEX', 'Other'])
        ];
    }
}
