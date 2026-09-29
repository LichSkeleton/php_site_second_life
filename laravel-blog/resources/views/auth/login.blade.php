@extends('layouts.app')

@section('title', 'Sign in')

@section('content')
    <h1>Sign in</h1>
    <p class="lead">Use a seeded account to create and edit posts.</p>
    <ul class="accounts">
        <li>Admin: <code>admin@myblog.local</code> / <code>admin123</code></li>
        <li>User: <code>demo@myblog.local</code> / <code>user123</code></li>
    </ul>

    <form class="stack narrow" method="post" action="{{ route('login.store') }}">
        @csrf
        <label>
            Email
            <input type="email" name="email" value="{{ old('email') }}" required autofocus>
        </label>
        @error('email')
            <p class="error">{{ $message }}</p>
        @enderror

        <label>
            Password
            <input type="password" name="password" required>
        </label>
        @error('password')
            <p class="error">{{ $message }}</p>
        @enderror

        <label class="check">
            <input type="checkbox" name="remember" value="1">
            Remember me
        </label>

        <button class="button" type="submit">Sign in</button>
    </form>
@endsection
