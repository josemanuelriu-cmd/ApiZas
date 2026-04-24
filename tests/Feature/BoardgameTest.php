<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\Models\Boardgame;
use App\Models\User;

class BoardgameTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_index_returns_all_boardgames_for_authenticated_user()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        Boardgame::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/boardgames');     
        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonStructure(['*' => ['name', 'slug', 'min_players', 'max_players', 'min_age', 'duration', 'description']]);
    }

    public function test_index_unauthenticated_returns_401()
    {
        $response = $this->getJson('/api/v1/boardgames');
        $response->assertStatus(401);
    }

    public function test_show_returns_boardgame_for_authenticated_user()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $boardgame = Boardgame::factory()->create();
        $response = $this->getJson("/api/v1/boardgames/{$boardgame->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure(['name', 'slug', 'min_players', 'max_players', 'min_age', 'duration', 'description']);
    }

    public function test_show_returns_404_for_nonexistent_boardgame()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $response = $this->getJson('/api/v1/boardgames/999');
        $response->assertStatus(404);
    }

    public function test_store_creates_boardgame_as_admin ()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);

        $data = [
            'name' => 'Pruebas',
            'slug' => 'pruebas',
            'min_players' => 3,
            'max_players' => 5,
            'min_age' => 10,
            'duration' => 100,
            'description' => 'descripcion prueba',
            'owner_user_id' => null,
        ];
        $response = $this->postJson('/api/v1/boardgames', $data);
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'name' => 'Pruebas',
        ]);
        $this->assertDatabaseHas('boardgames', [
            'slug' => 'pruebas',
        ]);
    }
    public function test_store_creates_boardgame_as_junta()
    {
        $user = User::factory()->junta()->create();
        Passport::actingAs($user);

        $data = [
            'name' => 'Pruebas_junta',
            'slug' => 'pruebas-junta',
            'min_players' => 3,
            'max_players' => 5,
            'min_age' => 10,
            'duration' => 100,
            'description' => 'descripcion prueba',
            'owner_user_id' => $user->id,
        ];
        $response = $this->postJson('/api/v1/boardgames', $data);
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'name' => 'Pruebas_junta',
        ]);
        $this->assertDatabaseHas('boardgames', [
            'slug' => 'pruebas-junta',
        ]);
    }
    public function test_store_forbidden_for_partner()
    {
        $user = User::factory()->partner()->create();
        Passport::actingAs($user);

        $data = [
            'name' => 'Pruebas3',
            'slug' => 'pruebas3',
            'min_players' => 3,
            'max_players' => 5,
            'min_age' => 10,
            'duration' => 100,
            'description' => 'descripcion prueba3',
            'owner_user_id' => $user->id,
        ];
        $response = $this->postJson('/api/v1/boardgames', $data);
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Forbidden',
        ]);
        $this->assertDatabaseMissing('boardgames', [
            'slug' => 'pruebas3',
        ]);
    }
    public function test_store_validation_fails_with_missing_fields()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);

        $data = [
            'name' => 'Pruebas4',            
            'min_age' => 10,
            'duration' => 100,
            'description' => 'descripcion prueba4',
            'owner_user_id' => $user->id,
        ];
        $response = $this->postJson('/api/v1/boardgames', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['min_players', 'max_players']);
    }
    public function test_store_auto_generates_slug()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);

        $data = [
            'name' => 'Pruebas Auto Slug',
            'min_players' => 3,
            'max_players' => 5,
            'min_age' => 10,
            'duration' => 100,
            'description' => 'descripcion prueba auto slug',
            'owner_user_id' => $user->id,
        ];
        $response = $this->postJson('/api/v1/boardgames', $data);
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'name' => 'Pruebas Auto Slug',
        ]);
        $this->assertDatabaseHas('boardgames', [
            'slug' => 'pruebas-auto-slug',
        ]);
    }

    public function test_update_boardgame_as_admin()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $boardgame = Boardgame::factory()->create();

        $data = [
            'name' => 'Pruebas Update',
            'slug' => 'pruebas-update',
            'min_players' => 4,
            'max_players' => 6,
            'min_age' => 12,
            'duration' => 120,
            'description' => 'descripcion prueba update',
        ];
        $response = $this->putJson("/api/v1/boardgames/{$boardgame->id}", $data);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'Pruebas Update',
        ]);
        $this->assertDatabaseHas('boardgames', [
            'id' => $boardgame->id,
            'name' => 'Pruebas Update',
            'slug' => 'pruebas-update',
        ]);
    }
    public function test_update_boardgame_as_junta()
    {
        $user = User::factory()->junta()->create();
        Passport::actingAs($user);
        $boardgame = Boardgame::factory()->create();

        $data = [
            'name' => 'Pruebas Update junta',
            'slug' => 'pruebas-update-junta',
            'min_players' => 3,
            'max_players' => 5,
            'min_age' => 10,
            'duration' => 120,
            'description' => 'descripcion prueba update junta',
        ];
        $response = $this->putJson("/api/v1/boardgames/{$boardgame->id}", $data);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'Pruebas Update junta',
        ]);
        $this->assertDatabaseHas('boardgames', [
            'id' => $boardgame->id,
            'name' => 'Pruebas Update junta',
            'slug' => 'pruebas-update-junta',
        ]);
    }
    public function test_update_forbidden_for_partner()
    {
        $user = User::factory()->partner()->create();
        Passport::actingAs($user);
        $boardgame = Boardgame::factory()->create();

        $data = [
            'name' => 'Pruebas Update partner',
            'slug' => 'pruebas-update3-partner',
            'min_players' => 2,
            'max_players' => 6,
            'min_age' => 14,
            'duration' => 120,
            'description' => 'descripcion prueba update partner',
        ];
        $response = $this->putJson("/api/v1/boardgames/{$boardgame->id}", $data);
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Forbidden',
        ]);
        $this->assertDatabaseMissing('boardgames', [
            'slug' => 'pruebas-update3-partner',
        ]);
    }

    public function test_destroy_deletes_boardgame_as_admin()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $boardgame = Boardgame::factory()->create();

        $response = $this->deleteJson("/api/v1/boardgames/{$boardgame->id}");
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Boardgame deleted',
        ]);
        $this->assertDatabaseMissing('boardgames', ['id' => $boardgame->id]);
    }
    public function test_destroy_deletes_boardgame_as_junta()
    {
        $user = User::factory()->junta()->create();
        Passport::actingAs($user);
        $boardgame = Boardgame::factory()->create();

        $response = $this->deleteJson("/api/v1/boardgames/{$boardgame->id}");
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Boardgame deleted',
        ]);
        $this->assertDatabaseMissing('boardgames', ['id' => $boardgame->id]);
    }
    public function test_destroy_forbidden_for_partner()
    {
        $user = User::factory()->partner()->create();
        Passport::actingAs($user);
        $boardgame = Boardgame::factory()->create();

        $response = $this->deleteJson("/api/v1/boardgames/{$boardgame->id}");
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Forbidden',
        ]);
        $this->assertDatabaseHas('boardgames', ['id' => $boardgame->id]);
    }
}
