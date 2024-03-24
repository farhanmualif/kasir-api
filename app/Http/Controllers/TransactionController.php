<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionStoreRequest;
use App\Http\Resources\TransactionCollection;
use App\Models\DetailTransaction;
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
        return \responseJson("data found", TransactionCollection::collection(Transaction::all()), true);
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
            $body_transaction = $request->all();
            $total_payment = 0;
            foreach ($body_transaction["transaction"]["items"] as $item) {
                $total_payment += $item["total_price"];
            }

            $insert_transaction = Transaction::create([
                "no_transaction" => generateNoTransaction(),
                "total_payment" => $total_payment,
                "cash" => $body_transaction['transaction']['cash'],
                "change" => $body_transaction['transaction']['cash'] - $total_payment
            ]);

            foreach ($body_transaction["transaction"]["items"] as $item) {
                DetailTransaction::create([
                    "id_transaction" => $insert_transaction->id,
                    "id_product" => $item['id_product'],
                    "total_price" => $item['total_price'],
                    "quantity" => $item['quantity'],
                ]);
            }

            DB::commit();
            return responseJson("insert data successfully", new TransactionCollection($insert_transaction));
        } catch (\Throwable $th) {
            DB::rollBack();
            DB::commit();
            return responseJson("insert data failure, {$th->getMessage()} file: {$th->getFile()} line: {$th->getLine()}", null, false, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $no_transaction)
    {
        try {
            $product = Transaction::where("no_transaction", $no_transaction)->first();

            return responseJson("get data successfully", new TransactionCollection($product));
        } catch (\Throwable $th) {
            return responseJson("get data failed {$th->getMessage()} file: {$th->getFile()} line: {$th->getLine()} {$th->getPrevious()}", null, false, 500);
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
