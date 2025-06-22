<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductImageSeeder extends Seeder
{
    public function run()
    {
        $products = Product::all();

        foreach ($products as $product) {
            for ($i = 1; $i <= rand(1, 3); $i++) {
                $product->images()->create([
                    'url' => 'images/product-' . $product->id . '-' . $i . '.jpg',
                    'alt_text' => 'Image of ' . $product->name . ' - ' . $i,
                    'position' => $i,
                    'is_primary' => $i === 1,
                ]);
            }
        }
    }
}