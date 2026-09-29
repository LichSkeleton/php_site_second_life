<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $posts = Post::query()->with(['topic', 'author'])->latest()->get();

        return PostResource::collection($posts);
    }

    public function store(PostRequest $request): JsonResponse
    {
        $userId = $request->user()?->id
            ?? User::query()->where('is_admin', true)->value('id')
            ?? User::query()->value('id');

        if (! $userId) {
            return response()->json([
                'message' => 'Create a user before adding a post.',
            ], 422);
        }

        $post = Post::query()->create([
            'user_id' => $userId,
            'topic_id' => $request->integer('topic_id'),
            'title' => $request->string('title')->toString(),
            'content' => $request->string('content')->toString(),
            'image' => $request->input('image'),
            'status' => $request->boolean('status'),
        ]);

        return (new PostResource($post->load(['topic', 'author'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Post $post): PostResource
    {
        return new PostResource($post->load(['topic', 'author']));
    }

    public function update(PostRequest $request, Post $post): PostResource
    {
        $post->update([
            'topic_id' => $request->integer('topic_id'),
            'title' => $request->string('title')->toString(),
            'content' => $request->string('content')->toString(),
            'image' => $request->exists('image') ? $request->input('image') : $post->image,
            'status' => $request->boolean('status'),
        ]);

        return new PostResource($post->load(['topic', 'author']));
    }

    public function destroy(Post $post): JsonResponse
    {
        $post->delete();

        return response()->json(['message' => 'Post deleted.']);
    }
}
