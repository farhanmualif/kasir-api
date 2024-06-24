<?php

namespace App\Services;


interface StoreServices
{
    public function store(array $data);
    public function updateById(int $id, array $data);
    public function destroy($id);
    public function getAll();
    public function getById(int $id);
    public function findById(int $id);
}
