<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_groups_endpoint_returns_group_list(): void
    {
        \App\Models\Group::create([
            'slug' => 'general',
            'name' => '一般',
            'sort' => 1,
            'status' => 0,
        ]);

        $response = $this->getJson('/api/groups');

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.slug', 'general')
            ->assertJsonPath('0.name', '一般');
    }

    public function test_group_boards_endpoint_returns_boards_for_group_slug(): void
    {
        $group = \App\Models\Group::create([
            'slug' => 'general',
            'name' => '一般',
            'sort' => 1,
            'status' => 0,
        ]);

        $group->boards()->create([
            'group_id' => $group->id,
            'slug' => 'news',
            'name' => 'ニュース',
            'name_nns' => 'ニュース',
            'sort' => 1,
            'status' => 0,
        ]);

        $response = $this->getJson('/api/groups/general/boards');

        $response->assertOk()
            ->assertJsonPath('boards.0.slug', 'news')
            ->assertJsonPath('boards.0.name', 'ニュース');
    }

    public function test_board_threads_endpoint_returns_threads_for_board_slug(): void
    {
        $group = \App\Models\Group::create([
            'slug' => 'general',
            'name' => '一般',
            'sort' => 1,
            'status' => 0,
        ]);

        $board = $group->boards()->create([
            'group_id' => $group->id,
            'slug' => 'news',
            'name' => 'ニュース',
            'name_nns' => 'ニュース',
            'sort' => 1,
            'status' => 0,
        ]);

        $board->threads()->create([
            'title' => 'テストスレッド',
            'modified_at' => now(),
            'status' => 0,
        ]);

        $response = $this->getJson('/api/boards/news/threads');

        $response->assertOk()
            ->assertJsonPath('slug', 'news')
            ->assertJsonPath('threads.0.title', 'テストスレッド');
    }

    public function test_thread_create_endpoint_creates_thread_and_initial_response(): void
    {
        $group = \App\Models\Group::create([
            'slug' => 'general',
            'name' => '一般',
            'sort' => 1,
            'status' => 0,
        ]);

        $group->boards()->create([
            'group_id' => $group->id,
            'slug' => 'news',
            'name' => 'ニュース',
            'name_nns' => 'ニュース',
            'sort' => 1,
            'status' => 0,
        ]);

        $response = $this->postJson('/api/boards/news/thread/create', [
            'title' => '新しいスレッド',
            'content' => '最初の投稿です',
            'name' => 'テストユーザー',
            'mail' => null,
            'hash' => 'hash-123',
            'user_agent' => 'phpunit',
            'user_domain' => 'example.com',
            'ip_address' => '127.0.0.1',
        ]);

        $response->assertOk()
            ->assertJsonPath('result', 'success');

        $this->assertDatabaseHas('threads', [
            'title' => '新しいスレッド',
        ]);

        $thread = \App\Models\Thread::query()->where('title', '新しいスレッド')->first();

        $this->assertNotNull($thread);
        $this->assertDatabaseHas('responses', [
            'thread_id' => $thread->id,
            'content' => '最初の投稿です',
            'name' => 'テストユーザー',
        ]);
    }
}
