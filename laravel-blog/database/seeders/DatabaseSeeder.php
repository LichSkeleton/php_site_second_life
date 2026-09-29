<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@myblog.local'],
            [
                'name' => 'Admin',
                'password' => 'admin123',
                'is_admin' => true,
            ]
        );

        $demo = User::query()->updateOrCreate(
            ['email' => 'demo@myblog.local'],
            [
                'name' => 'Demo',
                'password' => 'user123',
                'is_admin' => false,
            ]
        );

        $topics = [
            'PHP' => 'Language notes and small examples carried over from the previous laboratories.',
            'Laravel' => 'Routes, controllers, Eloquent models, validation, and migrations.',
            'Databases' => 'How posts are stored and how a post keeps its category.',
            'Web' => 'HTML pages, navigation, and the public side of the blog.',
        ];

        $topicIds = [];
        foreach ($topics as $name => $description) {
            $topic = Topic::query()->updateOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
            $topicIds[$name] = $topic->id;
        }

        if (Post::query()->exists()) {
            return;
        }

        $rows = [
            [
                'title' => 'Moving the blog to Laravel',
                'topic' => 'Laravel',
                'author' => $admin,
                'status' => true,
                'content' => 'The same posts and categories now live in Eloquent models. A post still belongs to one category and one author, and both are stored with foreign keys.',
            ],
            [
                'title' => 'Routes, controllers, and views',
                'topic' => 'Laravel',
                'author' => $admin,
                'status' => true,
                'content' => 'Each page is a named route handled by a controller. Blade views render the HTML, including the category and the author of a post.',
            ],
            [
                'title' => 'Four fields of a post',
                'topic' => 'Databases',
                'author' => $demo,
                'status' => true,
                'content' => 'A post keeps a title, the text, an optional image, and a published status. The category is a separate row, not a column copied into the post.',
            ],
            [
                'title' => 'What changed in the PHP version',
                'topic' => 'PHP',
                'author' => $demo,
                'status' => true,
                'content' => 'The earlier laboratories used separate PHP files and a hand-written database layer. This version keeps the blog domain and lets the framework organise it.',
            ],
            [
                'title' => 'Draft: navigation notes',
                'topic' => 'Web',
                'author' => $admin,
                'status' => false,
                'content' => 'This draft stays off the home page until it is published. The author and an administrator can still open it from the post list.',
            ],
        ];

        foreach ($rows as $row) {
            Post::query()->create([
                'user_id' => $row['author']->id,
                'topic_id' => $topicIds[$row['topic']],
                'title' => $row['title'],
                'content' => $row['content'],
                'image' => null,
                'status' => $row['status'],
            ]);
        }
    }
}
