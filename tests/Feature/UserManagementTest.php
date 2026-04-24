<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\Models\User;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;
    public function test_index_returns_all_users_for_admin()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $response = $this->get('/api/v1/users');
        $response->assertStatus(200);
        $response->assertJsonStructure(['*' => ['id', 'num_partner', 'nickname', 'name', 'type', 'registration_date', 'withdrawal_date', 'email', 'telephone', 'age', 'language', 'email_verified_at', 'created_at', 'updated_at']]);
    }

    public function test_index_returns_all_users_for_junta()
    {
        $user = User::factory()->junta()->create();
        Passport::actingAs($user);
        $response = $this->get('/api/v1/users');
        $response->assertStatus(200);
        $response->assertJsonStructure(['*' => ['id', 'num_partner', 'nickname', 'name', 'type', 'registration_date', 'withdrawal_date', 'email', 'telephone', 'age', 'language', 'email_verified_at', 'created_at', 'updated_at']]);
    }

    public function test_index_forbidden_for_partner()
    {
        $user = User::factory()->partner()->create();
        Passport::actingAs($user);
        $response = $this->get('/api/v1/users');
        $response->assertStatus(403);
    }

    public function test_index_forbidden_for_guest()
    {
        $user = User::factory()->guest()->create();
        Passport::actingAs($user);
        $response = $this->get('/api/v1/users');
        $response->assertStatus(403);
    }

    public function test_index_unauthenticated_returns_401()
    {
        $response = $this->get('/api/v1/users');
        $response->assertStatus(401);
    }

    public function test_show_returns_user_for_admin()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $response = $this->get("/api/v1/users/{$user->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'num_partner', 'nickname', 'name', 'type', 'registration_date', 'withdrawal_date', 'email', 'telephone', 'age', 'language', 'email_verified_at', 'created_at', 'updated_at']);
        $response->assertJsonFragment(['name' => $user->name]);
    }

    public function test_show_forbidden_for_partner()
    {
        $user = User::factory()->partner()->create();
        Passport::actingAs($user);
        $response = $this->get("/api/v1/users/{$user->id}");
        $response->assertStatus(403);
    }

    public function test_store_creates_user_as_admin()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);

        $data = [
            'num_partner' => 3,
            'nickname' => 'PruebasTest',
            'name' => 'PruebasTest',
            'password' => 'password1',
            'type' => 'admin',
            'registration_date' => now()->toDateString(),
            'email' => 'pruebas@zas.es',
            'telephone' => '123456787',
            'age' => 25,
            'language' => 'es',
        ];
        $response = $this->postJson('/api/v1/users', $data);
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'name' => 'PruebasTest',
            'email' => 'pruebas@zas.es',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'pruebas@zas.es',
        ]);
    }

    public function test_store_forbidden_for_junta()
    {
        $user = User::factory()->junta()->create();
        Passport::actingAs($user);
        $data = [
            'num_partner' => 3,
            'nickname' => 'PruebasTest',
            'name' => 'PruebasTest',
            'password' => 'password1',
            'type' => 'admin',
            'registration_date' => now()->toDateString(),
            'email' => 'pruebas@zas.es',
            'telephone' => '123456787',
            'age' => 25,
            'language' => 'es',
        ];
        $response = $this->postJson('/api/v1/users', $data);
        $response->assertStatus(403);
    }

    public function test_store_validation_fails_with_missing_fields()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $data = [
            'num_partner' => 3,
        ];
        $response = $this->postJson('/api/v1/users', $data);
        $response->assertStatus(422);
    }

    public function test_update_user_as_admin()
    {
        $user = User::factory()->admin()->create([
            'num_partner' => 2,
            'nickname' => 'PruebasTest2',
            'name' => 'PruebasTest2',
            'password' => 'password2',
            'type' => 'admin',
            'registration_date' => now()->toDateString(),
            'email' => 'pruebas3@zas.es',
            'telephone' => '123456788',
            'age' => 25,
            'language' => 'es',
        ]);
        $data = [
            'name' => 'PruebasTest2 actualizado',
            'type' => 'admin',
            'registration_date' => now()->toDateString(),
            'age' => 35,
            'language' => 'en',
        ];
        Passport::actingAs($user);
        $response = $this->putJson("/api/v1/users/{$user->id}", $data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'PruebasTest2 actualizado',
            'type' => 'admin',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'PruebasTest2 actualizado',
            'type' => 'admin',
        ]);
    }

    public function test_update_own_profile()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $data = [
            'name' => 'PruebasTest2 actualizado',
            'type' => 'admin',
            'registration_date' => now()->toDateString(),
            'age' => 35,
            'language' => 'es',
        ];
        $response = $this->putJson("/api/v1/users/{$user->id}", $data);
        $response->assertStatus(200);
    }

    public function test_update_forbidden_for_other_user()
    {
        $user1 = User::factory()->partner()->create();
        $user2 = User::factory()->admin()->create();
        Passport::actingAs($user1);
        $data = [
            'name' => 'PruebasTest2 actualizado',
            'type' => 'admin',
            'registration_date' => now()->toDateString(),
            'age' => 35,
            'language' => 'es',
        ];
        $response = $this->putJson("/api/v1/users/{$user2->id}", $data);
        $response->assertStatus(403);
    }

    public function test_destroy_deletes_user_as_admin()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);

        $response = $this->delete("/api/v1/users/{$user->id}");
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'User deleted',
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'withdrawal_date' => now()->toDateString(), // o not null
        ]);
    }

    public function test_destroy_forbidden_for_junta()
    {
        $user = User::factory()->junta()->create();
        Passport::actingAs($user);

        $response = $this->delete("/api/v1/users/{$user->id}");
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Forbidden',
        ]);
    }
}
