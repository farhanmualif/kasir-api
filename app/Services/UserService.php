<?php

namespace App\Services;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreStoreRequest;

interface UserService
{
    public function register(StoreStoreRequest $payload);
    public function login(LoginRequest $payload);
    public function logout($request);
    public function getAll();
    public function getUserById(int $id);
    public function getUserByUuid(string $uuid);
    public function getUserByEmail(string $email);
    public function findById(string $id);
    public function findByUuid(string $uuid);
    public function findByEmail(string $email);
    public function updateById(string $id, $payload);
    public function updateByUuid(string $uuid, $payload);
    public function updateByEmail(string $email, $payload);
    public function deleteByEmail(string $email);
    public function deleteById(string $id);
    public function deleteByUuid(string $uuid);
}
