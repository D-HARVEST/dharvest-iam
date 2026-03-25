<?php
namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
class IntendedUrlGuestTest extends TestCase
{
    use RefreshDatabase;
    public function test_guest_intended_lost()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        
        $this->get('/dashboard');
        
        $response2 = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);
        
        $response2->assertRedirect('/dashboard');
    }
}
