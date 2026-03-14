<?php

namespace Tests\Feature\M2M;

use App\Models\User;
use App\Notifications\NewMemberCredentials;
use App\Notifications\PasswordChangeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    // ───────────────────────────────────────────────
    // Helpers
    // ───────────────────────────────────────────────

    /** Bypasse le middleware client.credentials (authentification M2M OAuth2). */
    private function asM2MClient(): static
    {
        return $this->withoutMiddleware(\Laravel\Passport\Http\Middleware\CheckToken::class);
    }

    /**
     * Injecte un mock du guard 'api' de Passport dans le conteneur IoC.
     * Utilisé uniquement pour les tests de getUserByJwt qui reconstituent
     * manuellement une requête HTTP pour interroger le guard.
     */
    private function mockApiGuard(?User $user): void
    {
        $guard = \Mockery::mock(\Illuminate\Contracts\Auth\Guard::class);
        $guard->shouldReceive('setRequest')->andReturnSelf();
        $guard->shouldReceive('user')->andReturn($user);

        $authManager = \Mockery::mock(\Illuminate\Auth\AuthManager::class);
        $authManager->shouldReceive('guard')->with('api')->andReturn($guard);
        $authManager->shouldAllowMockingProtectedMethods();

        $this->app->instance('auth', $authManager);
        $this->app->instance(\Illuminate\Contracts\Auth\Factory::class, $authManager);
    }

    // ───────────────────────────────────────────────
    // GET /api/m2m/get-user-by-jwt
    // ───────────────────────────────────────────────

    public function test_get_user_by_jwt_returns_401_when_token_is_missing(): void
    {
        $response = $this->asM2MClient()->getJson('/api/m2m/get-user-by-jwt');

        $response->assertStatus(401)
                 ->assertJsonFragment(['error' => 'No access token provided']);
    }

    public function test_get_user_by_jwt_returns_401_for_invalid_token(): void
    {
        $this->mockApiGuard(null);

        $response = $this->asM2MClient()->getJson('/api/m2m/get-user-by-jwt?token=invalid-token');

        $response->assertStatus(401)
                 ->assertJsonFragment(['error' => 'Invalid token']);
    }

    public function test_get_user_by_jwt_returns_user_for_valid_token(): void
    {
        $user = User::factory()->create(['fcm_token' => 'fcm-abc']);

        $this->mockApiGuard($user);

        $response = $this->asM2MClient()->getJson('/api/m2m/get-user-by-jwt?token=valid-token');

        $response->assertOk()
                 ->assertJsonFragment([
                     'id'        => $user->id,
                     'uid'       => $user->uid,
                     'email'     => $user->email,
                     'fcm_token' => 'fcm-abc',
                 ]);
    }

    // ───────────────────────────────────────────────
    // POST /api/m2m/add-new-user
    // ───────────────────────────────────────────────

    public function test_add_new_user_requires_uid(): void
    {
        Notification::fake();

        $response = $this->asM2MClient()->postJson('/api/m2m/add-new-user', [
            'name'  => 'Alice',
            'email' => 'alice@example.com',
        ]);

        $response->assertStatus(422)
                 ->assertJsonPath('errors.uid', fn ($v) => !empty($v));
    }

    public function test_add_new_user_requires_name(): void
    {
        Notification::fake();

        $response = $this->asM2MClient()->postJson('/api/m2m/add-new-user', [
            'uid'   => 'test-uid-001',
            'email' => 'alice@example.com',
        ]);

        $response->assertStatus(422)
                 ->assertJsonPath('errors.name', fn ($v) => !empty($v));
    }

    public function test_add_new_user_requires_email(): void
    {
        Notification::fake();

        $response = $this->asM2MClient()->postJson('/api/m2m/add-new-user', [
            'uid'  => 'test-uid-001',
            'name' => 'Alice',
        ]);

        $response->assertStatus(422)
                 ->assertJsonPath('errors.email', fn ($v) => !empty($v));
    }

    public function test_add_new_user_rejects_invalid_email(): void
    {
        Notification::fake();

        $response = $this->asM2MClient()->postJson('/api/m2m/add-new-user', [
            'uid'   => 'test-uid-001',
            'name'  => 'Alice',
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422)
                 ->assertJsonPath('errors.email', fn ($v) => !empty($v));
    }

    public function test_add_new_user_rejects_duplicate_email(): void
    {
        Notification::fake();

        User::factory()->create(['email' => 'alice@example.com']);

        $response = $this->asM2MClient()->postJson('/api/m2m/add-new-user', [
            'uid'   => 'test-uid-new',
            'name'  => 'Alice',
            'email' => 'alice@example.com',
        ]);

        $response->assertStatus(422)
                 ->assertJsonPath('errors.email', fn ($v) => !empty($v));
    }

    public function test_add_new_user_rejects_duplicate_uid(): void
    {
        Notification::fake();

        User::factory()->create(['uid' => 'existing-uid']);

        $response = $this->asM2MClient()->postJson('/api/m2m/add-new-user', [
            'uid'   => 'existing-uid',
            'name'  => 'Bob',
            'email' => 'bob@example.com',
        ]);

        $response->assertStatus(422)
                 ->assertJsonPath('errors.uid', fn ($v) => !empty($v));
    }

    public function test_add_new_user_creates_user_and_returns_resource(): void
    {
        Notification::fake();

        $response = $this->asM2MClient()->postJson('/api/m2m/add-new-user', [
            'uid'       => 'uid-alice-001',
            'name'      => 'Alice',
            'email'     => 'alice@example.com',
            'fcm_token' => 'fcm-token-alice',
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'uid'       => 'uid-alice-001',
                     'name'      => 'Alice',
                     'email'     => 'alice@example.com',
                     'fcm_token' => 'fcm-token-alice',
                 ]);

        $this->assertDatabaseHas('users', [
            'uid'   => 'uid-alice-001',
            'email' => 'alice@example.com',
        ]);
    }

    public function test_add_new_user_sends_notifications(): void
    {
        Notification::fake();

        $this->asM2MClient()->postJson('/api/m2m/add-new-user', [
            'uid'   => 'uid-bob-002',
            'name'  => 'Bob',
            'email' => 'bob@example.com',
        ]);

        $user = User::where('email', 'bob@example.com')->first();

        Notification::assertSentTo($user, PasswordChangeNotification::class);
        Notification::assertSentTo($user, NewMemberCredentials::class);
    }

    // ───────────────────────────────────────────────
    // POST /api/m2m/update-user/{uid}
    // ───────────────────────────────────────────────

    public function test_update_user_returns_404_for_unknown_uid(): void
    {
        $response = $this->asM2MClient()->postJson('/api/m2m/update-user/uid-inexistant');

        $response->assertStatus(404)
                 ->assertJsonFragment(['error' => 'User not found']);
    }

    public function test_update_user_returns_422_when_no_fields_provided(): void
    {
        $user = User::factory()->create();

        $response = $this->asM2MClient()->postJson("/api/m2m/update-user/{$user->uid}", []);

        $response->assertStatus(422)
                 ->assertJsonFragment(['error' => 'No fields to update']);
    }

    public function test_update_user_updates_fcm_token(): void
    {
        $user = User::factory()->create(['fcm_token' => 'old-token']);

        $response = $this->asM2MClient()->postJson("/api/m2m/update-user/{$user->uid}", [
            'fcm_token' => 'new-token',
        ]);

        $response->assertOk()
                 ->assertJsonFragment(['fcm_token' => 'new-token']);

        $this->assertDatabaseHas('users', [
            'uid'       => $user->uid,
            'fcm_token' => 'new-token',
        ]);
    }

    public function test_update_user_can_clear_fcm_token(): void
    {
        $user = User::factory()->create(['fcm_token' => 'some-token']);

        $response = $this->asM2MClient()->postJson("/api/m2m/update-user/{$user->uid}", [
            'fcm_token' => null,
        ]);

        $response->assertOk()
                 ->assertJsonFragment(['fcm_token' => null]);

        $this->assertDatabaseHas('users', [
            'uid'       => $user->uid,
            'fcm_token' => null,
        ]);
    }

    // ───────────────────────────────────────────────
    // DELETE /api/m2m/delete-user/{uid}
    // ───────────────────────────────────────────────

    public function test_delete_user_returns_404_for_unknown_uid(): void
    {
        $response = $this->asM2MClient()->deleteJson('/api/m2m/delete-user/uid-inexistant');

        $response->assertStatus(404)
                 ->assertJsonFragment(['error' => 'User not found']);
    }

    public function test_delete_user_removes_user_from_database(): void
    {
        $user = User::factory()->create();

        $response = $this->asM2MClient()->deleteJson("/api/m2m/delete-user/{$user->uid}");

        $response->assertOk()
                 ->assertJsonFragment([
                     'message' => 'User deleted successfully',
                     'uid'     => $user->uid,
                 ]);

        $this->assertDatabaseMissing('users', ['uid' => $user->uid]);
    }

    // ───────────────────────────────────────────────
    // POST /api/m2m/regenerate-password/{uid}
    // ───────────────────────────────────────────────

    public function test_regenerate_password_returns_404_for_unknown_uid(): void
    {
        $response = $this->asM2MClient()->postJson('/api/m2m/regenerate-password/uid-inexistant');

        $response->assertStatus(404)
                 ->assertJsonFragment(['error' => 'User not found']);
    }

    public function test_regenerate_password_returns_success_and_sends_notifications(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $oldPasswordHash = $user->password;

        $response = $this->asM2MClient()->postJson("/api/m2m/regenerate-password/{$user->uid}");

        $response->assertOk()
                 ->assertJsonFragment([
                     'message' => 'Password regenerated successfully',
                     'uid'     => $user->uid,
                 ]);

        $this->assertNotEquals($oldPasswordHash, $user->fresh()->password);

        Notification::assertSentTo($user, PasswordChangeNotification::class);
        Notification::assertSentTo($user, NewMemberCredentials::class);
    }

    // ───────────────────────────────────────────────
    // Middleware : accès refusé sans client credentials
    // ───────────────────────────────────────────────

    public function test_all_routes_require_client_credentials_middleware(): void
    {
        $user = User::factory()->create();

        $this->getJson('/api/m2m/get-user-by-jwt')->assertStatus(401);
        $this->postJson('/api/m2m/add-new-user')->assertStatus(401);
        $this->postJson("/api/m2m/update-user/{$user->uid}")->assertStatus(401);
        $this->deleteJson("/api/m2m/delete-user/{$user->uid}")->assertStatus(401);
        $this->postJson("/api/m2m/regenerate-password/{$user->uid}")->assertStatus(401);
    }
}
