<?php

namespace Tests\Feature;

use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResponseListApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_thread_responses_endpoint_returns_responses_in_id_order(): void
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

        $thread = $board->threads()->create([
            'title' => 'テストスレッド',
            'modified_at' => now(),
            'status' => 0,
        ]);

        $firstResponse = $thread->responses()->create([
            'content' => '最初のレスポンス',
            'user_agent' => 'phpunit',
            'ip_address' => '127.0.0.1',
        ]);

        $secondResponse = $thread->responses()->create([
            'content' => '次のレスポンス',
            'user_agent' => 'phpunit',
            'ip_address' => '127.0.0.1',
        ]);

        $otherThread = $board->threads()->create([
            'title' => '別のスレッド',
            'modified_at' => now(),
            'status' => 0,
        ]);

        $otherThread->responses()->create([
            'content' => '別スレッドのレスポンス',
            'user_agent' => 'phpunit',
            'ip_address' => '127.0.0.1',
        ]);

        $this->postJson("/api/threads/{$thread->id}/responses")
            ->assertOk()
            ->assertJsonPath('id', $thread->id)
            ->assertJsonCount(2, 'responses')
            ->assertJsonPath('responses.0.id', $firstResponse->id)
            ->assertJsonPath('responses.0.content', '最初のレスポンス')
            ->assertJsonPath('responses.1.id', $secondResponse->id)
            ->assertJsonPath('responses.1.content', '次のレスポンス');
    }

    public function test_thread_responses_endpoint_returns_empty_json_for_a_missing_thread(): void
    {
        $this->postJson('/api/threads/999/responses')
            ->assertOk()
            ->assertExactJson([]);
    }
}
