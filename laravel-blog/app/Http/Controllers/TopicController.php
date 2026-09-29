<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\View\View;

class TopicController extends Controller
{
    public function index(): View
    {
        $topics = Topic::query()
            ->withCount(['posts' => fn ($query) => $query->where('status', true)])
            ->orderBy('name')
            ->get();

        return view('topics.index', compact('topics'));
    }

    public function show(Topic $topic): View
    {
        $posts = $topic->posts()
            ->with('author')
            ->where('status', true)
            ->latest()
            ->get();

        return view('topics.show', compact('topic', 'posts'));
    }
}
