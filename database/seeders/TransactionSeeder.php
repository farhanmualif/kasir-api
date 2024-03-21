<?php

namespace Database\Seeders;

use App\Models\DetailTransaction;
use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactions = [
            [

                "id_product" => 1,
                "quantity" => 2,
                "total_price" => 2000.0
            ],
            [

                "id_product" => 2,
                "quantity" => 2,
                "total_price" => 3000.0
            ],
            [

                "id_product" => 3,
                "quantity" => 2,
                "total_price" => 3000
            ],
        ];

        $total_payment = 0;

        foreach ($transactions as $transaction) {
            $total_payment = $total_payment + $transaction["total_price"];
        }

        $insert_transaction = Transaction::create(
            [
                "no_transaction" => \generateNoTransaction(),
                "total_payment" => $total_payment,

            ]
        );

        foreach ($transactions as $transaction) {
            DetailTransaction::create([
                "id_transaction" => $insert_transaction->id,
                "id_product" => $transaction['id_product'],
                "quantity" =>  $transaction['quantity'],
            ]);
        }
    }
}
