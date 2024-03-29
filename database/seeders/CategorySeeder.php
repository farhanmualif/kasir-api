<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            "product_id" => 3,
            "uuid" => Uuid::uuid4(),
            "name" => "minuman",
        ];
        
        $insert_category =  Category::create([
            "name" => $categories["name"],
            "uuid" => Uuid::uuid4(),
        ]);

        ProductCategory::create([
            "product_id" => $categories["product_id"],
            "category_id" => $insert_category->id
        ]);
    }
}
