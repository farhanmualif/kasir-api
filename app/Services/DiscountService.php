<?php


namespace App\Services;

use App\Http\Requests\DiscountCreateRequest;
use App\Http\Requests\DiscountUpdateRequest;

interface DiscountService
{
    public function create(DiscountCreateRequest $data);
    public function findById(int $data);
    public function getById(int $data);
    public function getAll();
    public function getByName(string $data);
    public function updateById(int $id, DiscountUpdateRequest $data);
    public function deleteById(int $data);
}
