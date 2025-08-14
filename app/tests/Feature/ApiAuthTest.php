<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

describe('API Authentication', function (): void {
    describe('POST /api/register', function (): void {
        it('registers a new user successfully', function (): void {
            $userData = [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ];

            $response = $this->postJson('/api/register', $userData);

            $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user' => ['id', 'name', 'email'],
                        'token',
                        'token_type',
                    ],
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'User registered successfully',
                    'data' => [
                        'user' => [
                            'name' => 'Test User',
                            'email' => 'test@example.com',
                        ],
                        'token_type' => 'Bearer',
                    ],
                ]);

            $this->assertDatabaseHas('users', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

            $this->assertDatabaseHas('personal_access_tokens', [
                'name' => 'auth-token',
            ]);
        });

        it('fails validation with invalid data', function (): void {
            $userData = [
                'name' => '',
                'email' => 'invalid-email',
                'password' => '123',
                'password_confirmation' => 'different',
            ];

            $response = $this->postJson('/api/register', $userData);

            $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors' => ['name', 'email', 'password'],
                ])
                ->assertJson([
                    'success' => false,
                    'message' => 'Validation failed',
                ]);
        });

        it('fails when email already exists', function (): void {
            User::factory()->create(['email' => 'existing@example.com']);

            $userData = [
                'name' => 'Test User',
                'email' => 'existing@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ];

            $response = $this->postJson('/api/register', $userData);

            $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'errors' => [
                        'email' => ['The email has already been taken.'],
                    ],
                ]);
        });
    });

    describe('POST /api/login', function (): void {
        it('logs in user successfully with valid credentials', function (): void {
            $user = User::factory()->create([
                'email' => 'test@example.com',
                'password' => bcrypt('password123'),
            ]);

            $loginData = [
                'email' => 'test@example.com',
                'password' => 'password123',
            ];

            $response = $this->postJson('/api/login', $loginData);

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user' => ['id', 'name', 'email'],
                        'token',
                        'token_type',
                    ],
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Login successful',
                    'data' => [
                        'user' => [
                            'email' => 'test@example.com',
                        ],
                        'token_type' => 'Bearer',
                    ],
                ]);
        });

        it('fails with invalid credentials', function (): void {
            $user = User::factory()->create([
                'email' => 'test@example.com',
                'password' => bcrypt('password123'),
            ]);

            $loginData = [
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
            ];

            $response = $this->postJson('/api/login', $loginData);

            $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ]);
        });

        it('fails validation with invalid data', function (): void {
            $loginData = [
                'email' => 'invalid-email',
                'password' => '',
            ];

            $response = $this->postJson('/api/login', $loginData);

            $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors' => ['email', 'password'],
                ])
                ->assertJson([
                    'success' => false,
                    'message' => 'Validation failed',
                ]);
        });
    });

    describe('POST /api/logout', function (): void {
        it('logs out authenticated user successfully', function (): void {
            $user = User::factory()->create();
            Sanctum::actingAs($user);

            $response = $this->postJson('/api/logout');

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Logged out successfully',
                ]);

            $this->assertDatabaseMissing('personal_access_tokens', [
                'tokenable_id' => $user->id,
            ]);
        });

        it('fails when user is not authenticated', function (): void {
            $response = $this->postJson('/api/logout');

            $response->assertStatus(401);
        });
    });
});
