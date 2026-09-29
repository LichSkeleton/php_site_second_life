@extends('layouts.app')

@section('title', 'Posts')

@section('content')
    <div class="page-head">
        <h1>Posts</h1>
        @auth
            <a class="button" href="{{ route('posts.create') }}">New post</a>
        @endauth
    </div>
    <p class="lead">Create, edit, and delete posts. A guest sees published posts. After sign-in, drafts appear in this list.</p>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr>
                        <td><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></td>
                        <td>{{ $post->topic->name }}</td>
                        <td>{{ $post->author->name }}</td>
                        <td>{{ $post->status ? 'Published' : 'Draft' }}</td>
                        <td class="actions">
                            @if (auth()->check() && (auth()->user()->is_admin || auth()->id() === $post->user_id))
                                <a href="{{ route('posts.edit', $post) }}">Edit</a>
                                <form method="post" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('Delete this post?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="link-button">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No posts yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
