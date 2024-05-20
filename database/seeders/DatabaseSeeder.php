<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        DB::beginTransaction();
        try {
            $this->call([
                UserSeeder::class,
                ProductSeeder::class,
                CategorySeeder::class,
                TransactionSeeder::class,
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            echo $th;
            DB::rollBack();
            DB::commit();
        }
    }
}
