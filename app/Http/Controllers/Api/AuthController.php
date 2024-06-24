<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreStoreRequest;
use App\Models\User;
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
        try {
            $validated = $request->validated();
            $createUser = $this->userServices->register($validated);

            if ($createUser['status'] == false) {
                return responseJson('Gagal menambahkan data user, email sudah ada.', null, false, 406);
            }

            return responseJson('Berhasil menambahkan data user', $createUser['data'], true, 200);
        } catch (\Exception $th) {

            // Generic exception handling
            return responseJson("Gagal menambahkan user, {$th->getMessage()} file: {$th->getFile()} line: {$th->getLine()}", null, false, 500);
        }
    }


    public function login(LoginRequest $request)
    {

        try {
            $payload = $request->validated();
            $loginUser = $this->userServices->login($payload, $request);

            if (!$loginUser) {
                return responseJson("email / password tidak ada", null, false, 401);
            }

            return responseJson("berhasil login", $loginUser['data'], true, 202);
        } catch (\Throwable $th) {
            return responseJson("Gagal melakukan login, {$th->getMessage()} file: {$th->getFile()} line: {$th->getLine()}", null, false, 500);
        }
    }

    public function checkAuth()
    {
        try {
            $auth = auth('sanctum')->check();
            if (!$auth) {
                return \responseJson("uauthenticated", null, false, 400);
            }
            return \responseJson("authenticated", null);
        } catch (\Throwable $th) {
            return false;
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
                return responseJson('Token tidak ditemukan, silakan login terlebih dahulu', null, false, 404);
            }

            $deleteToken = $this->userServices->logout($token);

            if (!$deleteToken) {
                return responseJson('tidak dapat logout', null, false, 404);
            }
            return responseJson("berhasil logout", null, true, 200);
        } catch (\Throwable $th) {
            return responseJson("terjadi kesalahan: {$th->getMessage()}", null, true, 200);
        }
    }
}
