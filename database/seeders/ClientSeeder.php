<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\CreditCard;
use App\Models\MyCompany;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::class::factory(5)->create()->each(function ($client) {
            $client->creditCards()->saveMany(CreditCard::factory(2)->create());
            $client->users()->saveMany(User::factory(2)->create());
            $client->defaultCreditCard()->associate($client->creditCards->first());
            $client->save();
        });
    }
}
