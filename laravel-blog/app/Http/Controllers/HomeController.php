<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Topic;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $posts = Post::query()
            ->with(['topic', 'author'])
            ->where('status', true)
            ->latest()
            ->get();

        $topics = Topic::query()
            ->withCount(['posts' => fn ($query) => $query->where('status', true)])
            ->orderBy('name')
            ->get();

        return view('home', compact('posts', 'topics'));
    }
}
