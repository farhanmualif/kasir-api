<?php

namespace App\Repositories;

use App\Models\Store;
use App\Repositories\StoreRepository;

class StoreRepositoryImpl implements StoreRepository
{

    /**
     * @inheritDoc
     */
    public function create(array $data)
    {
        return Store::create($data);
    }

    /**
     * @inheritDoc
     */
    public function delete($id)
    {
        return Store::destroy($id);
    }

    /**
     * @inheritDoc
     */
    public function findById($id)
    {

        return Store::find($id)->exists();
    }

    /**
     * @inheritDoc
     */
    public function findByUuid($uuid)
    {
        return Store::where("uuid", $uuid)->exists();
    }

    /**
     * @inheritDoc
     */
    public function getByid($id)
    {
        return Store::findOrFail($id)->first();
    }

    /**
     * @inheritDoc
     */
    public function getByUuid($uuid)
    {
        return Store::where("uuid", $uuid)->first();
    }

    /**
     * @inheritDoc
     */
    public function getByd($id)
    {
    }

    /**
     * @inheritDoc
     */
    public function updateById($id,  $data)
    {
    }

    /**
     * @inheritDoc
     */
    public function updateByUuid($uuid,  $data)
    {
    }
    /**
     * @inheritDoc
     */
    public function deleteById($id)
    {
        return Store::destroy($id);
    }

    /**
     * @inheritDoc
     */
    public function deleteByUuid($uuid)
    {
        return Store::where('uuid', $uuid)->delete();
    }
    /**
     * @inheritDoc
     */
    public function deleteByUserUuid($userUuid)
    {
        $store = new Store();

        return $store->user()->where('uuid', $userUuid)->delete();
    }
}
