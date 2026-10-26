<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Populates initial customer records.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Alice Johnson',
                'email' => 'alice.j@example.com',
                'phone' => '+1-555-0101',
                'address' => '742 Evergreen Terrace, Springfield',
                'notes' => 'Prefers gluten-free cones. VIP regular customer.',
            ],
            [
                'name' => 'Bob Smith',
                'email' => 'bob.smith@example.com',
                'phone' => '+1-555-0102',
                'address' => '12 Grimmauld Place, London',
                'notes' => 'Likes Belgian Dark Chocolate with extra fudge.',
            ],
            [
                'name' => 'Charlie Brown',
                'email' => 'charlie.b@example.com',
                'phone' => '+1-555-0103',
                'address' => '221B Baker Street, Marylebone',
                'notes' => 'Frequently orders 500ml French Vanilla tubs for family.',
            ],
            [
                'name' => 'Diana Prince',
                'email' => 'diana.p@example.com',
                'phone' => '+1-555-0104',
                'address' => '100 Gateway Blvd, Metropolis',
                'notes' => 'Prefers dairy-free options (Alphonso Mango Sorbet).',
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::updateOrCreate(
                ['phone' => $customerData['phone']],
                $customerData
            );
        }
    }
}
