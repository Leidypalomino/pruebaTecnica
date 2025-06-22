<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->integer('low_stock_threshold')->default(5); // 
            $table->decimal('weight', 8, 3)->nullable();
            $table->enum('status', ['active', 'inactive', 'draft'])->default('active'); // 
            $table->boolean('featured')->default(false);
            $table->timestamps(); // Gestiona created_at y updated_at automáticamente

            $table->index(['status', 'featured'], 'idx_status_featured'); // 
        });

        // FULLTEXT index para PostgreSQL se crea con un índice GIN y to_tsvector
        DB::statement('ALTER TABLE products ADD COLUMN search_vector tsvector');
        DB::statement("UPDATE products SET search_vector = to_tsvector('english', name || ' ' || coalesce(description, ''))");
        DB::statement('CREATE INDEX idx_search ON products USING GIN(search_vector)'); // 
        DB::statement(<<<'SQL'
            CREATE TRIGGER ts_search_vector_update BEFORE INSERT OR UPDATE
            ON products FOR EACH ROW EXECUTE PROCEDURE
            tsvector_update_trigger(search_vector, 'pg_catalog.english', name, description);
        SQL);
    }

    public function down(): void
    {
        // El trigger debe eliminarse primero
        DB::statement('DROP TRIGGER IF EXISTS ts_search_vector_update ON products');
        Schema::dropIfExists('products');
    }
};