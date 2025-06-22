<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the CategorySeeder (already created with 10 categories)
        $this->call(CategorySeeder::class);

        // Call the UserSeeder (to create multiple users including the admin)
        $this->call(UserSeeder::class);

        // Call the ProductSeeder (to create 50 products)
        $this->call(ProductSeeder::class);

        // Call seeders for other tables
        $this->call(ProductCategorySeeder::class);
        $this->call(ProductImageSeeder::class);
        $this->call(ApiKeySeeder::class);
        $this->call(AuditLogSeeder::class);
    }
}