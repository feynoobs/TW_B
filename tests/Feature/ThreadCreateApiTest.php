<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreadCreateApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_thread_create_endpoint_creates_thread_and_initial_response(): void
    {
        $group = Group::create([
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

        $this->postJson('/api/boards/news/thread/create', [
            'title' => '新しいスレッド',
            'content' => '最初の投稿です',
            'name' => 'テストユーザー',
            'mail' => null,
            'hash' => 'hash-123',
            'user_agent' => 'phpunit',
            'user_domain' => 'example.com',
            'ip_address' => '127.0.0.1',
        ])
            ->assertOk()
            ->assertJsonPath('result', 'success');

        $this->assertDatabaseHas('threads', [
            'title' => '新しいスレッド',
        ]);

        $thread = Thread::query()->where('title', '新しいスレッド')->first();

        $this->assertNotNull($thread);
        $this->assertDatabaseHas('responses', [
            'thread_id' => $thread->id,
            'content' => '最初の投稿です',
            'name' => 'テストユーザー',
        ]);
    }
}
