<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductCategorySeeder extends Seeder
{
    public function run()
    {
        $products = Product::all();
        $categories = Category::all()->pluck('id')->toArray();

        foreach ($products as $product) {
            $randomCategories = array_rand(array_flip($categories), rand(1, 3)); // 1-3 categories per product
            if (!is_array($randomCategories)) {
                $randomCategories = [$randomCategories];
            }
            $product->categories()->attach($randomCategories);
        }
    }
}