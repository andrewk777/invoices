<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\CreditCard;
use App\Models\MyCompany;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::class::factory(10)->create()->each(function ($client) {
            $client->creditCards()->saveMany(CreditCard::factory(2)->create());
            $client->myCompany()->associate(MyCompany::factory()->create());
            $client->save();
        });
    }
}
