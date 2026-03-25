<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;
use App\Models\User;

class IntendedUrlTest2 extends TestCase
{
    use RefreshDatabase;

    public function test_full_oauth_login_flow()
    {
        $client = (new ClientRepository)->createAuthorizationCodeGrantClient(
            'Test Client', ['http://localhost/callback']
        );

        $authUrl = '/oauth/authorize?client_id=' . $client->id . '&redirect_uri=http://localhost/callback&response_type=code&scope=';
        
        $this->get($authUrl);
        $this->get(route('login'));
        
        $user = User::factory()->create(['password' => bcrypt('password')]);
        
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);
        
        $response->assertRedirectContains('oauth/authorize');
    }
}
