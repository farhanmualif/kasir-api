<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class PasswordResetController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 422);
        }

        // Generate token
        $token = Str::random(6);

        try {
            // Simpan token
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now()
                ]
            );

            $user = \App\Models\User::where('email', $request->email)->first();
            $user->notify(new \App\Notifications\ResetPasswordNotification($token));

            return response()->json([
                'status' => 'success',
                'message' => 'Kode verifikasi telah dikirim ke email Anda'
            ]);
        } catch (\Exception $e) {
            // Tambahkan detail error untuk debugging
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim kode verifikasi',
                'error' => $e->getMessage() // Tambahkan ini untuk melihat error detail
            ], 500);
        }
    }

    public function verifyToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string|min:6|max:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 422);
        }

        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode verifikasi tidak valid'
            ], 400);
        }

        // Token valid dan belum expired (1 jam)
        if (now()->diffInHours($passwordReset->created_at) > 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode verifikasi sudah kadaluarsa'
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Kode verifikasi valid'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string|min:6|max:6',
            'password' => 'required|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 422);
        }

        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode verifikasi tidak valid'
            ], 400);
        }

        $user = \App\Models\User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token yang sudah digunakan
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil direset'
        ]);
    }
}
