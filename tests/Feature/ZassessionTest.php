<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\Models\User;
use App\Models\Zassession;

class ZassessionTest extends TestCase
{
    use RefreshDatabase;    

    public function test_index_returns_all_zassessions_for_authenticated_user()
    {
        $user = User::factory()->admin()->create();        
        Passport::actingAs($user);
        $sessions = Zassession::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/zassessions');
        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonStructure(['*' => ['id', 'name', 'event_name', 'date', 'start_time', 'end_time', 'max_users', 'direction', 'latitude', 'longitude']]);
    }
    public function test_index_unauthenticated_returns_401()
    {
        $response = $this->getJson('/api/v1/zassessions');

        $response->assertStatus(401);
    }

    public function test_show_returns_zassession_for_authenticated_user()
    {
        $user = User::factory()->admin()->create();        
        Passport::actingAs($user);
        $zassession = Zassession::factory()->create();

        $response = $this->getJson("/api/v1/zassessions/{$zassession->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $zassession->id]);
    }

    public function test_show_returns_404_for_nonexistent_zassession()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $response = $this->getJson('/api/v1/zassessions/999');

        $response->assertStatus(404);
    }

    public function test_store_creates_zassession_as_admin()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);

        $data = [
            'name' => 'Zassession admin',
            'event_name' => 'Admin Event',
            'date' => '2026-10-12',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'max_users' => 10,
            'direction' => 'Admin Direction',
            'latitude' => 40.7128,
            'longitude' => -74.0060
        ];
        $response = $this->postJson('/api/v1/zassessions', $data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'name' => $data['name'],
            'event_name' => $data['event_name'],
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'max_users' => $data['max_users'],
            'direction' => $data['direction'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude']
        ]);
        $this->assertDatabaseHas('zassessions', [
            'name' => $data['name'],
            'event_name' => $data['event_name'],
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'max_users' => $data['max_users'],
            'direction' => $data['direction'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude']
        ]);
    }
    public function test_store_creates_zassession_as_junta()
    {
        $user = User::factory()->junta()->create();
        Passport::actingAs($user);
        
        $data = [
            'name' => 'Zassession junta',
            'event_name' => 'Junta Event',
            'date' => '2026-10-13',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'max_users' => 10,
            'direction' => 'Junta Direction',
            'latitude' => 40.7128,
            'longitude' => -74.0060
        ];
        $response = $this->postJson('/api/v1/zassessions', $data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'name' => $data['name'],
            'event_name' => $data['event_name'],
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'max_users' => $data['max_users'],
            'direction' => $data['direction'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude']
        ]);
        $this->assertDatabaseHas('zassessions', [
            'name' => $data['name'],
            'event_name' => $data['event_name'],
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'max_users' => $data['max_users'],
            'direction' => $data['direction'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude']
        ]);
    }
    public function test_store_forbidden_for_partner()
    {
        $user = User::factory()->partner()->create();
        Passport::actingAs($user);

        $data = [
            'name' => 'Zassession partner',
            'event_name' => 'partner Event',
            'date' => '2026-10-12',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'max_users' => 10,
            'direction' => 'partner Direction',
            'latitude' => 40.7128,
            'longitude' => -74.0060
        ];
        $response = $this->postJson('/api/v1/zassessions', $data);
        $response->assertStatus(403);
        $response->assertJsonFragment([
            'message' => 'Forbidden',
        ]);
        $this->assertDatabaseMissing('zassessions', [
            'name' => $data['name'],
            'event_name' => $data['event_name'],
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'max_users' => $data['max_users'],
            'direction' => $data['direction'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude']
        ]);
    }
    public function test_store_validation_fails_with_missing_fields()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);

        $data = [
            'name' => 'Zassession admin',
            'event_name' => 'Admin Event',            
            'max_users' => 10,
            'direction' => 'Admin Direction',
            'latitude' => 40.7128,
            'longitude' => -74.0060
        ];
        $response = $this->postJson('/api/v1/zassessions', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['date', 'start_time', 'end_time']);
    }
    public function test_update_zassession_as_admin()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);

        $zassession = Zassession::factory()->create();
        $data = [
            'name' => 'Updated Zassession admin',
            'event_name' => 'An updated zassession',
        ];
        $response = $this->putJson("/api/v1/zassessions/{$zassession->id}", $data);

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Updated Zassession admin']);
    }
    public function test_update_zassession_as_junta()
    {
        $user = User::factory()->junta()->create();
        Passport::actingAs($user);

        $zassession = Zassession::factory()->create();
        $data = [
            'name' => 'Updated Zassession junta',
            'event_name' => 'An updated zassession',
        ];
        $response = $this->putJson("/api/v1/zassessions/{$zassession->id}", $data);

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Updated Zassession junta']);
    }
    public function test_update_forbidden_for_partner()
    {
        $user = User::factory()->partner()->create();
        Passport::actingAs($user);

        $zassession = Zassession::factory()->create();
        $data = [
            'name' => 'Updated Zassession partner',
            'event_name' => 'An updated zassession',
        ];
        $response = $this->putJson("/api/v1/zassessions/{$zassession->id}", $data);

        $response->assertStatus(403);
        $response->assertJsonFragment([
            'message' => 'Forbidden',
        ]);
        $this->assertDatabaseMissing('zassessions', [
            'name' => 'Updated Zassession partner',
        ]);
    }
    public function test_destroy_deletes_zassession_as_admin()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $zassession = Zassession::factory()->create();

        $response = $this->deleteJson("/api/v1/zassessions/{$zassession->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('zassessions', [
            'id' => $zassession->id
        ]);
    }
    public function test_destroy_forbidden_for_partner()
    {
        $user = User::factory()->partner()->create();
        Passport::actingAs($user);
        $zassession = Zassession::factory()->create();

        $response = $this->deleteJson("/api/v1/zassessions/{$zassession->id}");
        $response->assertStatus(403);
        $this->assertDatabaseHas('zassessions', [
            'id' => $zassession->id
        ]);
    }
    public function test_join_zassession_as_authenticated_user()
    {
        $user = User::factory()->junta()->create();
        Passport::actingAs($user);

        $zassession = Zassession::factory()->create();

        $response = $this->postJson("/api/v1/zassessions/{$zassession->id}/join");

        $response->assertStatus(200);
    }
    public function test_leave_zassession_as_authenticated_user()
    {
         $user = User::factory()->admin()->create();
        Passport::actingAs($user);

        $zassession = Zassession::factory()->create();

        $response = $this->postJson("/api/v1/zassessions/{$zassession->id}/join");
        $response->assertStatus(200);
        $response = $this->deleteJson("/api/v1/zassessions/{$zassession->id}/leave");
        $response->assertStatus(200);
    }
    public function test_join_zassession_already_joined_returns_error()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $zassession = Zassession::factory()->create();

        $response = $this->postJson("/api/v1/zassessions/{$zassession->id}/join");
        $response->assertStatus(200);
        $response = $this->postJson("/api/v1/zassessions/{$zassession->id}/join");
        $response->assertStatus(422);
    }

    public function test_get_users_in_zassession()
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $zassession = Zassession::factory()->create();

        $response = $this->postJson("/api/v1/zassessions/{$zassession->id}/join");
        $response->assertStatus(200);
        $response = $this->getJson("/api/v1/zassessions/{$zassession->id}/users");
        $response->assertStatus(200);
    }

    public function test_stats_forbidden_for_guest()
    {
        $user = User::factory()->guest()->create();
        Passport::actingAs($user);
        $zassession = Zassession::factory()->create();

        $response = $this->getJson("/api/v1/zassessions/{$zassession->id}/stats");
        $response->assertStatus(403);
    }    
}       
