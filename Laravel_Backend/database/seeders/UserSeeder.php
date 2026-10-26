<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates default pre-configured accounts for each required assessment role.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Store Owner',
                'email' => 'owner@delivergate.com',
                'password' => Hash::make('Password123!'),
                'role' => UserRole::OWNER,
            ],
            [
                'name' => 'Store Manager',
                'email' => 'manager@delivergate.com',
                'password' => Hash::make('Password123!'),
                'role' => UserRole::MANAGER,
            ],
            [
                'name' => 'Store Cashier',
                'email' => 'cashier@delivergate.com',
                'password' => Hash::make('Password123!'),
                'role' => UserRole::CASHIER,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
