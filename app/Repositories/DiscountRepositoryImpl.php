<?php

namespace App\Repositories;


use App\Models\Discounts;

class DiscountRepositoryImpl implements DiscountRepository
{

    public function __construct(public Discounts $discounts) {}
    public function getAll()
    {
        return $this->discounts->all();
    }
    public function create(array $data)
    {
        return $this->discounts->create($data);
    }
    public function update(array $data, $id)
    {
        return $this->discounts->update($data, $id);
    }

    public function updateByUuid(string $uuid, array $data)
    {
        return $this->discounts->where('uuid', $uuid)->update($data);
    }

    public function deleteByUuid(string $uuid)
    {
        return $this->discounts->where('uuid', $uuid)->delete();
    }

    public function getByName(string $name)
    {
        return $this->discounts->where('name', $name)->first();
    }
    public function getByUuid(string $uuid)
    {
        return $this->discounts->where('uuid', $uuid)->first();
    }
}
