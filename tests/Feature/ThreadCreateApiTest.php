<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

    public function test_thread_create_endpoint_stores_each_attached_image(): void
    {
        Storage::fake('s3');
        $imageData = 'R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=';

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
            'title' => '画像付きスレッド',
            'content' => '画像を添付します',
            'images' => [
                [
                    'data' => 'data:image/gif;base64,' . $imageData,
                    'file_name' => 'first.gif',
                ],
                [
                    'data' => 'data:image/gif;base64,' . $imageData,
                    'file_name' => 'second.gif',
                ],
            ],
        ])
            ->assertOk()
            ->assertJsonPath('result', 'success');

        $this->assertDatabaseCount('images', 2);
        $this->assertDatabaseHas('images', [
            'file_name' => 'first.gif',
            'mime_type' => 'image/gif',
        ]);

        foreach (DB::table('images')->pluck('path') as $path) {
            Storage::disk('s3')->assertExists($path);
        }

        $storedImage = DB::table('images')->where('file_name', 'first.gif')->first();
        $this->assertSame('image/gif', $storedImage->mime_type);
        $this->assertSame(strlen(base64_decode($imageData)), $storedImage->size);
        $this->assertSame(
            base64_decode($imageData),
            Storage::disk('s3')->get($storedImage->path)
        );
    }
}
