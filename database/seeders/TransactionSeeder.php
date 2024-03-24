<?php

namespace Database\Seeders;

use App\Models\DetailTransaction;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $transactions = [
        //     [
        //         "id_product" => 1,
        //         "quantity" => 2,
        //         "total_price" => 2000.0,
        //     ],
        //     [

        //         "id_product" => 2,
        //         "quantity" => 2,
        //         "total_price" => 3000.0,
        //     ],
        //     [

        //         "id_product" => 3,
        //         "quantity" => 2,
        //         "total_price" => 3000,
        //     ],
        // ];

        // $total_payment = 0;

        // $cash = 10000.00;

        // foreach ($transactions as $transaction) {
        //     $total_payment = $total_payment + $transaction["total_price"];
        // }

        // $insert_transaction = Transaction::create(
        //     [
        //         "no_transaction" => \generateNoTransaction(),
        //         "total_payment" => $total_payment,
        //         "cash" => $cash,
        //         "change" => $cash - $total_payment
        //     ]
        // );

        // foreach ($transactions as $transaction) {
        //     DetailTransaction::create([
        //         "id_transaction" => $insert_transaction->id,
        //         "id_product" => $transaction['id_product'],
        //         "total_price" => $transaction['total_price'],
        //         "quantity" =>  $transaction['quantity'],
        //     ]);
        // }



        $transaction = [
            "cash" => 10000.00,
            "items" =>  [
                [
                    "id_product" => 1,
                    "name" => "shampo clear",
                    "quantity" => 2,
                    "total_price" => 2000.00
                ],
                [
                    "id_product" => 2,
                    "name" => "sabun lifeboy",
                    "quantity" => 2,
                    "total_price" => 3000.00
                ],
            ]
        ];

        $total_payment = 0;
        foreach ($transaction['items'] as $item) {
            $total_payment = $total_payment + $item['total_price'];
        }

        $insert_transaction = Transaction::create(
            [
                "no_transaction" => generateNoTransaction(),
                "total_payment" => $total_payment,
                "cash" => $transaction['cash'],
                "change" => $transaction['cash'] - $total_payment,
            ]
        );
        foreach ($transaction['items'] as $item) {
            DetailTransaction::create(
                [
                    "id_transaction" => $insert_transaction->id,
                    "id_product" => $item['id_product'],
                    "total_price" => $item['total_price'],
                    "quantity" => $item['quantity']
                ]
            );
        }
    }
}
