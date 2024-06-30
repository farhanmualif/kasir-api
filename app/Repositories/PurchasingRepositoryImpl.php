<?php

namespace App\Repositories;

use App\Models\Purchasing;


class PurchasingRepositoryImpl implements PurchasingRepository
{

    public function __construct(private Purchasing $purchasing)
    {
    }

    /**
     * @inheritDoc
     */
    public function create(array $data)
    {
        return $this->purchasing->create($data);
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $id)
    {
        return $this->purchasing->find($id)->delete();
    }

    /**
     * @inheritDoc
     */
    public function deleteByNoPurchasing(string $noPurchasing)
    {
        return $this->purchasing->where('no_purchasing', $noPurchasing)->delete();
    }

    /**
     * @inheritDoc
     */
    public function findById(int $id)
    {
        return $this->purchasing->find($id)->exists();
    }

    /**
     * @inheritDoc
     */
    public function findByNoPurchasing(string $noPurchasing)
    {
        return $this->purchasing->where('no_purchasing')->exists();
    }

    /**
     * @inheritDoc
     */
    public function updateById(int $id, array $data)
    {
        return $this->purchasing->find($id)->update($data);
    }

    /**
     * @inheritDoc
     */
    public function updateByNoPurchasing(string $noPurchasing, array $data)
    {
        return $this->purchasing->where('no_purchasing', $noPurchasing)->update($data);
    }
    /**
     * @inheritDoc
     */
    public function getById(int $id)
    {
        return $this->purchasing->find($id);
    }
    /**
     * @inheritDoc
     */
    public function getByProductId(int $id)
    {
        return $this->purchasing->where('product_id', $id);
    }

    /**
     * @inheritDoc
     */
    public function getByNoPurchasing(string $noPurchasing)
    {
        return $this->purchasing->where('no_purchasing', $noPurchasing)->get();
    }
}
