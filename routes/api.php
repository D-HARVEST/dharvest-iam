<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthTokenController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

// gRPC API endpoints exposed over HTTP/REST
Route::prefix('auth')->group(function () {
    Route::post('/verify-token', [AuthTokenController::class, 'verifyToken'])->name('auth.verify-token');
});

require __DIR__ . '/m2m.php';
