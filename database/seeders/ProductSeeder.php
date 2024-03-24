<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Purchasing;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                // "uuid" => Uuid::uuid4(),
                "barcode" => "9898" . \rand(1, 20),
                "name" => "shampo clear",
                "stock" => 20,
                "selling_price" => 1000.0,
                "purchase_price" => 500.0,
                "image" => "product-default.png"
            ],
            [
                // "uuid" => Uuid::uuid4(),
                // "barcode" => "9898" . \rand(1, 20),
                "name" => "sabun lifeboy",
                "stock" => 20,
                "selling_price" => 1500.0,
                "purchase_price" => 1000.0,
                "image" => "product-default.png"
            ],
            [
                // "uuid" => Uuid::uuid4(),
                "barcode" => "9898" . \rand(1, 20),
                "name" => "aqua galon",
                "stock" => 10,
                "selling_price" => 1000.0,
                "purchase_price" => 1500.0,
                // "image" => "product-default.png"
            ],
        ];


        foreach ($products as $product) {
            $insert_product = Product::create($product);
            $total_payment = $insert_product->purchase_price * $insert_product->stock;
            echo $total_payment;
            Purchasing::create([
                "no_purchasing" => \rand(1, 20),
                "product_id" => $insert_product->id,
                "quantity" => $insert_product->stock,
                "description" => "pembelian $insert_product->name",
                "total_payment" => $total_payment
            ]);
        };
    }
}
