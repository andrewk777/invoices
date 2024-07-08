<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'client@email.com'],
            [
                'first_name' => 'Client',
                'last_name' => 'User',
                'password' => Hash::make('password'),
                'system_access' => true,
                'hash' => Str::random(50),
                'role' => 'supper-admin',
                'client_id' => null,
            ]
        );

        User::factory(3)->create();
    }
}
