<?php

namespace App\Services;


interface StoreService
{
    public function store(array $data);
    public function updateById(int $id, array $data);
    public function deleteById($id);
    public function deleteByUuid($uuid);
    public function deleteByUserUuid($userUuid);
    public function getAll();
    public function getById(int $id);
    public function findById(int $id);
}
