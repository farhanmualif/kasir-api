<?php

namespace App\Services;
use App\Repositories\StoreRepository;




class StoreServiceImpl implements StoreService
{

    public function __construct(protected StoreRepository $storeRepository)
    {
    }

    /**
     * @inheritDoc
     */
    public function destroy($id)
    {
    }

    /**
     * @inheritDoc
     */
    public function findById(int $id)
    {
    }

    /**
     * @inheritDoc
     */
    public function getAll()
    {
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id)
    {
    }

    /**
     * @inheritDoc
     */
    public function store(array $data)
    {
        $this->storeRepository->create($data);
    }

    /**
     * @inheritDoc
     */
    public function updateById(int $id, array $data)
    {
    }
    /**
     * @inheritDoc
     */
    public function deleteById($id)
    {
        return $this->storeRepository->deleteById($id);
    }

    /**
     * @inheritDoc
     */
    public function deleteByUuid($uuid)
    {
        return $this->storeRepository->deleteByUuid($uuid);
    }
    /**
     * @inheritDoc
     */
    public function deleteByUserUuid($userUuid)
    {
        return $this->storeRepository->deleteByUserUuid($userUuid);
    }
}
