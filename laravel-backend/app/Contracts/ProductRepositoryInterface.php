<?php
namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\CursorPaginator;

interface ProductRepositoryInterface
{
    // Updated method signature to accept relations
    public function find(int $id, array $relations = []): ?Model;

    public function create(array $data): Model;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function search(array $filters): CursorPaginator;
}