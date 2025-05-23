<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreStoreRequest;
use App\Repositories\StoreRepository;
use App\Repositories\UserRepository;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class UserServiceImpl implements UserService
{
    public function __construct(public UserRepository $userRepository, public Logger $logging, public StoreRepository $storeRepository) {}

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
    public function login(LoginRequest $request)
    {
        try {
            $payload = $request->validated();
            $checkUser = $this->userRepository->findByEmail($payload['email']);

            if (!$checkUser) {
                throw new ApiException('user tidak ditemukan', 404);
            }

            if (Auth::attempt(['email' => $payload['email'], 'password' => $payload['password']])) {

                $success['token'] = $request->user()->createToken('token-name', ['server:update'])->plainTextToken;
                $success['uuid'] = $request->user()['uuid'];
                $success['user'] = $request->user()['name'];
                $success['email'] = $request->user()['email'];


                $this->logging->info('User logged in successfully with Email: ' . $request->user()['email']);

                return $success;
            } else {
                throw new ApiException('username atau password salah', 401);
            }
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function logout($token)
    {
        try {
            $this->logging->channel('info')->info('User logged out successfully with ID: ' . auth()->id());
            return $this->userRepository->revokeCurrentToken($token);
        } catch (\Throwable $th) {
            throw new ApiException($th->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function register(StoreStoreRequest $payload)
    {
        DB::beginTransaction();
        try {

            /** check any user in database */

            $payload["uuid"] = Uuid::uuid4();

            if ($this->userRepository->findByEmail($payload['email'])) {
                throw new ApiException('email sudah dipakai', 409);
            }

            /* insert user */

            $createUser = $this->userRepository->create([
                'name' => $payload['name'],
                'email' => $payload['email'],
                'password' => Hash::make($payload['password']),
            ]);

            /* create store */
            $createStore = $this->storeRepository->create([
                'uuid' => Uuid::uuid4()->toString(),
                'name' => "{$createUser->name}_store",
                'address' => $payload['address'],
                'user_id' => $createUser->id
            ]);

            $response = [
                'status' => true,
                'data' => [
                    'store' => $createStore->name,
                    'name' => $createUser->name,
                    'email' => $createUser->email,
                    'address' => $createStore->address
                ]
            ];

            $this->logging->info("User register successfully with Email: {$createUser->email}");

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

    /**
     * Get user with their store data
     * @param int $userId
     * @return mixed
     */
    public function getUserWithStore($userUuid)
    {
        try {
            $findUser = $this->userRepository->findById($userUuid);
            // dd($userUuid);
            if (!$findUser) {
                throw new ApiException('user tidak ditemukan', 404);
            }

            $user = $this->userRepository->getById($userUuid);
            // dd($userUuid);

            // Get store data
            $store = $this->storeRepository->findStoreByUserUuid($userUuid);

            // Combine user and store data
            $user->store = $store;

            return $user;
        } catch (\Throwable $th) {
            $this->logging->error("Error getting user with store: {$th->getMessage()}");
            throw new ApiException($th->getMessage());
        }
    }
}
