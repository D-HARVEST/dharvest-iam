<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Passport\ClientRepository;
use App\Models\User;

class MyOauthTest extends TestCase
{
    use RefreshDatabase;

    public function test_bad_redirect_uri()
    {
        $client = (new ClientRepository)->createAuthorizationCodeGrantClient('Test Client', ['location.d-harvest.com://auth']);

        // Pass a MISMATCHING redirect_uri
        $authUrl = '/oauth/authorize?client_id=' . $client->id . '&redirect_uri=http://bad-uri.com&response_type=code&scope=&state=123&code_challenge=1234567890123456789012345678901234567890123&code_challenge_method=S256';

        $user = User::factory()->create(['password' => bcrypt('password')]);

        // First we are logged in
        $this->actingAs($user);

        // We visit authorize with a bad URI
        $response = $this->get($authUrl);
        dd($response->getContent());
        $response->assertStatus(400);
    }
}
