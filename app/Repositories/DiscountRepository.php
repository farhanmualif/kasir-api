<?php

namespace App\Repositories;

use App\Models\Discounts;

interface DiscountRepository
{
    public function create(array $data): Discounts;
    public function deleteById(int $id): bool;
    public function getAll(): array;
    public function getById(int $id): ?Discounts;
    public function updateById(int $id, array $data): Discounts;
    public function findById(int $id): bool;
    public function getByName(string $name): array;
    public function getByStoreId(int $storeId): array;
    public function getActiveDiscounts(int $storeId): array;
}
