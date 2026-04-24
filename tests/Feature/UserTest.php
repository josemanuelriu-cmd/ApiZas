<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_basic_default_page()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
    public function test_login_successful(): void
    {
        $user = User::factory()
            ->withPassword('password')
            ->create([
                'email' => 'test@example.com',
            ]);
        $response = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'user']);
    }
    public function test_login_failed(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);
        $response->assertStatus(401);
    }
    public function test_logout_successful(): void
    {
        $user = User::factory()
            ->withPassword('password')
            ->create([
                'email' => 'test@example.com',
            ]);

        $response = $this->actingAs($user, 'api') 
                     ->postJson('/api/v1/logout');
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully'
            ]);
    }
    public function test_register_successful(): void
    {
        $payload = [
            'num_partner' => null,
            'nickname' => 'PruebasTest',
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'type' => 'junta',
            'registration_date' => now()->toDateString(),
            'withdrawal_date' => null,
            'telephone' => '123456777',
            'age' => 25,
            'language' => 'es',
        ];

        $response = $this->postJson('/api/v1/register', $payload);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'user' => ['id', 'num_partner', 'nickname', 'name', 'email', 'type', 'registration_date', 'withdrawal_date', 'created_at', 'updated_at'],
            'token'
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
            'name' => 'Test User'
        ]);
    }
}