<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends BaseController
{
    //

    public function verify($user_id, Request $request)
    {
        if (!$request->hasValidSignature()) {
            return $this->sendError("Invalid/Expired url provided.", [], 401);
        }

        $user = User::findOrFail($user_id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect()->to('/');
    }

    public function resend()
    {
        if (auth()->user()->hasVerifiedEmail()) {
            return $this->sendError("Email already verified.", [], 400);
        }

        $success = auth()->user()->sendEmailVerificationNotification();

        return $this->sendResponse($success, "Email verification link sent on your email", 200);
    }
}
