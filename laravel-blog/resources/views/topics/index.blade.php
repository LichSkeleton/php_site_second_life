@extends('layouts.app')

@section('title', 'Categories')

@section('content')
    <h1>Categories</h1>
    <p class="lead">Each post is linked to one category. The number is how many published posts use it.</p>
    <div class="cards">
        @foreach ($topics as $topic)
            <article class="card">
                <h2><a href="{{ route('topics.show', $topic) }}">{{ $topic->name }}</a></h2>
                <p>{{ $topic->description }}</p>
                <p class="meta">{{ $topic->posts_count }} published {{ $topic->posts_count === 1 ? 'post' : 'posts' }}</p>
            </article>
        @endforeach
    </div>
@endsection
