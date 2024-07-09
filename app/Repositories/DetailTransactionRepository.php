<?php

namespace App\Repositories;
interface DetailTransactionRepository
{
    public function create(array $data);
    public function findById(int $id);
    public function findByUuid(string $uuid);
    public function updateById(int $id, array $data);
    public function updateByUuid(string $uuid, array $data);
    public function deleteById(int $id);
    public function deleteByUuid(string $uuid);
}
