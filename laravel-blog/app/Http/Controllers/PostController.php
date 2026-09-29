<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::query()
            ->with(['topic', 'author'])
            ->when(! auth()->check(), fn ($query) => $query->where('status', true))
            ->latest()
            ->get();

        return view('posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('posts.create', [
            'post' => new Post(['status' => true]),
            'topics' => Topic::query()->orderBy('name')->get(),
        ]);
    }

    public function store(PostRequest $request): RedirectResponse
    {
        $post = Post::query()->create($this->payload($request));

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Post saved.');
    }

    public function show(Post $post): View
    {
        $this->ensureVisible($post);
        $post->load(['topic', 'author']);

        $related = Post::query()
            ->with('author')
            ->where('topic_id', $post->topic_id)
            ->whereKeyNot($post->id)
            ->where('status', true)
            ->latest()
            ->take(4)
            ->get();

        return view('posts.show', compact('post', 'related'));
    }

    public function edit(Post $post): View
    {
        $this->ensureCanManage($post);

        return view('posts.edit', [
            'post' => $post,
            'topics' => Topic::query()->orderBy('name')->get(),
        ]);
    }

    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        $this->ensureCanManage($post);
        $previousImage = $post->image;
        $post->update($this->payload($request, $post));

        if ($previousImage && $previousImage !== $post->image) {
            $this->deleteImage($previousImage);
        }

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Post updated.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->ensureCanManage($post);
        $this->deleteImage($post->image);
        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('status', 'Post deleted.');
    }

    private function payload(PostRequest $request, ?Post $post = null): array
    {
        $data = [
            'title' => $request->string('title')->toString(),
            'content' => $request->string('content')->toString(),
            'topic_id' => $request->integer('topic_id'),
            'status' => $request->boolean('status'),
            'image' => $post?->image,
        ];

        if ($post === null) {
            $data['user_id'] = $request->user()->id;
        }

        if ($name = $this->storeImage($request)) {
            $data['image'] = $name;
        }

        return $data;
    }

    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $directory = public_path('images/posts');
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $file = $request->file('image');
        $name = time().'_'.bin2hex(random_bytes(4)).'.'.($file->guessExtension() ?: 'img');
        $file->move($directory, $name);

        return $name;
    }

    private function deleteImage(?string $name): void
    {
        if (! $name) {
            return;
        }

        $path = public_path('images/posts/'.$name);
        if (is_file($path)) {
            unlink($path);
        }
    }

    private function ensureCanManage(Post $post): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->is_admin || $user->id === $post->user_id), 403);
    }

    private function ensureVisible(Post $post): void
    {
        if ($post->status) {
            return;
        }

        $user = auth()->user();
        abort_unless($user && ($user->is_admin || $user->id === $post->user_id), 404);
    }
}
