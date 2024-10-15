<?php


namespace App\Services;
use App\Repositories\PurchasingRepository;
use App\Services\PurchasingService;

class PurchasingServiceImpl implements PurchasingService
{

    public function __construct(public PurchasingRepository $purchasingRepository)
    {
    }

    /**
     * @inheritDoc
     */
    public function create(array $data)
    {
        return $this->purchasingRepository->create($data);
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $id)
    {
        return $this->purchasingRepository->deleteById($id);
    }

    /**
     * @inheritDoc
     */
    public function deleteByNoPurchasing(int $id)
    {
        return $this->purchasingRepository->deleteByNoPurchasing($id);
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id)
    {
        return $this->purchasingRepository->getById($id);
    }

    /**
     * @inheritDoc
     */
    public function getByNoPurchasing(int $id)
    {
        return $this->purchasingRepository->getByNoPurchasing($id);
    }

    /**
     * @inheritDoc
     */
    public function updateById(int $id, array $data)
    {
        return $this->purchasingRepository->updateById($id, $data);
    }

    /**
     * @inheritDoc
     */
    public function updateByNoPurchasing(int $id, array $data)
    {
        return $this->purchasingRepository->updateById($id, $data);
    }
}
