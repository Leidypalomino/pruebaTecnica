<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // <-- ADD THIS LINE
use Illuminate\Support\Str;         // <-- ADD THIS LINE

class ApiKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::pluck('id');

        foreach ($users as $userId) {
            ApiKey::create([
                'user_id' => $userId,
                'key_hash' => Hash::make(Str::random(32)), // This line will now work
                'name' => 'API Key for User ' . $userId,
                'last_used_at' => now(),
                'expires_at' => now()->addYear(),
            ]);
        }
    }
}