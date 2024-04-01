<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\Events\TokenAuthenticated;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $payload = $request->all();

        $validate = Validator::make($payload, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ]);

        if ($validate->fails()) {
            return \response()->json([
                'status' => \false,
                'messgae' => $validate->errors(),
            ])->setStatusCode(300);
        }
        $payload['password'] = \bcrypt($payload['password']);

        $create_user = User::create($request->all());
        if (!$create_user) {
            return response()->json([
                'status' => \false,
                'messgae' => 'any problem when insert data',
            ]);
        }

        return \response()->json([
            'status' => \true,
            'data' => $create_user
        ])->setStatusCode(200);
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
