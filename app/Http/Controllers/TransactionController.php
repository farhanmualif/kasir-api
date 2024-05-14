<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionStoreRequest;
use App\Http\Resources\TransactionCollection;
use App\Models\DetailTransaction;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return \responseJson("data transaksi ditemukan", TransactionCollection::collection(Transaction::all()), true);
    }

    public function store(TransactionStoreRequest $request)

    {

        DB::beginTransaction();
        try {
            $payload = $request->validated();
            $transaction = $payload["transaction"];
            $totalPayment = 0;

            foreach ($transaction['items'] as $product) {

                $findProduct = Product::find($product['id_product']);
                if (!$findProduct) {
                    return responseJson("produk tidak tersedia", null, false, Response::HTTP_BAD_REQUEST);
                }

                $currentStock = $findProduct->stock;

                if ($currentStock <= 0) {
                    return responseJson("stock product {$findProduct->name} tidak tersedia", null, false, Response::HTTP_BAD_REQUEST);
                }

                if (($currentStock - $product['quantity']) < 1) {
                    return responseJson("stok produk tidak mencukupi", null, false, Response::HTTP_BAD_REQUEST);
                }
            }

            foreach ($transaction['items'] as $item) {
                $totalPayment += ($item['quantity'] * $item['item_price']);
            }

            if ($transaction['cash'] < $totalPayment) {
                return responseJson("gagal menambahkan data transaction, cash kurang dari total transaction", null, false, Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            DB::beginTransaction();

            $insertTransaction = Transaction::create([
                "no_transaction" => generateNoTransaction(),
                "total_payment" => $totalPayment,
                "cash" => $transaction['cash'],
            ]);

            foreach ($transaction['items'] as $item) {
                DetailTransaction::create([
                    "id_transaction" => $insertTransaction->id,
                    "id_product" => $item['id_product'],
                    "item_price" => $item["item_price"],
                    "quantity" => $item['quantity'],
                    "total_price" => $item['item_price'] * $item['quantity'],
                ]);

                $product = Product::where('id', $item['id_product'])->first();
                $product->stock = $product->stock - $item['quantity'];
                $product->save();
            }

            $resGenerate = generateInvoice($insertTransaction->no_transaction);

            DB::commit();

            return responseJson("$resGenerate && berhasil menambahkan data transaksi", new TransactionCollection($insertTransaction), Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return responseJson("gagal menambahkan data transaksi, {$th->getMessage()} file: {$th->getFile()} line: {$th->getLine()}", null, false, Response::HTTP_INTERNAL_SERVER_ERROR);
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
