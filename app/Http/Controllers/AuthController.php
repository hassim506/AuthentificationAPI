<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);
        $user = User::create($fields);

        //creation du token
        $token = $user->createToken($request->name);

        return [
            'user' => $user,
            "token" => $token->plainTextToken
        ];

    }

    public function login(Request $request)
    {
         $request->validate([

            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Mot de passe incorrect'
            ], 401);
        }
        $token = $user->createToken($user->name);
        return [
            'user' => $user,
            "token" => $token->plainTextToken
        ];
    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return[
            'message' => 'Vous vous êtes deconnecter avec succées'
        ];
    }
}
