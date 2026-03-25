<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class OauthAuthorizeScreenTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_it_redirects_to_register_screen(): void
    {
        $client = (new ClientRepository)->createAuthorizationCodeGrantClient(
            'Test Client',
            ['http://localhost']
        );

        $response = $this->get('/oauth/authorize?client_id=' . $client->id . '&redirect_uri=http://localhost&response_type=code&scope=&screen=register');
        $response->assertRedirectContains('register?client_id=' . $client->id);

        $response = $this->get('/oauth/authorize?client_id=' . $client->id . '&redirect_uri=http://localhost&response_type=code&scope=&screen=login');
        $response->assertRedirectContains('login?client_id=' . $client->id);

        $response = $this->get('/oauth/authorize?client_id=' . $client->id . '&redirect_uri=http://localhost&response_type=code&scope=&screen=google');
        $response->assertRedirectContains('login/google?client_id=' . $client->id);
    }

    public function test_it_adds_intended_url_to_session(): void
    {
        $client = (new ClientRepository)->createAuthorizationCodeGrantClient(
            'Test Client',
            ['http://localhost']
        );

        $url = '/oauth/authorize?client_id=' . $client->id . '&redirect_uri=http://localhost&response_type=code&scope=&screen=google';
        $response = $this->get($url);

        $response->assertSessionHas('url.intended', 'http://localhost' . $url);
    }
}
