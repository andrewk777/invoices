<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientUser;
use App\Models\CreditCard;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Seeder;
use Random\RandomException;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws RandomException
     */
    public function run(): void
    {
        Client::class::factory(10)->create()->each(function ($client) {
            $client->creditCards()->saveMany(CreditCard::factory(2)->create());
            $client->invoices()->saveMany(Invoice::factory(2)->create([
                'my_company_id' => random_int(1, 2),
            ]));
            $client->users()->saveMany(User::factory(2)->create([
                'role' => 'client-user',
                'system_access' => 1,
            ]));
            $client->defaultCreditCard()->associate($client->creditCards->first());
            $client->save();
        });
    }
}
