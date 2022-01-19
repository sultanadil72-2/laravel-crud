<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MobileAuthController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function register(Request $request): Response
    {
        $fields = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ]);

        User::create([
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
        ]);

        return response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function login(Request $request): Response
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        $response = [
            'token' => $token
        ];

        return response($response, 201);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request): Response
    {
        $user = auth()->user();

        if (!$user) {
            return response('', Response::HTTP_UNAUTHORIZED);
        }

        if ($request->logout_eveywhere) {
            // Revoke all tokens....
            $user->tokens()->delete();
        } else {
            // Revoke the token that was used to authenticate the current request...
            $user->currentAccessToken()->delete();
        }

        return response('', Response::HTTP_NO_CONTENT);
    }
}
