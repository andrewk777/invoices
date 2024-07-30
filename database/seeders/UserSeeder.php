<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Random\RandomException;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws RandomException
     */
    public function run(): void
    {
        $users = [
            'admin@email.com',
            'client-user@email.com'
        ];

        foreach ($users as $email) {
            User::firstOrCreate(
                ['email' => $email],
                [
                    'first_name' => $email === 'admin@email.com' ? 'Admin User' : 'Client User',
                    'password' => Hash::make('password'),
                    'system_access' => 1,
                    'hash' => Str::random(50),
                    'role' => $email === 'admin@email.com' ? 'admin' : 'client-user',
                    'last_login' => now(),
                ]
            );
        }
    }
}
