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
                'email' => 'info@swsemarketing.com',
                'mobile' => '416-477-4763',
                'address' => '08 - 1110 Finch Ave. West North York, ON, M3J 2T2',
                'country' => 'Canada',
                'logo' => '/images/sws-emarketing.png',

            ],

            [
                'name' => 'OAD Soft',
                'hash' => BaseRepository::randomCharacters(50, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'),
                'email' => 'info@oadsoft.com',
                'mobile' => '416-477-4763',
                'address' => '203-125 Commerce Valley Dr. W., Thornhill, ON, L3T 7W4 ',
                'country' => 'Canada',
                'logo' => '/images/oad-soft.png',
            ],
        ];

        foreach ($myCompanies as $myCompany) {
            MyCompany::create($myCompany);
        }
    }
}
