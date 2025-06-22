<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AuditLog;

class AuditLogSeeder extends Seeder
{
    public function run()
    {
        $actions = ['product_created', 'category_updated', 'user_logged_in'];
        $users = [1, 2, 3];

        for ($i = 1; $i <= 10; $i++) {
            AuditLog::create([
                'user_id' => $users[array_rand($users)],
                'action' => $actions[array_rand($actions)],
                'entity_type' => ['product', 'category', 'user'][array_rand([0, 1, 2])],
                'entity_id' => rand(1, 50),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Seeder/1.0',
            ]);
        }
    }
}