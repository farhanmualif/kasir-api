<?php


namespace App\Services;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;

interface CategoryService
{
    public function create(CategoryStoreRequest $data);
    public function findById(int $data);
    public function findByUuid(string $data);
    public function getById(int $data);
    public function getAll();
    public function getByUuid(string $data);
    public function getByName(string $data);
    public function updateById(int $id, CategoryStoreRequest $data);
    public function updateByUuid(string $uuid, CategoryStoreRequest $data);
    public function updateByProductUuid(string $productUuid, CategoryUpdateRequest $data);
    public function deleteById(int $data);
    public function deleteByUuid(string $data);
}
