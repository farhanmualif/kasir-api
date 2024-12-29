<?php

namespace App\Repositories;


interface UserRepository
{
    function getAll();

    function findById(int $id);
    function findByUuid(string $uuid);
    function findByEmail(string $email);
    function getById(int $id);
    function getByUuid(string $uuid);
    function getByEmail(string $email);
    function create($user);
    function deleteById(int $id);
    function deleteByUuid(string $id);
    function deleteByEmail(string $email);
    function updateByEmail(string $email, array $payload);
    function updateById(string $id, array $payload);
    function updateByUuid(string $uuid, array $payload);
    function deleteToken(string $tokenId);
    function revokeCurrentToken(string $token);
    function getUserWithStore(string $userUuid);
}
