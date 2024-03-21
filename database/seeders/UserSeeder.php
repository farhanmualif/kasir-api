<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = [
            'uuid'=> Uuid::uuid4(),
            'name'=>'testing',
            'email'=>'testing@mail.com',
            'password' => Hash::make('testing123'),
            'role'=>'admin',
        ];

        User::create($user);
    }
}
