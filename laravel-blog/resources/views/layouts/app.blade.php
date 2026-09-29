<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'My blog')</title>
    <link rel="stylesheet" href="{{ asset('css/blog.css') }}">
</head>
<body>
    <header class="site-header">
        <div class="wrap header-bar">
            <a class="brand" href="{{ route('home') }}">My blog</a>
            <nav class="site-nav" aria-label="Main">
                <a href="{{ route('home') }}" @class(['is-active' => request()->routeIs('home')])>Home</a>
                <a href="{{ route('posts.index') }}" @class(['is-active' => request()->routeIs('posts.*')])>Posts</a>
                <a href="{{ route('topics.index') }}" @class(['is-active' => request()->routeIs('topics.*')])>Categories</a>
                <a href="{{ route('api.help') }}" @class(['is-active' => request()->routeIs('api.help')])>API</a>
                @auth
                    <a href="{{ route('posts.create') }}">New post</a>
                    <span class="nav-user">{{ auth()->user()->name }}</span>
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" @class(['is-active' => request()->routeIs('login')])>Sign in</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="wrap">
        @if (session('status'))
            <p class="flash" role="status">{{ session('status') }}</p>
        @endif
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="wrap">Laravel blog: posts belong to a category and an author.</div>
    </footer>
</body>
</html>
