<?php

namespace App\Http\Controllers\M2M;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    public function getTokenForSensor(Request $request)
    {
        $request->validate([
            'chip_id' => 'required|string|max:255',
        ]);

        $sensor = \App\Models\Sensor::firstOrCreate(
            ['chip_id' => $request->chip_id],
            ['last_token_generated_at' => now()]
        );

        // Générer un token pour le capteur
        $tokenResult = $sensor->createToken('Sensor Access Token');
        $token = $tokenResult->accessToken;

        // Mettre à jour la date de dernière génération de token
        $sensor->last_token_generated_at = now();
        $sensor->save();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => now()->addYears(10)->toDateTimeString(),
        ]);
    }
}
