<?php


namespace App\Repositories;

interface ProductRepository
{
    public function create(array $data);
    public function addCategoriesToProduct(string $productUuid, array $categoriesId);
    public function deleteCategoriesInProduct(string $productUuid, array $categoriesId);
    public function findById(int $id);
    public function findByUuid(string $uuid);
    public function findByBarcode(string $barcode);
    public function findByStoreId(int $storeId);
    public function getAll();
    public function getById(int $id);
    public function getByUuid(string $uuid);
    public function getByName(string $name);
    public function getByBarcode(string $barcode);
    public function getByCategory(string $category);
    public function updateById(int $id, array $data);
    public function updateByUuid(string $uuid, array $data);
    public function updateByBarcode(string $barcode, array $data);
    public function deleteById(int $id);
    public function deleteByUuid(string $uuid);
    public function deleteByBarcode(string $barcode);
}
