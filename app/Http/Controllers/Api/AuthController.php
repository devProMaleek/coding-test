<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends BaseController
{
    //

    public function signin(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return $this->sendError('Error validation', $validateUser->errors(), 422);
            }

            if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return $this->sendError('Unauthorized.', ['error' => 'Email & Password does not match with our record.'], 401);
            }

            $user = User::where('email', $request->email)->first();

            $success['token'] =  $user->createToken('ACCESS_TOKEN')->plainTextToken;
            $success['name'] =  $user->name;
            return $this->sendResponse($success, 'User Logged In Successfully', 200);
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), ['error' => 'Server Error'], 500);
        }
    }

    public function signup(Request $request)
    {

        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required|string',
                    'email' => 'required|string|email|unique:users,email',
                    'password' => 'required|string',
                    'confirm_password' => 'required|same:password',
                ]
            );

            if ($validateUser->fails()) {
                return $this->sendError('Error validation', $validateUser->errors(), 422);
            }

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ];

            $user = User::create($userData);
            $user->sendEmailVerificationNotification();
            $success['token'] =  $user->createToken('ACCESS_TOKEN')->plainTextToken;
            $success['name'] =  $user->name;

            if ($user) {
                return $this->sendResponse($success, 'User created successfully.', 201);
            } else {
                return $this->sendError('Something went wrong', ['error' => 'Error creating user'], 422);
            }
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), ['error' => 'Server Error'], 500);
        }
    }

    /**
     * Logout The User
     * @param Request $request
     * @return User
     */
    public function logoutUser(Request $request)
    {
        try {
            $authUser = $request->user();
            if ($authUser) {
                $deleteToken = $request->user()->tokens()->delete();
            }

            if ($deleteToken) {
                return $this->sendResponse($deleteToken, 'User Logged Out Successfully', 200);
            } else {
                return $this->sendError('Error validation', ['error' => 'Server Error'], 500);
            }
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), ['error' => 'Server Error'], 500);
        }
    }
}
