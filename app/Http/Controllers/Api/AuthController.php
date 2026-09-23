<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Nette\Schema\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();

        if($user == null || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $user->getRoleNames(),
            ]
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $invite = Invite::findOrFail($request->invite_id);

        $data = $request->validated();

        $newUser = User::create([
            'name' => $data['name'],
            'email' => $invite->email,
            'password' => Hash::make($data['password']),
            'default_store_value_limit' => $invite->value_limit,
        ]);

        $newUser->assignRole('admin');

        $invite->used_at = now();
        $invite->save();

        $token = $newUser->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $newUser->id,
                'name' => $newUser->name,
                'email' => $newUser->email,
                'roles' => $newUser->getRoleNames(),
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
