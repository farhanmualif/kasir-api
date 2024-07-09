<?php


namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\TransactionRepository;


class TransactionRepositoryImpl implements TransactionRepository
{

    public function __construct(public Transaction $transaction)
    {
    }

    /**
     * @inheritDoc
     */
    public function create(array $data)
    {
        return $this->transaction->create($data);
    }
    public function getAll()
    {
        return $this->transaction->all();
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $id)
    {
        return $this->transaction->find($id)->delete();
    }

    /**
     * @inheritDoc
     */
    public function deleteByNoTransaction(string $noTransaction)
    {
        return $this->transaction->where('no_transaction', $noTransaction)->delete();
    }

    /**
     * @inheritDoc
     */
    public function deleteByUuid(string $uuid)
    {
        return $this->transaction->where('uuid', $uuid)->delete();
    }

    /**
     * @inheritDoc
     */
    public function findById(int $id)
    {
        return $this->transaction->find($id);
    }

    /**
     * @inheritDoc
     */
    public function findByNoTransaction(string $noTransaction)
    {
        return $this->transaction->where('no_transaction', $noTransaction);
    }

    /**
     * @inheritDoc
     */
    public function findByUuid(string $uuid)
    {
        return $this->transaction->where('uuid', $uuid);
    }
}
