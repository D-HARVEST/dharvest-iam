<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;
use App\Models\User;

class IntendedUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_oauth_login_flow()
    {
        $client = (new ClientRepository)->createAuthorizationCodeGrantClient(
            'Test Client', ['location.d-harvest.com://auth']
        );

        $authUrl = '/oauth/authorize?client_id=' . $client->id . '&redirect_uri=location.d-harvest.com%3A%2F%2Fauth&response_type=code&scope=&state=123&code_challenge=abc&code_challenge_method=S256';
        
        $response1 = $this->get($authUrl);
        if ($response1->status() !== 302) {
            dd($response1->getContent());
        }
        $response1->assertRedirect();
        
        $user = User::factory()->create(['password' => bcrypt('password')]);
        
        $response2 = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);
        
        $response2->assertRedirectContains('oauth/authorize');
    }
}
