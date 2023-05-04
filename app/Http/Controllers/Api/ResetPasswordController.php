<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\SendMailReset;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class ResetPasswordController extends BaseController
{

    public function sendEmail(Request $request)
    {
        if (!$this->validateEmail($request->email)) {
            return $this->sendError('Something went wrong', ['error' => "Email was not found in the Database"], 404);
        }

        $sendMail = $this->send($request->email);

        if ($sendMail) {
            return $this->sendResponse($sendMail, 'Reset email link sent successfully, please check your inbox"', 200);
        } else {
            return $this->sendError('Something went wrong', ['error' => "Error sending mail"], 400);
        }
    }


    public function send($email)
    {
        $token = $this->createToken($email);
        Mail::to($email)->send(new SendMailReset($token, $email));
    }


    public function createToken($email)
    {

        $oldToken = DB::table('password_resets')->where('email', $email)->first();

        if ($oldToken) {
            return $oldToken->token;
        }

        $token = Str::random(40);
        $this->saveToken($token, $email);
        return $token;
    }


    public function saveToken($token, $email)
    {
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
    }


    public function validateEmail($email)
    {
        return !!User::where('email', $email)->first();
    }
}
