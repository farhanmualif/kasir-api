<?php

namespace App\Services;


interface UserService
{
    public function register(array $payload);
    public function login(array $payload, $request);
    public function logout($request);
    public function getAll();
    public function getUserById(int $id);
    public function getUserByEmail(string $email);
}
