<?php

namespace Database\Seeders;

use App\Models\DetailTransaction;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;


class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactions = [
            [
                "cash" => 25000.00,
                "created_at" => "2023-03-08 13:03:12",
                "items" => [
                    [
                        "id_product" => 3,
                        "name" => "sari roti",
                        "quantity" => 1,
                        "item_price" => 20000.00
                    ],
                    [
                        "id_product" => 2,
                        "name" => "sabun colek",
                        "quantity" => 3,
                        "item_price" => 1500.00
                    ]
                ]
            ]
        ];

        foreach ($transactions as $transaction) {
            $total_payment = 0;

            foreach ($transaction['items'] as $item) {
                $total_payment += ($item['quantity'] * $item['item_price']);
            }

            $insert_transaction = Transaction::create([
                "no_transaction" => generateNoTransaction(),
                "total_payment" => $total_payment,
                "cash" => $transaction['cash'],
                "created_at" => $transaction['created_at'],
                "updated_at" => $transaction['created_at'],
            ]);

            foreach ($transaction['items'] as $item) {
                DetailTransaction::create([
                    "id_transaction" => $insert_transaction->id,
                    "id_product" => $item['id_product'],
                    "item_price" => $item['item_price'],
                    "total_price" => $item['item_price'] * $item['quantity'],
                    "quantity" => $item['quantity'],
                ]);
            }
        }
    }
}
