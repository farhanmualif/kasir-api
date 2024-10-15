<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {



        DB::beginTransaction();
        try {

            $newUser =  User::create([
                'uuid' => Uuid::uuid4(),
                'name' => 'testing',
                'email' => 'testing@mail.com',
                'password' => Hash::make('testing123'),
                'role' => 'admin',
            ]);

            Store::create([
                'uuid' => Uuid::uuid4(),
                'name' => "store_{$newUser->name}",
                'user_id' =>  $newUser->id,
                'address' => 'yogyakarta',
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
