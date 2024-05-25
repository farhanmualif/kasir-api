<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class AuthController extends Controller
{
    public function store(UserRegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $validated['password'] = Hash::make($validated['password']);
            $validated['uuid'] = Uuid::uuid4();
            $userCreated = User::create($validated);

            DB::commit();

            $data = [
                'user' => $userCreated,
            ];

            return responseJson("Berhasil mendaftarkan user", $data);
        } catch (\Throwable $th) {
            DB::rollBack();
            return responseJson("Gagal mendaftarkan user, {$th->getMessage()} file: {$th->getFile()} line: {$th->getLine()}", null, false, 500);
        }
    }

    public function login(Request $request)
    {
        $payload = $request->all();
        $check_user = User::where('email', $payload['email'])->first();
        if (!$check_user) {
            return responseJson('user tidak ditemukan', null, false, 404);
        }
        if (Auth::attempt(['email' => $payload['email'], 'password' => $payload['password']])) {

            $success['token'] = $request->user()->createToken('token-name', ['server:update'])->plainTextToken;
            $success['uuid'] = $request->user()['uuid'];
            $success['user'] = $request->user()['name'];
            $success['email'] = $request->user()['email'];

            return responseJson('berhasil login', $success);
        } else {
            return responseJson('terjadi kesalahan', null, true, 500);
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
            if ($check_header == \null) {
                return responseJson('anda belum login', null, false, 404);
            }

            $user = auth('sanctum')->user();
            $user->tokens()->delete();

            return responseJson("berhasil logout", null, true, 200);
        } catch (\Throwable $th) {
            return responseJson("terjadi kesalahan: {$th->getMessage()}", null, true, 200);
        }
    }
}
