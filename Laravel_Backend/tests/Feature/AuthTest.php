<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::create([
            'name' => 'Store Owner',
            'email' => 'owner@delivergate.com',
            'password' => Hash::make('Password123!'),
            'role' => UserRole::OWNER,
        ]);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'owner@delivergate.com',
            'password' => 'Password123!',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'token',
                    'token_type',
                    'user' => ['id', 'name', 'email', 'role'],
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'user' => [
                        'email' => 'owner@delivergate.com',
                        'role' => 'owner',
                    ],
                ],
            ]);
    }

    public function test_login_fails_with_invalid_password(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'owner@delivergate.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertUnauthorized()
            ->assertJson([
                'status' => 'error',
                'message' => 'Invalid email or password.',
            ]);
    }

    public function test_login_validation_requires_email_and_password(): void
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_authenticated_user_can_retrieve_profile(): void
    {
        $response = $this->actingAs($this->owner)
            ->getJson('/api/auth/me');

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'email' => 'owner@delivergate.com',
                    'role' => 'owner',
                ],
            ]);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $token = $this->owner->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout');

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => 'Logged out successfully.',
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_unauthenticated_request_to_protected_route_is_denied(): void
    {
        $response = $this->getJson('/api/items');

        $response->assertUnauthorized();
    }
}
