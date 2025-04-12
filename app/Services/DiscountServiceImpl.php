<?php

namespace App\Services;

use App\Http\Requests\DiscountStoreRequest;
use App\Http\Requests\DiscountUpdateRequest;
use App\Repositories\DiscountRepository;
use Illuminate\Support\Facades\Auth;

class DiscountServiceImpl implements DiscountService
{

    public function __construct(public DiscountRepository $discountRepository) {}
    public function create(DiscountStoreRequest $data)
    {
        $discountValidate = $data->validated();

        // Ambil store pertama milik user yang sedang login
        $userStore = Auth::user()->stores()->first();

        if (!$userStore) {
            throw new \Exception('Anda belum memiliki store');
        }

        $discountValidate['store_id'] = $userStore->id;

        return $this->discountRepository->create($discountValidate);
    }
    public function getByUuId(string $id)
    {
        return $this->discountRepository->getByUuId($id);
    }

    public function getAll()
    {
        return $this->discountRepository->getAll();
    }

    public function getByName(string $data)
    {
        return $this->discountRepository->getByName($data);
    }
    public function updateByUuId(string $id, DiscountUpdateRequest $data)
    {
        $discountValidate = $data->validated();
        $userStore = Auth::user()->stores()->first();
        if (!$userStore) {
            throw new \Exception('User Tidak Ditemukan');
        }
        $discountValidate['store_id'] = $userStore->id;
        return $this->discountRepository->updateByUuId($id, $discountValidate);
    }
    public function deleteByUuId(string $id)
    {
        $findDiscount = $this->getByUuId($id);
        if (!$findDiscount) {
            throw new \Exception('Discount Tidak Ditemukan');
        }
        return $this->discountRepository->deleteByUuId($id);
    }
}
