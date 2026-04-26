<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\Models\User;
use App\Models\Game;
use App\Models\Zassession;
use App\Models\Boardgame;

class GameTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_games_for_admin()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        Game::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/games');
        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonStructure(['*' => ['id', 'zassession_id', 'boardgame_id', 'host_user_id', 'max_players', 'start_time', 'status', 'necesary_know_how']]);
    }
    public function test_index_forbidden_for_guest()
    {
        $user = User::factory()->guest()->create();
        Passport::actingAs($user);

        $response = $this->getJson('/api/v1/games');
        $response->assertStatus(403);
    }
    public function test_index_unauthenticated_returns_401()
    {
        $response = $this->getJson('/api/v1/games');
        $response->assertStatus(401);
    }
    public function test_show_returns_game_for_authenticated_user()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $game = Game::factory()->create();

        $response = $this->getJson("/api/v1/games/{$game->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'zassession_id', 'boardgame_id', 'host_user_id', 'max_players', 'start_time', 'status', 'necesary_know_how']);
    }
    public function test_show_returns_404_for_nonexistent_game()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);

        $response = $this->getJson('/api/v1/games/999');
        $response->assertStatus(404);
    }
    public function test_store_creates_game_as_admin()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $zassession = Zassession::factory()->create();
        $boardgame  = Boardgame::factory()->create();

        $data = [
            'zassession_id' => $zassession->id,
            'boardgame_id' => $boardgame->id,
            'host_user_id' => $user->id,
            'max_players' => 5,
            'start_time' => '17:00:00',
            'status' => 'open',
            'necesary_know_how' => true
        ];
        $response = $this->postJson('/api/v1/games', $data);
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'zassession_id' => $data['zassession_id'],
            'boardgame_id' => $data['boardgame_id'],
            'host_user_id' => $data['host_user_id'],
            'max_players' => $data['max_players'],
            'status' => $data['status'],
            'necesary_know_how' => $data['necesary_know_how']
        ]);
        $this->assertDatabaseHas('games', [
            'zassession_id' => $data['zassession_id'],
            'boardgame_id' => $data['boardgame_id'],
            'host_user_id' => $data['host_user_id'],
            'max_players' => $data['max_players'],
            'status' => $data['status'],
            'necesary_know_how' => $data['necesary_know_how']
        ]);
    }
    public function test_store_creates_game_as_partner()
    {
        $user = User::factory()->partner()->create();
        Passport::actingAs($user);
        $zassession = Zassession::factory()->create();
        $boardgame  = Boardgame::factory()->create();

        $data = [
            'zassession_id' => $zassession->id,
            'boardgame_id' => $boardgame->id,
            'host_user_id' => $user->id,
            'max_players' => 6,
            'start_time' => '17:30:00',
            'status' => 'limited',
            'necesary_know_how' => false
        ];
        $response = $this->postJson('/api/v1/games', $data);
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'zassession_id' => $data['zassession_id'],
            'boardgame_id' => $data['boardgame_id'],
            'host_user_id' => $data['host_user_id'],
            'max_players' => $data['max_players'],
            'status' => $data['status'],
            'necesary_know_how' => $data['necesary_know_how']
        ]);
        $this->assertDatabaseHas('games', [
            'zassession_id' => $data['zassession_id'],
            'boardgame_id' => $data['boardgame_id'],
            'host_user_id' => $data['host_user_id'],
            'max_players' => $data['max_players'],
            'status' => $data['status'],
            'necesary_know_how' => $data['necesary_know_how']
        ]);
    }
    public function test_store_forbidden_for_guest()
    {
        $user = User::factory()->guest()->create();
        Passport::actingAs($user);
        $zassession = Zassession::factory()->create();
        $boardgame  = Boardgame::factory()->create();

        $data = [
            'zassession_id' => $zassession->id,
            'boardgame_id' => $boardgame->id,
            'host_user_id' => $user->id,
            'max_players' => 7,
            'start_time' => '18:00:00',
            'status' => 'open',
            'necesary_know_how' => false
        ];
        $response = $this->postJson('/api/v1/games', $data);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('games', [
            'zassession_id' => $data['zassession_id'],
            'boardgame_id' => $data['boardgame_id'],
            'host_user_id' => $data['host_user_id'],
            'max_players' => $data['max_players'],
            'status' => $data['status'],
            'necesary_know_how' => $data['necesary_know_how']
        ]);
    }
    public function test_store_validation_fails_with_missing_fields()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $zassession = Zassession::factory()->create();
        $boardgame  = Boardgame::factory()->create();

        $data = [
            'zassession_id' => $zassession->id,
            'boardgame_id' => $boardgame->id,
            'host_user_id' => $user->id,            
            'start_time' => '18:30:00',
            'status' => 'limited',
            'necesary_know_how' => true
        ];
        $response = $this->postJson('/api/v1/games', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['max_players']);
         $this->assertDatabaseMissing('games', [
            'zassession_id' => $data['zassession_id'],
            'boardgame_id' => $data['boardgame_id'],
            'host_user_id' => $data['host_user_id'],            
            'start_time' => $data['start_time'],
            'status' => $data['status'],
            'necesary_know_how' => $data['necesary_know_how']
        ]);
    }
    public function test_update_game_as_admin()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $game = Game::factory()->create();
        $zassession = Zassession::factory()->create();
        $boardgame  = Boardgame::factory()->create();

        $data = [
            'id' => $game['id'],
            'zassession_id' => $zassession->id,
            'boardgame_id' => $boardgame->id,
            'host_user_id' => $user->id,
            'max_players' => 8,
            'start_time' => '18:00:00',
            'status' => 'open',
            'necesary_know_how' => true,
        ];
        $response = $this->putJson("/api/v1/games/{$data['id']}", $data);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'zassession_id' => $data['zassession_id'],
            'boardgame_id' => $data['boardgame_id'],
            'host_user_id' => $data['host_user_id'],
            'max_players' => $data['max_players'],
            'start_time' => $data['start_time'],
            'status' => $data['status'],
            'necesary_know_how' => $data['necesary_know_how'],
        ]);
        $this->assertDatabaseHas('games', [
            'id' => $data['id'],
            'zassession_id' => $data['zassession_id'],
            'boardgame_id' => $data['boardgame_id'],
            'host_user_id' => $data['host_user_id'],
            'max_players' => $data['max_players'],
            'start_time' => $data['start_time'],
            'status' => $data['status'],
            'necesary_know_how' => $data['necesary_know_how'],
        ]);
    }
    public function test_update_game_as_junta()
    {
        $user = User::factory()->junta()->create();
        Passport::actingAs($user);
        $game = Game::factory()->create();
        $zassession = Zassession::factory()->create();
        $boardgame  = Boardgame::factory()->create();
        $data = [
            'id' => $game['id'],
            'zassession_id' => $zassession->id,
            'boardgame_id' => $boardgame->id,
            'host_user_id' => $user->id,
            'max_players' => 2,
            'start_time' => '17:30:00',
            'status' => 'limited',
            'necesary_know_how' => false,
        ];
        $response = $this->putJson("/api/v1/games/{$data['id']}", $data);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'zassession_id' => $data['zassession_id'],
            'boardgame_id' => $data['boardgame_id'],
            'host_user_id' => $data['host_user_id'],
            'max_players' => $data['max_players'],
            'start_time' => $data['start_time'],
            'status' => $data['status'],
            'necesary_know_how' => $data['necesary_know_how'],
        ]);
        $this->assertDatabaseHas('games', [
            'id' => $data['id'],
            'zassession_id' => $data['zassession_id'],
            'boardgame_id' => $data['boardgame_id'],
            'host_user_id' => $data['host_user_id'],
            'max_players' => $data['max_players'],
            'start_time' => $data['start_time'],
            'status' => $data['status'],
            'necesary_know_how' => $data['necesary_know_how'],
        ]);
    }
    public function test_update_forbidden_for_partner()
    {
        $user = User::factory()->partner()->create();
        Passport::actingAs($user);
        $game = Game::factory()->create();
        $zassession = Zassession::factory()->create();
        $boardgame  = Boardgame::factory()->create();
        $data = [
            'id' => $game['id'],
            'zassession_id' => $zassession->id,
            'boardgame_id' => $boardgame->id,
            'host_user_id' => $user->id,
            'max_players' => 6,
            'start_time' => '19:00:00',
            'status' => 'open',
            'necesary_know_how' => true,
        ];
        $response = $this->putJson("/api/v1/games/{$data['id']}", $data);
        $response->assertStatus(403);
        $response->assertJsonFragment([
            'message' => 'Forbidden',
        ]);
        $this->assertDatabaseMissing('games', [
            'id' => $data['id'],
            'zassession_id' => $data['zassession_id'],
            'boardgame_id' => $data['boardgame_id'],
            'host_user_id' => $data['host_user_id'],
            'max_players' => $data['max_players'],
            'start_time' => $data['start_time'],
            'status' => $data['status'],
            'necesary_know_how' => $data['necesary_know_how'],
        ]);
    }
    public function test_destroy_deletes_game_as_admin()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $game = Game::factory()->create();

        $response = $this->deleteJson("/api/v1/games/{$game->id}");
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Game deleted',
        ]);
        $this->assertDatabaseMissing('games', ['id' => $game->id]);
    }
    public function test_destroy_forbidden_for_partner()
    {
        $user = User::factory()->partner()->create();
        Passport::actingAs($user);
        $game = Game::factory()->create();

        $response = $this->deleteJson("/api/v1/games/{$game->id}");
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Forbidden',
        ]);
        $this->assertDatabaseHas('games', ['id' => $game->id]);
    }
    public function test_join_game_as_authenticated_user()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $game = Game::factory()->create();
        
        $response = $this->postJson("/api/v1/games/{$game->id}/join");
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'User joined the game',
        ]);
        $this->assertDatabaseHas('game_user', [
            'game_id' => $game->id,
            'user_id' => $user->id,
        ]);
    }
    public function test_leave_game_as_authenticated_user()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $game = Game::factory()->create();
        
        $response = $this->postJson("/api/v1/games/{$game->id}/join");
        $response->assertStatus(200);
        $response = $this->deleteJson("/api/v1/games/{$game->id}/leave");
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'User left the game',
        ]);
        $this->assertDatabaseMissing('game_user', [
            'game_id' => $game->id,
            'user_id' => $user->id,
        ]);
    }
    public function test_join_game_already_joined_returns_409()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $game = Game::factory()->create();
        
        $response = $this->postJson("/api/v1/games/{$game->id}/join");
        $response->assertStatus(200);
        $response = $this->postJson("/api/v1/games/{$game->id}/join");
        $response->assertStatus(409);
        $response->assertJson([
            'message' => 'User already joined this game',
        ]);
    }
    public function test_get_users_in_game()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $game = Game::factory()->create();
        $game->players()->attach($user->id);

        $response = $this->getJson("/api/v1/games/{$game->id}/users");
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
    public function test_stats_forbidden_for_guest()
    {
        $user = User::factory()->guest()->create();
        Passport::actingAs($user);
        $game = Game::factory()->create();

        $response = $this->getJson("/api/v1/games/{$game->id}/stats");
        $response->assertStatus(403);
    }
}
