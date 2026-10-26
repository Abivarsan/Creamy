<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Populates initial ice cream flavors and stock across categories.
     */
    public function run(): void
    {
        $items = [
            [
                'name' => 'Belgian Dark Chocolate Supreme',
                'flavor_category' => 'Chocolate',
                'price' => 4.50,
                'quantity' => 120,
                'unit' => 'scoop',
                'description' => 'Rich, velvety 70% dark Belgian cocoa ice cream.',
                'is_active' => true,
            ],
            [
                'name' => 'Dutch Chocolate Fudge Swirl',
                'flavor_category' => 'Chocolate',
                'price' => 4.75,
                'quantity' => 95,
                'unit' => 'scoop',
                'description' => 'Creamy milk chocolate folded with thick fudge ripples.',
                'is_active' => true,
            ],
            [
                'name' => 'Madagascar Vanilla Bean',
                'flavor_category' => 'Vanilla',
                'price' => 3.99,
                'quantity' => 150,
                'unit' => 'scoop',
                'description' => 'Traditional vanilla custard infused with real Bourbon vanilla caviar.',
                'is_active' => true,
            ],
            [
                'name' => 'French Vanilla Custard Tub',
                'flavor_category' => 'Vanilla',
                'price' => 12.50,
                'quantity' => 40,
                'unit' => 'tub',
                'description' => 'Pre-packed 500ml gourmet French vanilla tub.',
                'is_active' => true,
            ],
            [
                'name' => 'Fresh Wild Strawberry',
                'flavor_category' => 'Strawberry',
                'price' => 4.25,
                'quantity' => 80,
                'unit' => 'scoop',
                'description' => 'Sweet summer strawberries blended into rich organic dairy cream.',
                'is_active' => true,
            ],
            [
                'name' => 'Strawberry Shortcake Crunch',
                'flavor_category' => 'Strawberry',
                'price' => 5.00,
                'quantity' => 65,
                'unit' => 'scoop',
                'description' => 'Strawberry ice cream layered with biscuit crumbs and strawberry puree.',
                'is_active' => true,
            ],
            [
                'name' => 'Cool Mint Chocolate Crisp',
                'flavor_category' => 'Mint',
                'price' => 4.50,
                'quantity' => 70,
                'unit' => 'scoop',
                'description' => 'Refreshing peppermint cream loaded with bittersweet dark chocolate chips.',
                'is_active' => true,
            ],
            [
                'name' => 'Alphonso Mango Sorbet',
                'flavor_category' => 'Fruit',
                'price' => 4.20,
                'quantity' => 85,
                'unit' => 'scoop',
                'description' => 'Dairy-free, refreshing fruit sorbet made from sun-ripened Alphonso mangoes.',
                'is_active' => true,
            ],
        ];

        foreach ($items as $itemData) {
            Item::updateOrCreate(
                ['name' => $itemData['name']],
                $itemData
            );
        }
    }
}
