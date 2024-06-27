<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoicePayment>
 */
class InvoicePaymentFactory extends Factory
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
            'invoice_id' => Invoice::factory()->create()->id,
            'amount' => $this->faker->randomFloat(2),
            'date' => $this->faker->dateTime(),
            'type' => $this->faker->randomElement(['cash', 'check', 'credit_card']),
            'note' => $this->faker->sentence(),
        ];
    }
}
