<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreStoreRequest;
use App\Services\UserService;
use Illuminate\Http\Request;


class AuthController extends Controller
{

    public UserService $userServices;

    public function __construct(UserService $userServicess)
    {
        $this->userServices = $userServicess;
    }

    public function register(StoreStoreRequest $request)
    {
        $createUser = $this->userServices->register($request);
        return responseJson('Berhasil menambahkan data user', $createUser, true, 200);
    }


    public function login(LoginRequest $request)
    {
        $loginUser = $this->userServices->login($request);
        return responseJson("berhasil login", $loginUser, true, 202);
    }

    public function authenticated()
    {
        try {
            $auth = auth('sanctum')->check();
            if (!$auth) {
                return \responseJson("unauthenticated", null, false, 400);
            }
            return \responseJson("authenticated", null);
        } catch (\Throwable $th) {
            return \responseJson("server problem", "{$th->getMessage()}", false, 500);
        }
    }
    public function logout(Request $request)
    {
        try {
            $check_header = $request->header('Authorization');

            if ($check_header == null) {
                return responseJson('Token tidak ditemukan, silakan login terlebih dahulu', null, false, 404);
            }

            $token = $request->user()->currentAccessToken();

            if (!$token->exists()) {
                return responseJson('Token tidak ditemukan', null, false, 404);
            }

            $deleteToken = $this->userServices->logout($token);

            if (!$deleteToken) {
                return responseJson('gagal melakukan logout', null, false, 404);
            }
            return responseJson("berhasil logout", null, true, 200);
        } catch (\Throwable $th) {
            return responseJson("terjadi kesalahan: {$th->getMessage()}", null, true, 200);
        }
    }
}
