<?php


namespace App\Repositories;


interface DiscountRepository
{
    public function create(array $data);
    public function update(array $data, $id);
    public function getAll();
    public function getByUuId(string $id);
    public function getByName(string $name);
    public function updateByUuId(string $id, array $data);
    public function deleteByUuId(string $id);
}
