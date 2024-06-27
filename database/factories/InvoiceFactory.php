<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\MyCompany;
use Faker\Provider\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hash' => $this->faker->uuid(),
            'company_id' => MyCompany::factory()->create()->id,
            'client_id' => Client::factory()->create()->id,
            'invoice_num' => $this->faker->randomNumber(),
            'invoice_type' => $this->faker->word(),
            'status' => $this->faker->word(),
            'currency' => $this->faker->currencyCode(),
            'invoice_date' => $this->faker->date(),
            'invoice_due' => $this->faker->date(),
            'na' => $this->faker->word(),
            'can_pay_with_cc' => $this->faker->boolean(),
            'subtotal' => $this->faker->randomFloat(2),
            'taxes' => $this->faker->randomFloat(2),
            'total' => $this->faker->randomFloat(2),
            'total_paid' => $this->faker->randomFloat(2),
            'balance' => $this->faker->randomFloat(2),
        ];
    }
}
