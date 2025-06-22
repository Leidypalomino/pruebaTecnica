<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Electronics', 'parent_id' => null],
            ['name' => 'Smartphones', 'parent_id' => 1],
            ['name' => 'Laptops', 'parent_id' => 1],
            ['name' => 'Clothing', 'parent_id' => null],
            ['name' => 'Men\'s Clothing', 'parent_id' => 4],
            ['name' => 'Women\'s Clothing', 'parent_id' => 4],
            ['name' => 'Accessories', 'parent_id' => null],
            ['name' => 'Watches', 'parent_id' => 7],
            ['name' => 'Jewelry', 'parent_id' => 7],
            ['name' => 'Books', 'parent_id' => null],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'parent_id' => $category['parent_id'],
            ]);
        }
    }
}