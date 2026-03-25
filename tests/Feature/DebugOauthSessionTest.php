<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;
use App\Models\User;

class DebugOauthSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_oauth_params_saved_in_session()
    {
        $client = (new ClientRepository)->createAuthorizationCodeGrantClient(
            'Test Client', ['http://localhost/callback']
        );

        $authUrl = '/oauth/authorize?client_id=' . $client->id . '&redirect_uri=http://localhost/callback&response_type=code&scope=';
        
        $response1 = $this->get($authUrl);
        $response1->assertRedirect();
        
        // Check session after redirect
        $this->assertTrue(session()->has('oauth_params'), 'oauth_params not found in session');
    }
}
