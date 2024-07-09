<?php

namespace App\Repositories;


interface TransactionRepository
{
    public function getAll();
    public function create(array $data);
    public function findById(int $id);
    public function findByUuid(string $uuid);
    public function findByNoTransaction(string $noTransaction);
    public function deleteById(int $id);
    public function deleteByUuid(string $uuid);
    public function deleteByNoTransaction(string $noTransaction);
}
