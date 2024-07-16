<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientUser;
use App\Models\CreditCard;
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

        $client = Client::factory()->create();
        $client->creditCards()->saveMany(CreditCard::factory(2)->create());
        $client->users()->saveMany(ClientUser::factory(2)->create());
        $client->defaultCreditCard()->associate($client->creditCards->first());
        $client->save();

        return [
            'hash' => $this->faker->uuid,
            'name' => $this->faker->name,
            'my_company_id' => MyCompany::inrandomOrder()->first()->id,
            'client_id' => $client->id,
            'tags' => implode(', ', $this->faker->words(3)),
            'currency' => $this->faker->randomElement(['CAD', 'USD']),
            'credit_card_id' => $this->faker->randomNumber(),
            'next_charge_date' => $this->faker->dateTime(),
            'due_in_days' => $this->faker->numberBetween(1, 30),
            'frequency_day' => $this->faker->numberBetween(1, 31),
            'frequency_month' => $this->faker->numberBetween(1, 12),
            'can_pay_with_cc' => $this->faker->boolean,
            'starting_date' => $this->faker->dateTime(),
            'expiration_date' => $this->faker->dateTime(),
            'charge_cc' => $this->faker->boolean,
            'email_invoice' => $this->faker->boolean,
            'email_template_id' => $this->faker->randomNumber('2'),
            'subtotal' => $subtotal,
            'taxes' => $taxes,
            'total' => $total,
        ];
    }
}
