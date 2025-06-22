<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * @OA\Schema(
 * schema="Category",
 * type="object",
 * title="Category Schema",
 * @OA\Property(property="id", type="integer", readOnly=true),
 * @OA\Property(property="name", type="string", example="Laptops"),
 * @OA\Property(property="slug", type="string", example="laptops"),
 * @OA\Property(property="parent_id", type="integer", nullable=true, example=1)
 * )
 */


class Category extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_categories');
    }
}