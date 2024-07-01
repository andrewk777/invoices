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
            'company_id' => MyCompany::inRandomOrder()->first()->id,
            'client_id' => Client::factory()->create()->id,
            'invoice_num' => $this->faker->randomNumber(),
            'invoice_type' => $this->faker->randomElement(['standard', 'credit_memo']),
            'status' => $this->faker->randomElement(['draft', 'approved', 'sent', 'partially_paid', 'paid']),
            'currency' => $this->faker->randomElement(['USD', 'CAD']),
            'invoice_date' => $this->faker->date(),
            'invoice_due' => $this->faker->date(),
            //'na' => null,
            'can_pay_with_cc' => $this->faker->boolean(),
            'subtotal' => $this->faker->randomFloat(2, 0, 999999.99),
            'taxes' => $this->faker->randomFloat(2, 0, 999999.99),
            'total' => $this->faker->randomFloat(2, 0, 999999.99),
            'total_paid' => $this->faker->randomFloat(2, 0, 999999.99),
            'balance' => $this->faker->randomFloat(2, 0, 999999.99),
        ];
    }
}
