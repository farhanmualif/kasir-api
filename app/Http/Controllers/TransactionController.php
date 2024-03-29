<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionStoreRequest;
use App\Http\Resources\TransactionCollection;
use App\Models\DetailTransaction;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return \responseJson("data transaksi ditemukan", TransactionCollection::collection(Transaction::all()), true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $payload = $request->validated();
            $transaction = $payload["transaction"];
            $total_payment = 0;

            foreach ($transaction['items'] as $item) {
                $total_payment += ($item['quantity'] * $item['item_price']);
            }

            if ($payload['transaction']['cash'] < $total_payment) {
                return responseJson("gagal menambahkan data transaction, cash kurang dari total transaction", null, false, 500);
            }

            $insert_transaction = Transaction::create([
                "no_transaction" => generateNoTransaction(),
                "total_payment" => $total_payment,
                "cash" => $transaction['cash'],
            ]);


            foreach ($transaction['items'] as $item) {
                DetailTransaction::create([
                    "id_transaction" => $insert_transaction->id,
                    "id_product" => $item['id_product'],
                    "item_price" => $item["item_price"],
                    "quantity" => $item['quantity'],
                    "total_price" => $item['item_price'] * $item['quantity'],
                ]);

                $product = Product::where('id', $item['id_product'])->first();
                $product->stock = $product->stock - $item['quantity'];
                $product->update();
            }


            DB::commit();
            return responseJson("berhasil menambahkan data transaksi", new TransactionCollection($insert_transaction));
        } catch (\Throwable $th) {
            DB::rollBack();
            return responseJson("gagal menambahkan data transaksi, {$th->getMessage()} file: {$th->getFile()} line: {$th->getLine()}", null, false, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $no_transaction)
    {
        try {
            $product = Transaction::where("no_transaction", $no_transaction)->first();

            return responseJson("berhasil ambil data transaksi", new TransactionCollection($product));
        } catch (\Throwable $th) {
            return responseJson("gagal ambil data transaksi {$th->getMessage()} file: {$th->getFile()} line: {$th->getLine()} {$th->getPrevious()}", null, false, 500);
        }
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
