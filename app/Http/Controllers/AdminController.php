<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function login(Request $request){
        $incomingFields = $request->validate([
            'loginname' => 'required',
            'loginpassword' => 'required'
        ]);

        // Cek Kredensial
        if (Auth::attempt(['name' => $incomingFields['loginname'], 'password' => $incomingFields['loginpassword']])){

            $user = Auth::user();

            // BUAT TOKEN (Solusi terbaik untuk React API)
            // Ini akan membuat token string panjang yang aman
            $token = $user->createToken('admin-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'user' => $user,
                'token' => $token // Kirim token ke frontend
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Login gagal. Username atau password salah.'
        ], 401);
    }

    public function checkAuth() {
        if(auth()->check()){
            return response()->json([
                'success' => true,
                'user' => auth()->user()
            ], 200);
        }
        return response()->json(['success' => false], 401);
    }

    public function logout(Request $request){
        // Hapus token user saat ini (Revoke)
        if(auth()->user()){
            auth()->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ], 200);
    }

    public function register(Request $request){
        $incomingFields = $request->validate([
            "name" => ["required", "min:3", "unique:users,name"],
            "password" => ["required", "min:8"],
            "email" => ["required", "email", "unique:users,email"]
        ]);

        $incomingFields['password'] = bcrypt($incomingFields['password']);
        $user = User::create($incomingFields);

        return response()->json([
            'success' => true,
            'message' => 'Admin berhasil didaftarkan',
            'data' => $user
        ], 201);
    }

    public function changePassword(Request $request)
    {
        $incomingFields = $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();

        // Verifikasi password lama
        if (!Hash::check($incomingFields['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini tidak sesuai'
            ], 400);
        }

        // Update password baru
        $user->password = Hash::make($incomingFields['new_password']);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah'
        ], 200);
    }
}
