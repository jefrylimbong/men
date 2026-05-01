<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required', // Bisa berisi email atau username
            'password' => 'required',
        ]);

        // Cek apakah input adalah email atau username
        $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($fieldType, $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kredensial tidak valid (Email/Username atau Password salah)',
            ], 401);
        }

        // if ($user->type !== 'user') {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Anda tidak memiliki akses ke aplikasi ini',
        //     ], 403);
        // }

        $token = $user->createToken('flutter-app')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil',
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->user()->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = $request->user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;

        if ($request->hasFile('avatar')) {
            // HAPUS FOTO LAMA JIKA ADA (Kecuali default)
            if ($user->avatar && !in_array($user->avatar, ['null', 'avatars/user.png', '/avatars/user.png'])) {
                $cleanPath = str_replace('storage/', '', $user->avatar);
                Storage::disk('public')->delete($cleanPath);
            }

            $file = $request->file('avatar');
            $filename = time() . '_user_' . $user->id . '.' . $file->getClientOriginalExtension();

            // Simpan ke storage/app/public/avatars
            $path = $file->storeAs('avatars', $filename, 'public');

            // Simpan path relatif di database (misal: avatars/filename.jpg)
            $user->avatar = $path;
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui',
            'data' => [
                'user' => $user,
                'avatar_url' => asset('storage/' . $user->avatar),
            ],
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password lama tidak cocok',
            ], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil diubah',
        ]);
    }
}
