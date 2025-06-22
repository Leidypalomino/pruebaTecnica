<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @OA\Schema(
 * schema="ProductImage",
 * type="object",
 * title="Product Image Schema",
 * @OA\Property(property="id", type="integer", readOnly=true),
 * @OA\Property(property="url", type="string", format="uri", example="http://example.com/images/product.jpg"),
 * @OA\Property(property="alt_text", type="string", example="Imagen de una laptop"),
 * @OA\Property(property="is_primary", type="boolean", example=true)
 * )
 */



class ProductImage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'url',
        'alt_text',
        'position',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}