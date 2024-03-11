<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LogUserRequest;
use App\Http\Requests\RegisterUser;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(RegisterUser $request)
    {
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password, [
                'rounds' => 12
            ]);
            $user->save();

            return response()->json([
                'status_code' => 200,
                'status_message' => 'Berhasil registrasi user',
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function login(LogUserRequest $request)
    {
        if (auth()->attempt($request->only(['email', 'password']))) {
            $user = auth()->user();
            $token = $user->createToken($user->email)->plainTextToken;

            return response()->json([
                'status_code' => 200,
                'status_message' => 'Berhasil login',
                'user' => $user,
                'token' => $token,
            ]);
        } else {
            return response()->json([
                'status_code' => 403,
                'status_message' => 'Email dan password tidak valid',
            ]);
        }
    }
}
