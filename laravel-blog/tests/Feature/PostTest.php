<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_lists_a_published_post_with_its_category(): void
    {
        [$user, $topic] = $this->blogRecords();
        $post = $this->makePost($user, $topic, 'Visible on the home page');

        $this->get('/')
            ->assertOk()
            ->assertSee('Visible on the home page')
            ->assertSee('Laravel');
    }

    public function test_guest_is_sent_to_login_before_creating_a_post(): void
    {
        $this->get('/posts/create')->assertRedirect(route('login'));
    }

    public function test_post_form_rejects_a_short_title_and_a_missing_category(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from('/posts/create')
            ->post('/posts', [
                'title' => 'Short',
                'content' => 'tiny',
                'topic_id' => 999,
            ])
            ->assertRedirect('/posts/create')
            ->assertSessionHasErrors(['title', 'content', 'topic_id']);

        $this->assertDatabaseCount('posts', 0);
    }

    public function test_user_can_create_a_post_linked_to_a_category(): void
    {
        $user = User::factory()->create();
        $topic = Topic::query()->create([
            'name' => 'Laravel',
            'description' => 'Framework notes for the laboratory work.',
        ]);

        $this->actingAs($user)
            ->post('/posts', [
                'title' => 'Eloquent relations',
                'content' => 'A post must keep the category it belongs to.',
                'topic_id' => $topic->id,
                'status' => '1',
            ])
            ->assertRedirect();

        $post = Post::query()->first();
        $this->assertNotNull($post);
        $this->assertSame($topic->id, $post->topic_id);
        $this->assertSame($user->id, $post->user_id);
        $this->assertTrue($post->status);

        $this->get('/posts/'.$post->id)
            ->assertOk()
            ->assertSee('Eloquent relations')
            ->assertSee('Laravel')
            ->assertSee($user->name);
    }

    public function test_api_returns_the_list_and_one_post(): void
    {
        [$user, $topic] = $this->blogRecords();
        $post = $this->makePost($user, $topic, 'Loaded through JSON');

        $this->getJson('/api/posts')
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Loaded through JSON')
            ->assertJsonPath('data.0.topic.name', 'Laravel')
            ->assertJsonPath('data.0.author.name', $user->name);

        $this->getJson('/api/posts/'.$post->id)
            ->assertOk()
            ->assertJsonPath('data.id', $post->id)
            ->assertJsonPath('data.topic.id', $topic->id);

        $this->getJson('/api/posts/999')->assertNotFound();
    }

    public function test_api_rejects_invalid_post_data(): void
    {
        $this->postJson('/api/posts', [
            'title' => 'No',
            'content' => '',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'content', 'topic_id']);
    }

    public function test_api_can_create_a_post(): void
    {
        User::factory()->create(['is_admin' => true]);
        $topic = Topic::query()->create([
            'name' => 'Databases',
            'description' => 'Where the related rows are stored.',
        ]);

        $this->postJson('/api/posts', [
            'title' => 'Created from JSON',
            'content' => 'The API stores a post together with its category.',
            'topic_id' => $topic->id,
            'status' => true,
        ])->assertCreated()
            ->assertJsonPath('data.topic.name', 'Databases')
            ->assertJsonPath('data.status', true);

        $this->assertDatabaseHas('posts', [
            'title' => 'Created from JSON',
            'topic_id' => $topic->id,
        ]);
    }

    private function blogRecords(): array
    {
        $user = User::factory()->create(['name' => 'Ada']);
        $topic = Topic::query()->create([
            'name' => 'Laravel',
            'description' => 'Framework notes for the laboratory work.',
        ]);

        return [$user, $topic];
    }

    private function makePost(User $user, Topic $topic, string $title): Post
    {
        return Post::query()->create([
            'user_id' => $user->id,
            'topic_id' => $topic->id,
            'title' => $title,
            'content' => 'The body explains how this post is stored.',
            'status' => true,
        ]);
    }
}
