<?php

namespace App\Repositories;

use App\Models\Store;
use App\Repositories\StoreRepository;

class StoreRepositoryImpl implements StoreRepository
{

    public function __construct(public Store $store)
    {
    }

    /**
     * @inheritDoc
     */
    public function create($data)
    {
        return $this->store->create($data);
    }

    /**
     * @inheritDoc
     */
    public function delete($id)
    {
        return $this->store->find($id)->delete();
    }

    /**
     * @inheritDoc
     */
    public function findById($id)
    {
        return $this->store->where('id', $id)->exists();
    }

    /**
     * @inheritDoc
     */
    public function findByUuid($uuid)
    {
        return $this->store->where("uuid", $uuid)->exists();
    }

    /**
     * @inheritDoc
     */
    public function getByid($id)
    {
        return $this->store->findOrFail($id)->first();
    }

    /**
     * @inheritDoc
     */
    public function getByUuid($uuid)
    {
        return $this->store->where("uuid", $uuid)->first();
    }

    /**
     * @inheritDoc
     */
    public function getByd($id)
    {
        return $this->store->find($id);
    }

    /**
     * @inheritDoc
     */
    public function updateById($id,  $data)
    {
        return $this->store->find($id)->update($data);
    }

    /**
     * @inheritDoc
     */
    public function updateByUuid($uuid,  $data)
    {
        return $this->store->where('uuid', $uuid)->update($data);
    }
    /**
     * @inheritDoc
     */
    public function deleteById($id)
    {
        return $this->store->destroy($id);
    }

    /**
     * @inheritDoc
     */
    public function deleteByUuid($uuid)
    {
        return $this->store->where('uuid', $uuid)->delete();
    }
    /**
     * @inheritDoc
     */
    public function deleteByUserUuid($userUuid)
    {
        return  $this->store->user()->where('uuid', $userUuid)->delete();
    }
}
