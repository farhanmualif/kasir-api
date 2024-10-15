<?php


namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\DB;

class TransactionRepositoryImpl implements TransactionRepository
{

    public function __construct(public Transaction $transaction) {}

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

    public function getSalesInvoice(string $noTransaction)
    {

        return DB::table('transactions')
            ->join('detail_transactions', 'transactions.id', '=', 'detail_transactions.id_transaction')
            ->join('products', 'detail_transactions.id_product', '=', 'products.id')
            ->select(
                'transactions.no_transaction',
                'products.name',
                'detail_transactions.quantity',
                'detail_transactions.item_price',
                'detail_transactions.total_price',
                'transactions.total_payment',
                'transactions.cash',
                DB::raw('TIME(transactions.created_at) as time'),
                DB::raw('DATE(transactions.created_at) as date'),
            )
            ->where('transactions.no_transaction', '=', $noTransaction)
            ->get();
    }
}
