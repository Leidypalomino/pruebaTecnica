<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $categories = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]; // IDs from CategorySeeder

        for ($i = 1; $i <= 50; $i++) {
            Product::create([
                'sku' => 'SKU' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'name' => 'Product ' . $i,
                'slug' => 'product-' . $i,
                'description' => 'Description for Product ' . $i,
                'price' => rand(10, 1000) + (rand(0, 99) / 100), // Random price between 10.00 and 1000.99
                'compare_price' => rand(1000, 2000) / 100, // Optional higher compare price
                'cost' => rand(5, 500) / 100, // Cost price
                'stock' => rand(0, 100),
                'low_stock_threshold' => 5,
                'weight' => rand(1, 50) / 100, // Weight in kg
                'status' => ['active', 'inactive', 'draft'][rand(0, 2)],
                'featured' => rand(0, 1) === 1,
                'views_count' => rand(0, 1000), // Add views for analytics
            ]);
        }
    }
}