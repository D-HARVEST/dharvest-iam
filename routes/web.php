<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\OauthClientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;



Route::get('/login/google', [GoogleController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/login/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::redirect('/', '/profile')->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // Impersonation
    Route::get('users-stop-impersonate', [UserController::class, 'stopImpersonate'])->name('users.stop-impersonate');

});

Route::middleware(['auth', 'role:Super-admin'])->group(function () {
    Route::resource('roles', App\Http\Controllers\RoleController::class);
    Route::post('roles/{role}/permissions', [App\Http\Controllers\RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::resource('users', UserController::class);
    Route::post('users/{user}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');

    Route::resource('oauth-clients', OauthClientController::class)->parameters(['oauth-clients' => 'oauthClient']);
    Route::patch('oauth-clients/{oauthClient}/toggle-revoke', [OauthClientController::class, 'toggleRevoke'])->name('oauth-clients.toggle-revoke');
    Route::patch('oauth-clients/{oauthClient}/regenerate-secret', [OauthClientController::class, 'regenerateSecret'])->name('oauth-clients.regenerate-secret');
});
Route::get('/oauth/keys', function () {
    $publicKey = File::get(storage_path('oauth-public.key'));

    // Extraction des paramètres RSA depuis la clé PEM
    $keyResource = openssl_pkey_get_public($publicKey);
    $details = openssl_pkey_get_details($keyResource);

    // Fonction d'encodage spécifique à la norme JWK (Base64Url)
    $base64UrlEncode = function ($data) {
        return str_replace(['+', '/'], ['-', '_'], rtrim(base64_encode($data), '='));
    };

    // Construction de l'objet conforme JWKS
    $jwk = [
        'keys' => [
            [
                'kty' => 'RSA',       // Type de clé (RSA)
                'alg' => 'RS256',     // Algorithme
                'use' => 'sig',       // Usage (Signature)
                'kid' => '1',         // Key ID (facultatif mais recommandé)
                'n' => $base64UrlEncode($details['rsa']['n']),
                'e' => $base64UrlEncode($details['rsa']['e']),
            ]
        ]
    ];

    return response()->json($jwk);
});

require __DIR__ . '/auth.php';
