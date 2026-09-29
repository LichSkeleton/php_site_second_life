@extends('layouts.app')

@section('title', 'New post')

@section('content')
    <h1>New post</h1>
    @include('posts._form', [
        'action' => route('posts.store'),
        'method' => 'POST',
        'submit' => 'Save post',
    ])
@endsection
