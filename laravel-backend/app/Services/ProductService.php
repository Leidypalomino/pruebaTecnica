<?php
namespace App\Services;

use App\Contracts\ProductRepositoryInterface;
use App\Contracts\ProductServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductService implements ProductServiceInterface
{
    private const CACHE_TAG = 'products';

    public function __construct(private ProductRepositoryInterface $productRepository) {}

    public function searchProducts(array $filters): CursorPaginator
    {
        $cacheKey = 'products_list:' . http_build_query($filters);
        return Cache::tags([self::CACHE_TAG])->remember($cacheKey, 900, function () use ($filters) {
            return $this->productRepository->search($filters);
        });
    }

    // Updated method to handle relations and create a unique cache key
    public function getProductById(int $id, array $relations = []): ?Model
    {
        // Sort relations to ensure cache key consistency (e.g., "a,b" is same as "b,a")
        sort($relations);
        $relationsKey = implode(',', $relations);
        $cacheKey = "product:{$id}:include:{$relationsKey}";
        
        return Cache::tags([self::CACHE_TAG])->remember($cacheKey, 3600, function () use ($id, $relations) {
            return $this->productRepository->find($id, $relations);
        });
    }

    public function createProduct(array $data): Model
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        $product = $this->productRepository->create($data);
        Cache::tags([self::CACHE_TAG])->flush();
        return $product;
    }

    public function updateProduct(int $id, array $data): bool
    {
        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $updated = $this->productRepository->update($id, $data);

        if ($updated) {
            Cache::tags([self::CACHE_TAG])->flush();
        }
        
        return $updated;
    }

    public function deleteProduct(int $id): bool
    {
        $deleted = $this->productRepository->delete($id);
        
        if ($deleted) {
            Cache::tags([self::CACHE_TAG])->flush();
        }

        return $deleted;
    }
}