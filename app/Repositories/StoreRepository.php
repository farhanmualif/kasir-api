<?php

namespace App\Repositories;

interface StoreRepository
{
    public function create(array $data);
    public function updateById($id,  $data);
    public function updateByUuid($uuid,  $data);
    public function delete($id);
    public function findById($id);
    public function findByUuid($uuid);
    public function getByUuid($uuid);
    public function getByd($id);
}
