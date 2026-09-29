@extends('layouts.app')

@section('title', 'My blog')

@section('content')
    <div class="split">
        <section>
            <h1>Latest posts</h1>
            <p class="lead">Published posts from the blog, with the category and the author stored as related records.</p>

            @forelse ($posts as $post)
                <article class="card">
                    <p class="meta">
                        <a href="{{ route('topics.show', $post->topic) }}">{{ $post->topic->name }}</a>
                        <span>· {{ $post->author->name }}</span>
                        <span>· {{ $post->created_at->format('d.m.Y') }}</span>
                    </p>
                    <h2><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h2>
                    <p>{{ \Illuminate\Support\Str::limit($post->content, 180) }}</p>
                </article>
            @empty
                <p>No published posts yet.</p>
            @endforelse
        </section>

        <aside class="panel">
            <h2>Categories</h2>
            <ul class="topic-list">
                @foreach ($topics as $topic)
                    <li>
                        <a href="{{ route('topics.show', $topic) }}">{{ $topic->name }}</a>
                        <span>{{ $topic->posts_count }}</span>
                    </li>
                @endforeach
            </ul>
        </aside>
    </div>
@endsection
