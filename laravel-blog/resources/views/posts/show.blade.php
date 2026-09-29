@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <article class="post">
        <p class="meta">
            <a href="{{ route('topics.show', $post->topic) }}">{{ $post->topic->name }}</a>
            <span>· {{ $post->author->name }}</span>
            <span>· {{ $post->created_at->format('d.m.Y H:i') }}</span>
            @unless ($post->status)
                <span class="badge">Draft</span>
            @endunless
        </p>
        <h1>{{ $post->title }}</h1>
        @if ($post->image)
            <img class="post-image" src="{{ asset('images/posts/'.$post->image) }}" alt="">
        @endif
        <div class="post-body">{!! nl2br(e($post->content)) !!}</div>
        <p class="related-note">Category: {{ $post->topic->description }}</p>

        @if (auth()->check() && (auth()->user()->is_admin || auth()->id() === $post->user_id))
            <p class="actions">
                <a class="button" href="{{ route('posts.edit', $post) }}">Edit</a>
            </p>
        @endif
    </article>

    @if ($related->isNotEmpty())
        <section class="related">
            <h2>More in {{ $post->topic->name }}</h2>
            <ul>
                @foreach ($related as $item)
                    <li>
                        <a href="{{ route('posts.show', $item) }}">{{ $item->title }}</a>
                        <span> · {{ $item->author->name }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
@endsection
