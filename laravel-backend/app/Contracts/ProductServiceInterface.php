<?php
namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\CursorPaginator;

interface ProductServiceInterface
{
    // Updated method signature to accept relations
    public function getProductById(int $id, array $relations = []): ?Model;

    public function createProduct(array $data): Model;
    public function updateProduct(int $id, array $data): bool;
    public function deleteProduct(int $id): bool;

    // Added the search method to the interface for consistency
    public function searchProducts(array $filters): CursorPaginator;
}