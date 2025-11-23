<?php

namespace App\Services;

use App\Models\Post;

class PostService
{
    public function store(array $userData)
    {
        return Post::create($userData);
    }

    public function delete(string $id)
    {
        $post = Post::findOrFail($id);

        $post->delete();
    }

    public function update(string $id, array $postData)
    {
        $post = Post::findOrFail($id);
        $post->fill($postData);
        $post->save();

        return $post;
    }

    public function getPostById(string $id)
    {
        return Post::leftjoin('users', 'posts.author_id', '=', 'users.id')->where('users.deleted_at', NULL)->findOrFail($id);
    }

    public function getPostsList($request)
    {
        $title = $request->query('title');
        $limit = $request->query('limit', 3);
        $authorId = $request->query('author_id');
        $sortType = $request->query('sort_type', 'desc');
        $posts = Post::leftjoin('users', 'posts.author_id', '=', 'users.id');
        $orderBy = (empty($request->query('order_by'))) ? 'posts.id' : ($request->query('order_by') == 'id' ? 'posts.id' : $request->query('order_by'));

        //Case : post created by deleted user
        $posts = $posts->where('users.deleted_at', NULL);

        if (!empty($title)) {
            $posts = $posts->where('posts.title', 'like', '%' . $title . '%');
        }

        if (!empty($authorId)) {
            $posts = $posts->where('posts.author_id', $authorId);
        }

        return $posts->orderBy($orderBy, $sortType)->paginate($limit)->appends($request->all());
    }
}
