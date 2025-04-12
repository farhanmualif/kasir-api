<?php

namespace App\Repositories;

use App\Models\DetailTransaction as ModelsDetailTransaction;


class DetailTransactionRepositoryImpl implements DetailTransactionRepository
{

    public function __construct(public ModelsDetailTransaction $detailTransaction) {}

    /**
     * @inheritDoc
     */
    public function create(array $data)
    {
        return $this->detailTransaction->create($data);
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $id)
    {
        return $this->detailTransaction->find($id)->delete();
    }

    /**
     * @inheritDoc
     */
    public function deleteByUuid(string $uuid)
    {
        return $this->detailTransaction->where('uuid', $uuid)->delete();
    }

    /**
     * @inheritDoc
     */
    public function findById(int $id)
    {
        return $this->detailTransaction->find($id);
    }

    /**
     * @inheritDoc
     */
    public function findByUuid(string $uuid)
    {
        return $this->detailTransaction->where('uuid', $uuid);
    }


    /**
     * @inheritDoc
     */
    public function updateById(int $id, array $data)
    {
        return $this->detailTransaction->find($id)->update($data);
    }

    /**
     * @inheritDoc
     */
    public function updateByUuid(string $uuid, array $data)
    {
        return $this->detailTransaction->where('uuid', $uuid)->update($data);
    }
}
