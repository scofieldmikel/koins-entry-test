<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shared\UserController;

Route::group(['prefix' => 'v1'], function () {
    Route::post('register', [\App\Http\Controllers\Auth\RegistrationController::class, 'store']);
    Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);

    Route::post('forgot', [\App\Http\Controllers\Auth\ForgetPasswordController::class, 'resetPassword']);
    Route::post('change', [\App\Http\Controllers\Auth\ForgetPasswordController::class, 'changePassword']);

    Route::group(['middleware' => ['auth:sanctum']], function () {

        Route::group(['prefix' => 'dashboard'], function () {
            Route::post('/upload-image', [\App\Http\Controllers\Dashboard\ImageController::class, 'UploadImage']);
            Route::post('/update-profile', [\App\Http\Controllers\Dashboard\DashboardController::class, 'updateProfile']);
            Route::get('/user-profile', [\App\Http\Controllers\Dashboard\DashboardController::class, 'getProfile']);
        });

        Route::group(['prefix' => 'email'], function () {
            Route::post('verify', [\App\Http\Controllers\BeforeDashboard\VerifyEmailController::class, 'verify']);
            Route::post('resend', [\App\Http\Controllers\BeforeDashboard\VerifyEmailController::class, 'resend']);
            Route::post('change-email', [\App\Http\Controllers\BeforeDashboard\VerifyEmailController::class, 'changeEmail']);
        });

        Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout']);
    });

    Route::group(['prefix' => 'shared'], function () {
        Route::get('/user-email/{email}', [UserController::class, 'fetchDetailsByEmail']);
        Route::get('/user-id/{id}', [UserController::class, 'fetchDetailsById']);
    });

});
