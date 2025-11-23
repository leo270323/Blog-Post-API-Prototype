<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function testCaseGetPostsListWithPagination()
    {
        Post::factory()->count(15)->create();

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)->assertJsonStructure(['success', 'message','data', 'links', 'meta']);

        $this->assertGreaterThan(0, count($response->json('data')));
    }

    public function testCaseCreatePostSuccessfully()
    {
        $author = User::factory()->create();

        $payload = [
            'title' => 'Test Post Title',
            'content' => 'Test content',
            'author_id' => $author->id,
        ];

        $response = $this->postJson('/api/posts', $payload);

        $response->assertStatus(201)->assertJsonStructure([
            'success', 'message',
            'data' => ['id', 'title', 'content', 'author', 'created_at', 'updated_at']
        ])->assertJson(['success' => true, 'message' => 'Post created']);

        $this->assertDatabaseHas('posts', ['title' => 'Test Post Title', 'author_id' => $author->id]);
    }

    public function testCaseCreatePostWithInvalidData()
    {
        // missing title & content
        $payload = [
            'title' => '',
            'content' => '',
        ];

        $response = $this->postJson('/api/posts', $payload);

        $response->assertStatus(422)->assertJsonStructure(['message', 'errors']);
    }

    public function testCaseCreatePostWithInvalidAuthor()
    {
        // author_id not exists
        $payload = [
            'title' => 'Another Post',
            'content' => 'content',
            'author_id' => 99
        ];

        $response = $this->postJson('/api/posts', $payload);

        $response->assertStatus(422)->assertJsonStructure(['message', 'errors']);
    }

    public function testCaseShowPostExists()
    {
        $post = Post::factory()->create();

        $response = $this->getJson('/api/posts/' . $post->id);

        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'message',
            'data' => ['id', 'title', 'content', 'author', 'created_at', 'updated_at']
        ])->assertJson(['success' => true, 'message' => 'Post retrieved']);

        $this->assertEquals($post->id, $response->json('data.id'));
    }

    public function testCaseShowPostNotFound()
    {
        $nonExistingId = 99;

        $response = $this->getJson('/api/posts/' . $nonExistingId);

        $response->assertStatus(404)->assertJson(['success' => false, 'message' => 'Post not found']);
    }

    public function testCaseShowPostWithDeletedAuthor()
    {
        $author = User::factory()->create();

        $payload = [
            'title' => 'Test Post With deleted author',
            'content' => 'Test content',
            'author_id' => $author->id,
        ];
        $post = $this->postJson('/api/posts', $payload);

        $this->deleteJson('/api/users/' . $author->id);

        $response = $this->getJson('/api/posts/' . $post->json('data.id'));

        $response->assertStatus(404)->assertJson(['success' => false, 'message' => 'Post not found']);
    }

    public function testCaseUpdatePostSuccessfully()
    {
        $author = User::factory()->create();

        $post = Post::factory()->create([
            'title' => 'Old Title',
            'content' => 'Old content',
        ]);

        $payload = [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'author_id' => $author->id
        ];

        $response = $this->putJson('/api/posts/' . $post->id, $payload);

        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'message',
            'data' => ['id', 'title', 'content', 'author', 'created_at', 'updated_at']
        ])->assertJson(['success' => true, 'message' => 'Post updated']);

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Updated Title']);
    }

    public function testCaseUpdatePostWithInvalidData()
    {
        $post = Post::factory()->create();

        $payload = [
            'title' => '', // invalid empty
        ];

        $response = $this->putJson('/api/posts/' . $post->id, $payload);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function testCaseUpdateNonExistentPost()
    {
        $nonExistingId = 99;
        $author = User::factory()->create();

        $response = $this->putJson('/api/posts/' . $nonExistingId, [
            'title' => 'Any',
            'content' => 'Any content',
            'author_id' => $author->id
        ]);

        $response->assertStatus(404)->assertJson([
            'success' => false,
            'message' => 'Post not found'
        ]);
    }


    public function testCaseDeletePostSuccessfully()
    {
        $post = Post::factory()->create();

        $response = $this->deleteJson('/api/posts/' . $post->id);

        $response->assertStatus(200)->assertJson(['success' => true, 'message' => 'Post deleted']);

        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }


    public function testCaseDeleteNonExistentPost()
    {
        $nonExistingId = 99;

        $response = $this->deleteJson("/api/posts/{$nonExistingId}");

        $response->assertStatus(404)->assertJson(['success' => false, 'message' => 'Post not found']);
    }
}
