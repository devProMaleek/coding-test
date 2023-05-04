<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\RandomUserController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Api\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('auth/login', [AuthController::class, 'signin']);
Route::post('auth/register', [AuthController::class, 'signup']);

Route::get('email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify'); // Make sure to keep this as your route name

Route::post('sendPasswordResetLink', [ResetPasswordController::class, 'sendEmail']);
Route::post('reset-password', [ForgotPasswordController::class, 'changePassword']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
    Route::post('auth/logout', [AuthController::class, 'logoutUser']);
    Route::get('/random-user', [RandomUserController::class, 'index']);
});
