<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionStoreRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(public  TransactionService $transactionService)
    {
    }
    public function index()
    {
        return \responseJson("data transaksi ditemukan", $this->transactionService->getAll());
    }

    public function store(TransactionStoreRequest $request)
    {
        return \responseJson("berhasil menyimpan data transaksi", $this->transactionService->create($request));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $no_transaction)
    {
        return responseJson("berhasil mendapatkan data", $this->transactionService->getByNoTransaction($no_transaction));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
