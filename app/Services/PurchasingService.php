<?php

namespace App\Services;


interface PurchasingService
{
    public function create(array $data);
    public function updateById(int $id, array $data);
    public function updateByNoPurchasing(int $id, array $data);
    public function getById(int $id);
    public function getByNoPurchasing(int $id);
    public function deleteById(int $id);
    public function deleteByNoPurchasing(int $id);
}
