@extends('layouts.app')

@section('title', 'Edit post')

@section('content')
    <h1>Edit post</h1>
    @include('posts._form', [
        'action' => route('posts.update', $post),
        'method' => 'PUT',
        'submit' => 'Update post',
    ])
@endsection
