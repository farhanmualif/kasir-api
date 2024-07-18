<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Repositories\StoreRepository;
use App\Repositories\UserRepository;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class UserServiceImpl implements UserService
{
    public function __construct(public UserRepository $userRepository, public Logger $logging, public StoreRepository $storeRepository)
    {
    }

    /**
     * @inheritDoc
     */
    public function getAll()
    {
        return $this->userRepository->getAll();
    }

    /**
     * @inheritDoc
     */
    public function getUserById(int $id)
    {
        return $this->userRepository->getById($id);
    }

    /**
     * @inheritDoc
     */
    public function getUserByEmail(string $email)
    {
        return $this->userRepository->getByEmail($email);
    }

    /**
     * @inheritDoc
     */
    public function login(array $payload, $request)
    {
        try {

            $checkUser = $this->userRepository->findByEmail($payload['email']);

            if (!$checkUser) {
                throw new ApiException('user tidak ditemukan');
            }

            if (Auth::attempt(['email' => $payload['email'], 'password' => $payload['password']])) {

                $success['token'] = $request->user()->createToken('token-name', ['server:update'])->plainTextToken;
                $success['uuid'] = $request->user()['uuid'];
                $success['user'] = $request->user()['name'];
                $success['email'] = $request->user()['email'];


                $this->logging->info('User logged in successfully with Email: ' . $request->user()['email']);

                return
                    [
                        'status' => true,
                        'data' => $success
                    ];
            } else {

                return [
                    'status' => false,
                    'data' => 'tidak dapat login'
                ];
            }
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'data' => $th
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function logout($token)
    {
        $this->logging->channel('info')->info('User logged out successfully with ID: ' . auth()->id());
        return $this->userRepository->revokeCurrentToken($token);
    }

    /**
     * @inheritDoc
     */
    public function register(array $payload)
    {
        DB::beginTransaction();
        try {

            /** check any user in database */

            $payload["uuid"] = Uuid::uuid4();

            if ($this->userRepository->findByEmail($payload['email'])) {
                throw new ApiException('email sudah dipakai', 409);
            }

            /* insert user */
            $createUser = $this->userRepository->create($payload);

            /* create store */
            $createStore = $this->storeRepository->create([
                'uuid' => Uuid::uuid4()->toString(),
                'name' => "{$createUser->name}_store",
                'address' => $payload['address'],
                'user_id' => $createUser->id
            ]);

            $response = [
                'store' => $createStore->name,
                'name' => $createUser->name,
                'email' => $createUser->email,
                'address' => $createStore->address
            ];

            $this->logging->info('User register successfully with Email: ' . $createUser->email);

            DB::commit();
            return $response;
        } catch (\Illuminate\Database\QueryException $th) {
            DB::rollBack();
            throw new ApiException($th->getMessage());
        }
    }
    /**
     * @inheritDoc
     */
    public function findByEmail(string $email)
    {
        $findUser = $this->userRepository->findByEmail($email);
        if (!$findUser) {
            throw new ApiException('user tidak ditemukan');
        }
        return $findUser;
    }

    /**
     * @inheritDoc
     */
    public function findById(string $id)
    {
        $findUser = $this->userRepository->findById($id);
        if (!$findUser) {
            throw new ApiException('user tidak ditemukan');;
        }
        return $findUser;
    }

    /**
     * @inheritDoc
     */
    public function findByUuid(string $uuid)
    {
        $findUser = $this->userRepository->findByUuid($uuid);
        if (!$findUser) {
            throw new ApiException('user tidak ditemukan');;
        }
        return $findUser;
    }
    /**
     * @inheritDoc
     */
    public function updateById(string $id, $payload)
    {

        return $this->userRepository->updateByid($id, $payload);
    }
    /**
     * @inheritDoc
     */
    public function updateByEmail(string $email, $payload)
    {
        return $this->userRepository->updateByid($email, $payload);
    }

    /**
     * @inheritDoc
     */
    public function updateByUuid(string $uuid, $payload)
    {
        try {
            $findUser = $this->userRepository->findByUuid($uuid);
            if (!$findUser) {
                throw new ApiException('user tidak ditemukan');
            }
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage());
        }
        return $this->userRepository->updateByUuid($uuid, $payload);
    }
    /**
     * @inheritDoc
     */
    public function getUserByUuid(string $uuid)
    {
        return $this->userRepository->getByUuid($uuid);
    }
    /**
     * @inheritDoc
     */
    public function deleteByEmail(string $email)
    {
        return $this->userRepository->deleteByEmail($email);
    }
    /**
     * @inheritDoc
     */
    public function deleteById(string $id)
    {
        return $this->userRepository->deleteById($id);
    }

    /**
     * @inheritDoc
     */
    public function deleteByUuid(string $uuid)
    {
        return $this->userRepository->deleteByUuid($uuid);
    }
}
