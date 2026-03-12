<?php

// ✅ Routes M2M — authentification par client credentials

use App\Http\Controllers\M2M\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('m2m')->middleware("client.credentials")->group(function () {
    Route::get('/get-user-by-jwt', [UserController::class, 'getUserByJwt']);
});

