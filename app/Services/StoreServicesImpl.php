<?php

use App\Repositories\StoreRepository;
use App\Services\StoreServices;

class StoreServicesImpl implements StoreServices
{

    public function __construct(protected StoreRepository $store_repository)
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
        $this->store_repository->create($data);
    }

    /**
     * @inheritDoc
     */
    public function updateById(int $id, array $data)
    {
    }
}
