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
                'price' => 650.00,
                'quantity' => 120,
                'unit' => 'scoop',
                'description' => 'Rich, velvety 70% dark Belgian cocoa ice cream.',
                'is_active' => true,
            ],
            [
                'name' => 'Dutch Chocolate Fudge Swirl',
                'flavor_category' => 'Chocolate',
                'price' => 700.00,
                'quantity' => 95,
                'unit' => 'scoop',
                'description' => 'Creamy milk chocolate folded with thick fudge ripples.',
                'is_active' => true,
            ],
            [
                'name' => 'Madagascar Vanilla Bean',
                'flavor_category' => 'Vanilla',
                'price' => 550.00,
                'quantity' => 150,
                'unit' => 'scoop',
                'description' => 'Traditional vanilla custard infused with real Bourbon vanilla caviar.',
                'is_active' => true,
            ],
            [
                'name' => 'French Vanilla Custard Tub',
                'flavor_category' => 'Vanilla',
                'price' => 1850.00,
                'quantity' => 40,
                'unit' => 'tub',
                'description' => 'Pre-packed 500ml gourmet French vanilla tub.',
                'is_active' => true,
            ],
            [
                'name' => 'Fresh Wild Strawberry',
                'flavor_category' => 'Strawberry',
                'price' => 600.00,
                'quantity' => 80,
                'unit' => 'scoop',
                'description' => 'Sweet Nuwara Eliya strawberries blended into rich dairy cream.',
                'is_active' => true,
            ],
            [
                'name' => 'Strawberry Shortcake Crunch',
                'flavor_category' => 'Strawberry',
                'price' => 750.00,
                'quantity' => 65,
                'unit' => 'scoop',
                'description' => 'Strawberry ice cream layered with biscuit crumbs and strawberry puree.',
                'is_active' => true,
            ],
            [
                'name' => 'Cool Mint Chocolate Crisp',
                'flavor_category' => 'Mint',
                'price' => 650.00,
                'quantity' => 70,
                'unit' => 'scoop',
                'description' => 'Refreshing peppermint cream loaded with bittersweet dark chocolate chips.',
                'is_active' => true,
            ],
            [
                'name' => 'Alphonso Mango Sorbet',
                'flavor_category' => 'Fruit',
                'price' => 600.00,
                'quantity' => 85,
                'unit' => 'scoop',
                'description' => 'Dairy-free, refreshing fruit sorbet made from sun-ripened tropical mangoes.',
                'is_active' => true,
            ],
            [
                'name' => 'Ceylon Cinnamon & Honey',
                'flavor_category' => 'Artisan',
                'price' => 750.00,
                'quantity' => 50,
                'unit' => 'scoop',
                'description' => 'Infused with pure organic Ceylon cinnamon quills and wild bee honey.',
                'is_active' => true,
            ],
            [
                'name' => 'Kithul Treacle & Roasted Cashew',
                'flavor_category' => 'Artisan',
                'price' => 850.00,
                'quantity' => 60,
                'unit' => 'scoop',
                'description' => 'Traditional Sri Lankan pure Kithul palm treacle with golden roasted cashews.',
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
