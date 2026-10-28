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
                'name' => 'Kasun Perera',
                'email' => 'kasun.p@gmail.com',
                'phone' => '+94 77 123 4567',
                'address' => 'No. 45, Galle Road, Colombo 03',
                'notes' => 'Prefers gluten-free waffle cones. VIP regular customer.',
            ],
            [
                'name' => 'Dinuka Jayasuriya',
                'email' => 'dinuka.j@gmail.com',
                'phone' => '+94 71 234 5678',
                'address' => '12/A, Peradeniya Road, Kandy',
                'notes' => 'Likes Belgian Dark Chocolate with extra roasted cashews.',
            ],
            [
                'name' => 'Thilini Fernando',
                'email' => 'thilini.f@yahoo.com',
                'phone' => '+94 76 345 6789',
                'address' => '78 Temple Road, Negombo',
                'notes' => 'Frequently orders 1L French Vanilla & Wild Strawberry family tubs.',
            ],
            [
                'name' => 'Sivakumar Tharmarajah',
                'email' => 'sivakumar.t@outlook.com',
                'phone' => '+94 75 456 7890',
                'address' => '22 Station Road, Jaffna',
                'notes' => 'Prefers dairy-free options (Alphonso Mango Sorbet).',
            ],
            [
                'name' => 'Fathima Riffai',
                'email' => 'fathima.riffai@gmail.com',
                'phone' => '+94 70 567 8901',
                'address' => '105 Marine Drive, Colombo 04',
                'notes' => 'Loves Kithul Treacle & Cashew artisan scoop.',
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
