<?php

namespace App\Services;

use App\Repositories\StoreRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class UserServiceImpl implements UserService
{
    public function __construct(protected UserRepository $userRepository, protected StoreRepository $storeRepository)
    {
        $this->userRepository = $userRepository;
        $this->storeRepository = $storeRepository;
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

            $check_user = $this->userRepository->findByEmail($payload['email']);

            if (!$check_user) {
                return [
                    'status' => false,
                    'data' => 'email / password tidak ditemukan'
                ];
            }

            if (Auth::attempt(['email' => $payload['email'], 'password' => $payload['password']])) {

                $success['token'] = $request->user()->createToken('token-name', ['server:update'])->plainTextToken;
                $success['uuid'] = $request->user()['uuid'];
                $success['user'] = $request->user()['name'];
                $success['email'] = $request->user()['email'];

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

            if ($this->userRepository->findByEmail($payload['email'])) {
                return [
                    'status' => false,
                    'data' => null
                ];
            }

            /* insert user */
            $create_user = $this->userRepository->create($payload);

            /* create store */
            $create_store = $this->storeRepository->create([
                'uuid' => Uuid::uuid4()->toString(),
                'name' => $create_user->name . '_store',
                'address' => $payload['address'],
                'user_id' => $create_user->id
            ]);

            $response = [
                'store' => $create_store->name,
                'name' => $create_user->name,
                'email' => $create_user->email,
                'address' => $create_store->address
            ];

            DB::commit();
            return [
                'status' => true,
                'data' => $response
            ];
        } catch (\Illuminate\Database\QueryException $th) {
            DB::rollBack();
            return [
                'status' => false,
                'data' => $th
            ];
        }
    }
}
