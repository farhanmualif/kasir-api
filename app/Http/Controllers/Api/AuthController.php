<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
            return \response()->json([
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
        if (Auth::attempt(['email' => $payload['email'], 'password' => $payload['password']])) {
            $success['token'] = $request->user()->createToken('token-name', ['server:update'])->plainTextToken;
            $success['uuid'] = $request->user()['uuid'];
            $success['user'] = $request->user()['name'];
            $success['email'] = $request->user()['email'];
            return \response()->json([
                'status' => \true,
                'data' => $success
            ])->setStatusCode(200);
        } else {
            return \response()->json([
                'status' => \false,
                'data' => 'username or password not found',
            ])->setStatusCode(404);
        }
    }

    public function logout(Request $request)
    {
        // check any token ?
        $check_header = $request->header('Authorization');
        if ($check_header == \null) {
            return \response()->json([
                'status' => \false,
                'message' => 'token not found'
            ])->setStatusCode(500);
        }

        $user = \auth('sanctum')->user();
        $user->tokens()->delete();

        return \response()->json([
            'status' => \true,
            'message' => 'token deleted'
        ])->setStatusCode(500);
    }
}
