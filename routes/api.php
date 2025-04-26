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

        Route::group(['prefix' => 'campaign', 'middleware' => ['IsProfileUpdated']], function () {
            Route::post('/create-campaign', [\App\Http\Controllers\Campaign\CampaignController::class, 'createCampaign']);
            Route::get('/get-campaigns', [\App\Http\Controllers\Campaign\CampaignController::class, 'getCampaigns']);
            Route::get('/get-campaign/{campaign}', [\App\Http\Controllers\Campaign\CampaignController::class, 'getCampaignDetails']);
            Route::put('/update-campaign/{campaign}', [\App\Http\Controllers\Campaign\CampaignController::class, 'updateStatus']);
            Route::patch('/campaigns/{campaign}/location', [\App\Http\Controllers\Campaign\CampaignController::class, 'modifyLocation']);
            Route::patch('/campaigns/{campaign}/add-location', [\App\Http\Controllers\Campaign\CampaignController::class, 'addLocationToExistingCampaign']);
        });

        Route::group(['prefix' => 'location', 'middleware' => ['IsProfileUpdated']], function () {
            Route::get('/get-location/{location}', [\App\Http\Controllers\Campaign\LocationController::class, 'getLocationDetails']);
            Route::get('/get-locations', [\App\Http\Controllers\Campaign\LocationController::class, 'getLocations']);
            Route::post('/add-location', [\App\Http\Controllers\Campaign\LocationController::class, 'addLocation']);
            Route::put('/update-location/{location}', [\App\Http\Controllers\Campaign\LocationController::class, 'updateLocation']);
        });

        Route::group(['prefix' => 'status', 'middleware' => ['IsProfileUpdated']], function () {
            Route::post('add-campaign-status', [\App\Http\Controllers\Campaign\StatusController::class, 'addCampaignStatus']);
            Route::get('get-campaign-statuses', [\App\Http\Controllers\Campaign\StatusController::class, 'getCampaignStatus']);
            Route::get('get-campaign-status/{status}', [\App\Http\Controllers\Campaign\StatusController::class, 'getCampaignStatusDetails']);
            Route::put('update-campaign-status/{status}', [\App\Http\Controllers\Campaign\StatusController::class, 'updateCampaignStatus']);
        });

        Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout']);
    });

    Route::group(['prefix' => 'shared'], function () {
        Route::get('/user-email/{email}', [UserController::class, 'fetchDetailsByEmail']);
        Route::get('/user-id/{id}', [UserController::class, 'fetchDetailsById']);
    });

});
