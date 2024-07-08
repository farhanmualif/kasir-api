<?php

namespace App\Repositories;

interface CategoryRepository
{
    public function getAll();
    public function create($data);
    public function getById(int $id);
    public function getByUuid(string $uuid);
    public function getByName(string $name);
    public function findById(int $id);
    public function findByUuid(string $uuid);
    public function updateById(int $id, array $data);
    public function updateByUuid(string $uuid, array $data);
    public function deleteById(int $id);
    public function deleteByUuid(string $uuid);
}
