<?php

namespace App\Repositories;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class UserRepositoryImpl implements UserRepository
{

    function getAll()
    {
        return User::all();
    }
    function findById($id)
    {
        return User::where('id', $id)->exists();
    }
    function findByUuid($uuid)
    {
        return User::where('uuid', $uuid)->exists();
    }

    function getById($id)
    {

        return User::find($id)->first();
    }
    function getByUuid(string $uuid)
    {
        return User::where('uuid', $uuid)->first();
    }
    function getByEmail($email)
    {
        return User::where('email', $email)->first();
    }
    function create($user)
    {
        return User::create($user);
    }
    function deleteById($id)
    {
        return User::destroy($id);
    }
    function deleteByUuid($uuid)
    {
        return User::where('uuid', $uuid)->delete();
    }
    function deleteByEmail($email)
    {
        return User::where('email', $email)->delete();
    }
    function updateByEmail($email, $payload)
    {
        return User::where('email', $email)->update($payload);
    }

    public function findByEmail(string $email)
    {
        return User::where('email', $email)->exists();
    }
    public function deleteToken(string $tokenId)
    {
        return PersonalAccessToken::where('id', $tokenId)->delete();
    }

    /**
     * @inheritDoc
     */
    public function revokeCurrentToken($token)
    {
        return $token->delete();
    }
    /**
     * @inheritDoc
     */

    /**
     * @inheritDoc
     */
    public function updateById(string $id, array $payload)
    {
        return User::where('id', $id)->update($payload);
    }

    /**
     * @inheritDoc
     */
    public function updateByUuid(string $uuid, array $payload)
    {
        return User::where('uuid', $uuid)->update($payload);
    }
    /**
     * @inheritDoc
     */

}
