<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;

class IntendedUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_oauth_login_flow()
    {
        // 1. Création du client OAuth (Public pour Flutter, donc confidential = false)
        $client = (new ClientRepository)->createAuthorizationCodeGrantClient(
            'Test Client',
            ['location.d-harvest.com://auth'],
            false // <--- NON CONFIDENTIEL POur une app mobile SPA / Flutter
        );

        // 2. Génération du PKCE (Verifier et Challenge) dynamiquement
        $codeVerifier = Str::random(128); // Le Verifier doit faire entre 43 et 128 caractères !
        $codeChallenge = strtr(rtrim(base64_encode(hash('sha256', $codeVerifier, true)), '='), '+/', '-_');

        $authUrl = '/oauth/authorize?' . http_build_query([
            'client_id' => $client->id,
            'redirect_uri' => 'location.d-harvest.com://auth',
            'response_type' => 'code',
            'scope' => '',
            'state' => '123',
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ]);
        
        $response1 = $this->get($authUrl);
        $response1->assertRedirect(); // Redirection vers /login gérée par l'app

        $user = User::factory()->create(['password' => bcrypt('password')]);

        // 3. Connexion de l'utilisateur
        $response2 = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response2->assertRedirectContains('oauth/authorize');

        // 4. Suivre la redirection pour générer le Code d'autorisation
        $this->actingAs($user);
        $redirectUrl = $response2->headers->get('Location');
        $response3 = $this->get($redirectUrl);
        
        $response3->assertRedirect();
        $finalRedirect = $response3->headers->get('Location');
        
        // 5. Extraction du "code" d'autorisation depuis l'URL de l'App
        $parts = parse_url($finalRedirect);
        parse_str($parts['query'] ?? '', $query);
        $this->assertArrayHasKey('code', $query, "Le code OAuth n'est pas présent dans la redirection finale");
        
        $authorizationCode = $query['code'];

        // 6. Échange du code + verifier contre le JWT (Jeton d'accès)
        $tokenResponse = $this->postJson('/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => $client->id,
            // 'client_secret' => $client->secret, // Passport Mobile App (Public Client) without secret
            'redirect_uri' => 'location.d-harvest.com://auth',
            'code' => $authorizationCode,
            'code_verifier' => $codeVerifier,
        ]);
        
        if ($tokenResponse->getStatusCode() !== 200) {
            $tokenResponse->dump();
        }

        $tokenResponse->assertStatus(200);

        // 7. Affichage et dump du JWT !
        $jwtData = $tokenResponse->json();
        
        echo "\n================= JWT ACCESS TOKEN =================\n";
        echo $jwtData['access_token'] ?? 'ERROR';
        echo "\n====================================================\n";

        // Assert final
        $this->assertArrayHasKey('access_token', $jwtData);
    }
}
