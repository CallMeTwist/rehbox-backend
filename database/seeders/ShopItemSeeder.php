<?php

namespace Database\Seeders;

use App\Models\ShopItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShopItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['name' => 'ReHboX Water Bottle', 'category' => 'hydration', 'coin_cost' => 50,  'cash_price' => 3500,  'stock' => 100],
            ['name' => 'Exercise Mat',         'category' => 'equipment', 'coin_cost' => 150, 'cash_price' => 8500,  'stock' => 50],
            ['name' => 'Resistance Band Set', 'category' => 'equipment', 'coin_cost' => 120, 'cash_price' => 6500,  'stock' => 75],
            ['name' => 'Foam Roller',          'category' => 'recovery',  'coin_cost' => 100, 'cash_price' => 7000,  'stock' => 40],
            ['name' => 'Therapy Ice Pack',     'category' => 'recovery',  'coin_cost' => 40,  'cash_price' => 2500,  'stock' => 200],
            ['name' => 'Topical Analgesic Gel','category' => 'recovery',  'coin_cost' => 30,  'cash_price' => 2000,  'stock' => 150],
            ['name' => 'ReHboX T-Shirt',       'category' => 'apparel',   'coin_cost' => 80,  'cash_price' => 5000,  'stock' => 60],
            ['name' => '10% Shop Discount',    'category' => 'equipment', 'coin_cost' => 20,  'cash_price' => null,  'stock' => -1],
        ];

        foreach ($items as $item) {
            ShopItem::create(array_merge($item, ['is_active' => true]));
        }
    }
}
