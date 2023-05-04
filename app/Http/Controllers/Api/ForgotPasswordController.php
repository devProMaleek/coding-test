<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends BaseController
{
    //

    public function changePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        $updatePassword = DB::table('password_resets')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])
            ->first();

        if (!$updatePassword) {
            return $this->sendError('Invalid token!.', ['error' => 'Validation Error'], 422);
        }

        $user = User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        $deleteToken = DB::table('password_resets')->where(['email' => $request->email])->delete();

        if ($user && $deleteToken) {
            return $this->sendResponse($user, 'Your password has been changed!', 200);
        } else {
            return $this->sendError('Something went wrong', ['error' => 'Error changing your password'], 422);
        }
    }
}
