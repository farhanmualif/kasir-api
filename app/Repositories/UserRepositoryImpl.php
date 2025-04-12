<?php

namespace App\Repositories;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class UserRepositoryImpl implements UserRepository
{

    public function __construct(public User $user) {}

    function getAll()
    {
        return $this->user->all();
    }
    function findById($id)
    {
        return $this->user->where('id', $id)->exists();
    }
    function findByUuid($uuid)
    {
        return $this->user->where('uuid', $uuid)->exists();
    }

    function getById($id)
    {
        return $this->user->find($id);
    }

    function getByUuid(string $uuid)
    {
        return $this->user->where('uuid', $uuid)->first();
    }
    function getByEmail($email)
    {
        return $this->user->where('email', $email)->first();
    }
    function create($user)
    {
        return $this->user->create($user);
    }
    function deleteById($id)
    {
        return $this->user->destroy($id);
    }
    function deleteByUuid($uuid)
    {
        return $this->user->where('uuid', $uuid)->delete();
    }
    function deleteByEmail($email)
    {
        return $this->user->where('email', $email)->delete();
    }
    function updateByEmail($email, $payload)
    {
        return $this->user->where('email', $email)->update($payload);
    }

    public function findByEmail(string $email)
    {
        return $this->user->where('email', $email)->exists();
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
        return $this->user->where('id', $id)->update($payload);
    }

    /**
     * @inheritDoc
     */
    public function updateByUuid(string $uuid, array $payload)
    {
        return $this->user->where('uuid', $uuid)->update($payload);
    }

    public function getUserWithStore(string $userUuid)
    {
        return $this->user->where('uuid', $userUuid)->with('store')->first();
    }
}
