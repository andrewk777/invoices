<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class InvoiceItemFactory extends Factory
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
            'description' => $this->faker->sentence(),
            'qty' => $this->faker->randomNumber(),
            'rate' => $this->faker->randomFloat(2),
            'tax' => $this->faker->randomFloat(2),
        ];
    }
}
