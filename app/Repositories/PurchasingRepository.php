<?php
namespace App\Repositories;
interface PurchasingRepository
{
    public function findById(int $id);
    public function findByNoPurchasing(string $noPurchasing);
    public function getById(int $id);
    public function getByProductId(int $id);
    public function getByNoPurchasing(string $noPurchasing);
    public function create(array $data);
    public function updateById(int $noPurchasing, array $data);
    public function updateByNoPurchasing(string $noPurchasing, array $data);
    public function deleteById(int $id);
    public function deleteByNoPurchasing(string $noPurchasing);
}
