<?php

namespace App\Services;

use App\Http\Requests\DiscountStoreRequest;
use App\Http\Requests\DiscountUpdateRequest;
use App\Models\Discounts;

interface DiscountService
{
    public function create(DiscountStoreRequest $data);
    public function getByUuId(string $data);
    public function getAll();
    public function getByName(string $data);
    public function updateByUuId(string $id, DiscountUpdateRequest $data);
    public function deleteByUuId(string $id);
}
