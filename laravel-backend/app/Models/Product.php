<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 * schema="Product",
 * type="object",
 * title="Product Schema",
 * required={"name", "sku", "price", "stock"},
 * @OA\Property(property="id", type="integer", readOnly=true),
 * @OA\Property(property="sku", type="string", description="Stock Keeping Unit único", example="LP-PRO-01"),
 * @OA\Property(property="name", type="string", description="Nombre del producto", example="Laptop Pro M3"),
 * @OA\Property(property="slug", type="string", readOnly=true, example="laptop-pro-m3"),
 * @OA\Property(property="description", type="string", example="Una laptop muy potente para profesionales."),
 * @OA\Property(property="price", type="number", format="float", example=1599.99),
 * @OA\Property(property="stock", type="integer", example=50),
 * @OA\Property(property="status", type="string", enum={"active", "inactive", "draft"}, example="active"),
 * @OA\Property(property="featured", type="boolean", example=true),
 * @OA\Property(property="created_at", type="string", format="date-time", readOnly=true),
 * @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true),
 * @OA\Property(property="categories", type="array", @OA\Items(ref="#/components/schemas/Category")),
 * @OA\Property(property="images", type="array", @OA\Items(ref="#/components/schemas/ProductImage"))
 * )
 */



class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'slug',
        'description',
        'price',
        'compare_price',
        'cost',
        'stock',
        'low_stock_threshold',
        'weight',
        'status',
        'featured',
        'views_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'weight' => 'decimal:3',
        'featured' => 'boolean',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
}