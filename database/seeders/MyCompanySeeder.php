<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\MyCompany;
use App\Repositories\Base\BaseRepository;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MyCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $myCompanies = [
            [
                'name' => 'SWS eMarketing Inc',
                'hash' => BaseRepository::randomCharacters(50, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'),
            ],

            [
                'name' => 'OAD Soft',
                'hash' => BaseRepository::randomCharacters(50, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'),
            ],
        ];

        foreach ($myCompanies as $myCompany) {
            MyCompany::create($myCompany);
        }
    }
}
