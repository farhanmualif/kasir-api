<?php

namespace App\Services;

use App\Http\Requests\TransactionStoreRequest;

interface TransactionService
{
    public function create(TransactionStoreRequest $data);
    public function getAll();
    public function getByNoTransaction(string $noTransaction);
    public function update(TransactionStoreRequest $data, array $transaction);

    public function getInvoice(string $noTransaction);
    public function generateInvoice(string $noTransaction);
}
