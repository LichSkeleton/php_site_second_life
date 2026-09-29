@extends('layouts.app')

@section('title', 'API')

@section('content')
    <h1>Posts API</h1>
    <p class="lead">JSON access to the same posts. Open the list in the browser, or call it from Postman. Send <code>Accept: application/json</code> and <code>Content-Type: application/json</code> for write requests.</p>

    <ul class="endpoint-list">
        <li><code>GET</code> <a href="{{ url('/api/posts') }}">/api/posts</a> — list of posts</li>
        <li><code>GET</code> /api/posts/{id} — one post by id</li>
        <li><code>POST</code> /api/posts — create a post</li>
        <li><code>PUT</code> /api/posts/{id} — update a post</li>
        <li><code>DELETE</code> /api/posts/{id} — delete a post</li>
    </ul>

    <h2>Create or update body</h2>
    <pre>{
  "title": "API post title",
  "content": "The text is at least ten characters.",
  "topic_id": 1,
  "status": true
}</pre>
    <p>A short title, empty text, or an unknown <code>topic_id</code> returns <code>422</code> with validation errors. An unknown id returns <code>404</code>.</p>
@endsection
