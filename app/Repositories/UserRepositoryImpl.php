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
    function getByUuid($uuid)
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
    function updateByEmail($email)
    {
        return User::where('email', $email)->update($email);
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
}
