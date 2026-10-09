<?php

namespace Tests\Feature;

use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreadListApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_board_threads_endpoint_returns_threads_for_board_slug(): void
    {
        $group = Group::create([
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

        $this->getJson('/api/boards/news/threads')
            ->assertOk()
            ->assertJsonPath('slug', 'news')
            ->assertJsonPath('threads.0.title', 'テストスレッド');
    }
}
