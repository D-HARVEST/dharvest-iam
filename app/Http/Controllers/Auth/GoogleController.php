<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Mail\OtpMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {

        if (request()->has('client_id')) {
            session(['oauth_params' => request()->query()]);
        }
        return Socialite::driver('google')->redirect();
    }

    // public function handleGoogleCallback()
    // {
    //     $googleUser = Socialite::driver('google')->stateless()->user();

    //     // Vérifie si l'utilisateur existe déjà
    //     $user = User::where('email', $googleUser->email)->first();

    //     if ($user) {
    //         // L'utilisateur existe → connexion directe
    //         auth()->login($user);
    //         return redirect()->route('home')
    //             ->with('message', 'Connexion réussie via Google');
    //     }

    //     // L'utilisateur n'existe pas → création + OTP
    //     $user = User::create([
    //         'email' => $googleUser->email,
    //         'password' => Hash::make(uniqid()),
    //     ]);

    //     // Générer et envoyer l'OTP
    //     // $otp = rand(100000, 999999);
    //     // Cache::put('otp_'.$user->id, $otp, 300); // 5 min
    //     // Mail::to($user->email)->send(new OtpMail($otp));

    //     // Déconnexion pour forcer l'OTP
    //     auth()->logout();

    //     // Redirection vers la page OTP
    //     return redirect()->route('otp', ['user_id' => $user->id])
    //         ->with('message', 'Inscription réussie via Google. Vérifiez votre email pour l’OTP.');
    // }
    // static function redirectForFlutter(User $user)
    // {
    //     if (Session::has('redirect_to')) {
    //         $redirectTo = Session::get('redirect_to');
    //         $url = Session::forget('redirect_to');
    //         $token = $user->createToken('Google')->plainTextToken;
    //         $redirectTo .= "?token=$token";
    //         // Session::forget('redirect_to');
    //         // auth()->login($user);
    //         // return response('', 302)
    //         //     ->header('Location', $redirectTo);
    //         // return redirect()->away($redirectTo);
    //         return view('auth.google-redirect', ['redirect_url' => $redirectTo, "user" => $user]);
    //     }
    // }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();


        $user = User::where('email', $googleUser->email)->first();

        if (!$user) {

            $user = User::create([
                'email' => $googleUser->email,
                'name' => $googleUser->name ?? '',
                "lastname" => '',
                'password' => Hash::make(uniqid()),
                'email_verified_at' => now(),
                'google_id' => $googleUser->id,
                "photo" => $googleUser->avatar,
            ]);

        } else {
            // Si l'utilisateur existe déjà , on le met à jour

            $user->update([
                'email_verified_at' => now(),
                'google_id' => $googleUser->id,
                'photo' => $googleUser->avatar,
            ]);

        }



        auth()->login($user);

        if (session()->has('oauth_params')) {
            $params = session('oauth_params');
            session()->forget('oauth_params');

            return redirect()->route('passport.authorizations.authorize', $params)
                ->with('message', 'Connexion réussie via Google');
        }

        return redirect()->intended(route('home'))
            ->with('user', $user)
            ->with('message', 'Connexion réussie via Google');
    }
}
