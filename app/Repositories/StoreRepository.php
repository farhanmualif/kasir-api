<?php

namespace App\Repositories;

interface StoreRepository
{
    public function create($data);
    public function updateById($id,  $data);
    public function updateByUuid($uuid,  $data);
    public function deleteById($id);
    public function deleteByUuid($uuid);
    public function deleteByUserUuid($userUuid);
    public function findById($id);
    public function findByUuid($uuid);
    public function getByUuid($uuid);
    public function getByd($id);
}
