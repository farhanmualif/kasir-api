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
        $transactions = [];

        for ($i = 1; $i <= 30; $i++) {

            for ($j = 1; $j <= 30; $j++) {
                $transaction = [
                    "cash" => 10000,
                    "created_at" => Carbon::create(2021, $i == $j ? $i + 1 : $i, $j),
                    "items" => [
                        (object)[
                            "id_product" => 1,
                            "name" => "shampo clear",
                            "quantity" => 2,
                            "item_price" => 1000.00
                        ],
                        (object)[
                            "id_product" => 2,
                            "name" => "sabun lifeboy",
                            "quantity" => 2,
                            "item_price" => 1500.00
                        ],
                    ],
                ];
            }

            $transactions[] = $transaction;
        }


        foreach ($transactions as $transaction) {
            $total_payment = 0;
            foreach ($transaction['items'] as $item) {
                $total_price = $item->item_price * $item->quantity;
                $total_payment += $total_price;
            }

            // dd($transaction);

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
                    "id_product" => $item->id_product,
                    "item_price" => $item->item_price,
                    "total_price" => $item->item_price * $item->quantity,
                    "quantity" => $item->quantity,
                ]);
            }
        }
    }
}
