<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\PostService;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\PostResource;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $posts = $this->postService->getPostsList($request);

            return PostResource::collection($posts)->additional(['success' => true, 'message' => 'Posts retrieved']);
        } catch (\Exception $e) {
            Log::error('Internal server error while get the list of posts', ['exception' => $e, 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'data' => []
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        try {
            $postData = $request->validated();
            $postData['content'] = htmlspecialchars($postData['content'], ENT_QUOTES, 'UTF-8');

            $post = $this->postService->store($postData);

            return response()->json([
                'success' => true,
                'message' => 'Post created',
                'data' => new PostResource($post)
            ], 201);
        } catch (\Exception $e) {
            Log::error('Internal server error while storing post', ['exception' => $e, 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'data' => []
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $post = $this->postService->getPostById($id);

            return response()->json([
                'success' => true,
                'message' => 'Post retrieved',
                'data' => new PostResource($post)
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found',
                'data' => []
            ], 404);
        } catch (\Exception $e) {
            Log::error('Internal server error while get post by id', ['exception' => $e, 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'data' => []
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, string $id)
    {
        try {
            $postData = $request->validated();
            $postData['content'] = htmlspecialchars($postData['content'], ENT_QUOTES, 'UTF-8');

            $post = $this->postService->update($id, $postData);

            return response()->json([
                'success' => true,
                'message' => 'Post updated',
                'data' => new PostResource($post)
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found',
                'data' => []
            ], 404);
        } catch (\Exception $e) {
            Log::error('Internal server error while update post', ['exception' => $e, 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'data' => []
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->postService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Post deleted',
                'data' => []
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found',
                'data' => []
            ], 404);
        } catch (\Exception $e) {
            Log::error('Internal server error while delete post', ['exception' => $e, 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'data' => []
            ], 500);
        }
    }
}
