<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
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
                "barcode" => "9898" . \rand(1, 20),
                "name" => "shampo clear",
                "stock" => 20,
                "selling_price" => 1000.0,
                "purchase_price" => 500.0,

            ],
            [
                "name" => "sabun lifeboy",
                "stock" => 20,
                "selling_price" => 1500.0,
                "purchase_price" => 1000.0,

            ],
            [
                "barcode" => "9898" . \rand(1, 20),
                "name" => "aqua galon",
                "stock" => 10,
                "selling_price" => 20000.0,
                "purchase_price" => 17000.0,
            ],
        ];


        foreach ($products as $product) {
            $insert_product = Product::create($product);
            $total_payment = $insert_product->purchase_price * $insert_product->stock;
          
            Purchasing::create([
                "no_purchasing" => generateNoTransaction(),
                "product_id" => $insert_product->id,
                "quantity" => $insert_product->stock,
                "description" => "pembelian $insert_product->name",
                "total_payment" => $total_payment
            ]);
        };
    }
}
