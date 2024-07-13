<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientUser;
use App\Models\CreditCard;
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
        $subtotal = $this->faker->randomFloat(2, 0, 500.99);
        $taxes = $this->faker->randomFloat(2, 0, 50.00);
        $total = $subtotal + $taxes;
        $total_paid = $this->faker->randomFloat(2, 0, $total);
        $balance = $total - $total_paid;

        $client = Client::factory()->create();
        $client->creditCards()->saveMany(CreditCard::factory(2)->create());
        $client->users()->saveMany(ClientUser::factory(2)->create());
        $client->defaultCreditCard()->associate($client->creditCards->first());
        $client->save();

        return [
            'hash' => $this->faker->uuid(),
            'my_company_id' => MyCompany::inRandomOrder()->first()->id,
            'client_id' => $client->id,
            'invoice_num' => $this->faker->unique()->numberBetween(1000, 9999),
            'invoice_type' => $this->faker->randomElement(['standard', 'credit_memo']),
            'status' => $this->faker->randomElement(['draft', 'approved', 'sent', 'partially_paid', 'paid']),
            'currency' => $this->faker->randomElement(['USD', 'CAD']),
            'invoice_date' => $this->faker->date(),
            'invoice_due' => $this->faker->date(),
            'na' => $this->faker->boolean(),
            'can_pay_with_cc' => $this->faker->boolean(),

            'subtotal' => $subtotal,
            'taxes' => $taxes,
            'total' => $total,
            'total_paid' => $total_paid,
            'balance' => $balance,
        ];
    }
}
