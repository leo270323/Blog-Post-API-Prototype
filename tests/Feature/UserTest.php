<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function testCaseUserExists()
    {
        $user = User::factory()->create();

        $response = $this->getJson('/api/users/' . $user->id);

        $response->assertStatus(200)->assertJsonStructure(
        [
            'success',
            'message',
            'data' => ['id', 'name', 'email', 'created_at', 'updated_at']
        ])->assertJson(['success' => true, 'message' => 'User retrieved']);

        $this->assertEquals($user->id, $response->json('data.id'));
    }

    public function testCase404UserNotFound()
    {
        $nonExistingId = 99;

        $response = $this->getJson('/api/users/' . $nonExistingId);

        $response->assertStatus(404)->assertJson([
            'success' => false,
            'message' => 'User not found',
        ]);
    }

    public function testCase404ForSotfDeletedUser()
    {
        $user = User::factory()->create();
        $user->delete();

        $response = $this->getJson('/api/users/' . $user->id);

        $response->assertStatus(404)->assertJson([
            'success' => false,
            'message' => 'User not found',
        ]);
    }

    public function testCaseGetUserListWithPagination()
    {
        User::factory()->count(15)->create();

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)->assertJsonStructure(['success', 'message','data', 'links', 'meta']);

        $this->assertGreaterThan(0, count($response->json('data')));
    }

    public function testCaseCreateUsersuccessfully()
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
        ];

        $response = $this->postJson('/api/users', $payload);

        $response->assertStatus(201)->assertJsonStructure([
            'success',
            'message',
            'data' => ['id', 'name', 'email', 'created_at', 'updated_at']
        ])->assertJson([
            'success' => true,
            'message' => 'User created'
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
            'name' => 'Test User'
        ]);
    }

    public function testCaseCreateUserWithInvalidData()
    {
        $payload = ['name' => ''];

        $response = $this->postJson('/api/users', $payload);

        $response->assertStatus(422)->assertJsonStructure(['message', 'errors']);
    }

    public function testCaseCreateUserWithInvalidEmail()
    {
        $payload = ['name' => 'testUser', 'email' => 'invalidEmail'];

        $response = $this->postJson('/api/users', $payload);

        $response->assertStatus(422)->assertJson(['message' => 'The email field must be a valid email address.']);
    }

    public function testCaseUpdateUserSuccessfully()
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com'
        ]);

        $payload = [
            'name' => 'New Name',
            'email' => 'newemail@example.com'
        ];

        $response = $this->putJson('/api/users/' . $user->id, $payload);

        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'message',
            'data' => ['id', 'name', 'email', 'created_at', 'updated_at'],
        ])->assertJson([
            'success' => true,
            'message' => 'User updated'
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'newemail@example.com'
        ]);
    }

    public function testCaseUpdateUserWithInvalidData()
    {
        $user = User::factory()->create();

        $payload = ['email' => 'invalidEmail'];

        $response = $this->putJson('/api/users/' . $user->id, $payload);

        $response->assertStatus(422)->assertJsonStructure(['message', 'errors']);
    }

    public function testCaseUpdateNonExistentUser()
    {
        $nonExistingId = 99;

        $response = $this->putJson('/api/users/' . $nonExistingId, [
            'name' => 'Any',
            'email' => 'any@example.com'
        ]);

        $response->assertStatus(404)->assertJson([
            'success' => false,
            'message' => 'User not found'
        ]);
    }

    public function testCaseDeleteUserSuccessfully()
    {
        $user = User::factory()->create();

        $response = $this->deleteJson('/api/users/' . $user->id);

        $response->assertStatus(200)->assertJson([
            'success' => true,
            'message' => 'User deleted'
        ]);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function testCaseDelteNonExistentUser()
    {
        $nonExistingId = 99;

        $response = $this->deleteJson('/api/users/' . $nonExistingId);

        $response->assertStatus(404)->assertJson([
            'success' => false,
            'message' => 'User not found'
        ]);
    }
}
