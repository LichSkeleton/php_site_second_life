@extends('layouts.app')

@section('title', $topic->name)

@section('content')
    <h1>{{ $topic->name }}</h1>
    <p class="lead">{{ $topic->description }}</p>

    @forelse ($posts as $post)
        <article class="card">
            <p class="meta">{{ $post->author->name }} · {{ $post->created_at->format('d.m.Y') }}</p>
            <h2><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h2>
            <p>{{ \Illuminate\Support\Str::limit($post->content, 180) }}</p>
        </article>
    @empty
        <p>No published posts in this category.</p>
    @endforelse
@endsection
