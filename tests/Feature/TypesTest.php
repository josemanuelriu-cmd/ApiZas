<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\Models\User;
use App\Models\Type;

class TypesTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_types_for_admin(): void
    {
        Type::factory()->count(3)->create();
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $response = $this->get('/api/v1/types');
        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $response->assertJsonStructure(['*' => ['id', 'type', 'description']]);
    }

    public function test_index_returns_all_types_for_junta(): void
    {
        Type::factory()->count(3)->create();
        $user = User::factory()->junta()->create();
        Passport::actingAs($user);
        $response = $this->get('/api/v1/types');
        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $response->assertJsonStructure(['*' => ['id', 'type', 'description']]);
    }

    public function test_index_returns_all_types_for_partner(): void
    {
        Type::factory()->count(3)->create();
        $user = User::factory()->partner()->create();
        Passport::actingAs($user);
        $response = $this->get('/api/v1/types');
        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $response->assertJsonStructure(['*' => ['id', 'type', 'description']]);
    }

    public function test_index_forbidden_for_guest(): void
    {
        $user = User::factory()->guest()->create();
        Passport::actingAs($user);
        $response = $this->get('/api/v1/types');
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Forbidden',
        ]);
    }

    public function test_index_unauthenticated_returns_401(): void
    {
        $response = $this->get('/api/v1/types');
        $response->assertStatus(401);
    }

    public function test_show_returns_type_for_admin(): void
    {
        $type = Type::factory()->create();
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $response = $this->get("/api/v1/types/{$type->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'type', 'description']);
        $response->assertJsonFragment(['type' => $type->type]);
    }

    public function test_show_returns_type_for_partner(): void
    {
        $type = Type::factory()->create();
        $user = User::factory()->partner()->create();
        Passport::actingAs($user);
        $response = $this->get("/api/v1/types/{$type->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'type', 'description']);
        $response->assertJsonFragment(['type' => $type->type]);
    }

    public function test_show_forbidden_for_guest(): void
    {
        $type = Type::factory()->create();
        $user = User::factory()->guest()->create();
        Passport::actingAs($user);
        $response = $this->get("/api/v1/types/{$type->id}");
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Forbidden',
        ]);
    }

    public function test_store_creates_type_as_admin(): void
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);

        $data = [
            'type' => 'Cartas',
            'description' => 'Cartas Description',
        ];
        $response = $this->postJson('/api/v1/types', $data);
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'type' => 'Cartas',
            'description' => 'Cartas Description',
        ]);
        $this->assertDatabaseHas('types', [
            'type' => 'Cartas',
            'description' => 'Cartas Description',
        ]);
    }

    public function test_store_creates_type_as_junta(): void
    {
        $user = User::factory()->junta()->create();
        Passport::actingAs($user);

        $data = [
            'type' => 'clásico',
            'description' => 'clásico Description',
        ];
        $response = $this->postJson('/api/v1/types', $data);
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'type' => 'clásico',
            'description' => 'clásico Description',
        ]);
        $this->assertDatabaseHas('types', [
            'type' => 'clásico',
            'description' => 'clásico Description',
        ]);
    }

    public function test_store_forbidden_for_partner(): void
    {
        $user = User::factory()->partner()->create();
        Passport::actingAs($user);

        $data = [
            'type' => 'partner_type',
            'description' => 'partner_description',
        ];
        $response = $this->postJson('/api/v1/types', $data);
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Forbidden',
        ]);
    }

    public function test_store_validation_fails_with_missing_fields(): void
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);

        $data = [
            'type' => '',
            'description' => '',
        ];
        $response = $this->postJson('/api/v1/types', $data);
        $response->assertStatus(422);
    }

    public function test_update_type_as_admin(): void
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $type = Type::factory()->create();

        
        $data = [
            'type' => 'Cartas',
            'description' => 'Updated Cartas Description',
        ];
        $response = $this->putJson("/api/v1/types/{$type->id}", $data);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'type' => 'Cartas',
            'description' => 'Updated Cartas Description',
        ]);
        $this->assertDatabaseHas('types', [
            'id' => $type->id,
            'type' => 'Cartas',
            'description' => 'Updated Cartas Description',
        ]);
    }

    public function test_update_type_as_junta(): void
    {
        $user = User::factory()->junta()->create();
        Passport::actingAs($user);
        $type = Type::factory()->create();
        $data = [
            'type' => 'clásico',
            'description' => 'Updated clásico Description',
        ];
        $response = $this->putJson("/api/v1/types/{$type->id}", $data);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'type' => 'clásico',
            'description' => 'Updated clásico Description',
        ]);
        $this->assertDatabaseHas('types', [
            'id' => $type->id,
            'type' => 'clásico',
            'description' => 'Updated clásico Description',
        ]);
    }

    public function test_update_forbidden_for_partner(): void
    {
        $user = User::factory()->partner()->create();
        Passport::actingAs($user);
        $type = Type::factory()->create();
        $data = [
            'type' => 'partner_type',
            'description' => 'partner_description',
        ];
        $response = $this->putJson("/api/v1/types/{$type->id}", $data);
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Forbidden',
        ]);
    }

    public function test_destroy_deletes_type_as_admin(): void
    {
        $user = User::factory()->admin()->create();
        Passport::actingAs($user);
        $type = Type::factory()->create();
        $response = $this->deleteJson("/api/v1/types/{$type->id}");
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Type deleted',
        ]);
    }

    public function test_destroy_forbidden_for_junta(): void
    {
        $user = User::factory()->junta()->create();
        Passport::actingAs($user);
        $type = Type::factory()->create();
        $response = $this->deleteJson("/api/v1/types/{$type->id}");
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Forbidden',
        ]);
        $this->assertDatabaseHas('types', [
            'id' => $type->id,
            'type' => $type->type,
        ]);
    }
}
