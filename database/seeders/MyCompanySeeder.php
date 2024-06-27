<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\MyCompany;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MyCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MyCompany::class::factory()->count(10)->create()->each(function ($myCompany) {
//            $myCompany->clients()->saveMany(Client::factory()->count(5)->create());
        });
    }
}
