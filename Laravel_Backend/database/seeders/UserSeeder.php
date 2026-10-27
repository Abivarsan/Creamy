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
                'email' => 'owner@creamy.com',
                'password' => Hash::make('Owner@123'),
                'role' => UserRole::OWNER,
            ],
            [
                'name' => 'Store Manager',
                'email' => 'manager1@creamy.com',
                'password' => Hash::make('Manager1@123'),
                'role' => UserRole::MANAGER,
            ],
            [
                'name' => 'Store Cashier',
                'email' => 'cashier1@creamy.com',
                'password' => Hash::make('cashier1@123'),
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
