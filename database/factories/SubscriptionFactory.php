<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\MyCompany;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat('2', '0', '500');
        $taxes = $this->faker->randomFloat('2', '0', '40');
        $total = $subtotal + $taxes;

        return [
            'hash' => $this->faker->uuid,
            'name' => $this->faker->name,
            'my_company_id' => MyCompany::inrandomOrder()->first()->id,
            'client_id' => Client::InRandomOrder()->first()->id,
            'tags' => $this->faker->words,
            'currency' => $this->faker->randomElement(['CAD', 'USD']),
            'credit_card_id' => $this->faker->randomNumber(),
            'next_charge_date' => $this->faker->dateTime(),
            'due_in_days' => $this->faker->randomNumber('2', '0'),
            'frequency_day' => $this->faker->randomNumber('2', '0'),
            'frequency_month' => $this->faker->randomNumber('2', '0'),
            'can_pay_with_cc' => $this->faker->boolean,
            'starting_date' => $this->faker->dateTime('now'),
            'expiration_date' => $this->faker->dateTime('now + 5 months'),
            'charge_cc' => $this->faker->boolean,
            'email_invoice' => $this->faker->boolean,
            'email_template_id' => $this->faker->randomNumber('2'),
            'subtotal' => $subtotal,
            'taxes' => $taxes,
            'total' => $total,
        ];
    }
}
