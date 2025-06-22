<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // INT PRIMARY KEY AUTO_INCREMENT
            $table->string('email', 255)->unique();
            $table->string('password_hash', 255);
            $table->string('name', 100)->nullable();
            $table->enum('role', ['admin', 'customer'])->default('customer'); // 
            $table->boolean('is_active')->default(true); // 
            $table->timestamp('created_at')->useCurrent(); // 
            // Laravel automáticamente añade updated_at con timestamps()
            // pero el spec no lo pide, así que lo omitimos.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};