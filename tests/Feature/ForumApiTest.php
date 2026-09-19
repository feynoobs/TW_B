<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_boards_endpoint_returns_board_list(): void
    {
        $this->seedBoard('news', 'ニュース', 'ニュース全般');

        $response = $this->getJson('/api/v1/boards');

        $response->assertOk()
            ->assertJsonPath('data.0.slug', 'news')
            ->assertJsonPath('data.0.name', 'ニュース');
    }

    public function test_thread_creation_creates_initial_post(): void
    {
        $board = $this->seedBoard('news', 'ニュース', 'ニュース全般');

        $response = $this->postJson('/api/v1/threads', [
            'board_id' => $board->id,
            'title' => 'サンプルスレッド',
            'body' => '最初の投稿本文です',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'サンプルスレッド')
            ->assertJsonPath('data.posts.data.0.body', '最初の投稿本文です');
    }

    public function test_posting_to_thread_creates_a_new_post_number(): void
    {
        $board = $this->seedBoard('news', 'ニュース', 'ニュース全般');
        $thread = $board->threads()->create(['title' => '既存スレッド']);
        $thread->posts()->create(['number' => 1, 'body' => '既存投稿', 'author_hash' => 'hash-1']);

        $response = $this->postJson('/api/v1/threads/' . $thread->id . '/posts', [
            'body' => '新しいレスです',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.number', 2)
            ->assertJsonPath('data.body', '新しいレスです');
    }

    public function test_post_report_is_recorded(): void
    {
        $board = $this->seedBoard('news', 'ニュース', 'ニュース全般');
        $thread = $board->threads()->create(['title' => '報告テスト']);
        $post = $thread->posts()->create(['number' => 1, 'body' => 'テスト投稿', 'author_hash' => 'hash-1']);

        $response = $this->postJson('/api/v1/posts/' . $post->id . '/reports', [
            'reason' => 'スパム',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.post_id', $post->id)
            ->assertJsonPath('data.reason', 'スパム');
    }

    private function seedBoard(string $slug, string $name, string $description): \App\Models\Board
    {
        return \App\Models\Board::create([
            'slug' => $slug,
            'name' => $name,
            'description' => $description,
        ]);
    }
}
