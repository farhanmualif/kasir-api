<?php

namespace Database\Seeders;

use App\Models\Category;
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
                [
                    "uuid"=> Uuid::uuid4(),
                    "name"=>"peralatan mandi",
                    "uuid"=> Uuid::uuid4(),
                    "name"=>"minuman",
                    "uuid"=> Uuid::uuid4(),
                    "name"=>"snack",
                    "uuid"=> Uuid::uuid4(),
                    "name"=>"bahan dapur",
                ]
            ];

        foreach ($categories as $category) {
            Category::create($category);

        }
    }
}
