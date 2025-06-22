<?php
namespace App\Repositories;

use App\Contracts\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\CursorPaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(private Product $model) {}

     public function search(array $filters = []): CursorPaginator
    {
        $query = $this->model->query();

        if (!empty($filters['include'])) {
            $relations = explode(',', $filters['include']);
            $validRelations = ['categories', 'images'];
            $query->with(array_intersect($relations, $validRelations));
        }

        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->whereRaw("search_vector @@ to_tsquery('english', ?)", [str_replace(' ', ' & ', $search)]);
        });
        
        $query->when($filters['category'] ?? null, function ($q, $filterCategoryId) {
            $q->whereHas('categories', function ($categoryQuery) use ($filterCategoryId) {
                $categoryQuery->where('categories.id', $filterCategoryId);
            });
        });

        $query->when($filters['min_price'] ?? null, fn($q, $min) => $q->where('price', '>=', $min));
        $query->when($filters['max_price'] ?? null, fn($q, $max) => $q->where('price', '<=', $max));
        $query->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status));
        $query->when($filters['featured'] ?? null, fn($q, $featured) => $q->where('featured', filter_var($featured, FILTER_VALIDATE_BOOLEAN)));

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        if (in_array($sortBy, ['name', 'price', 'created_at'])) {
            $query->orderBy($sortBy, $sortDir);
        }

        return $query->cursorPaginate(15);
    }
    
    // --- CORRECTION HERE ---
    // The method signature now matches the interface exactly.
    public function find(int $id, array $relations = []): ?Model
    {
        $query = $this->model->query();

        // Whitelist and load the requested relations
        if (!empty($relations)) {
            $validRelations = ['categories', 'images'];
            $query->with(array_intersect($relations, $validRelations));
        }

        return $query->find($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $product = $this->find($id); // This will now work without error
        if ($product) {
            return $product->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $product = $this->find($id); // This will now work without error
        if ($product) {
            return $product->delete();
        }
        return false;
    }
}