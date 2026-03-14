<?php

// ✅ Routes M2M — authentification par client credentials

use App\Http\Controllers\M2M\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('m2m')->middleware("client.credentials")->group(function () {
    Route::get('/get-user-by-jwt', [UserController::class, 'getUserByJwt']);
    Route::post('/add-new-user', [UserController::class, 'addNewUser']);
    Route::post('/update-user/{uid}', [UserController::class, 'updateUser']);
    Route::delete('/delete-user/{uid}', [UserController::class, 'deleteUser']);
    Route::post('/regenerate-password/{uid}', [UserController::class, 'regeneratePassword']);
});

