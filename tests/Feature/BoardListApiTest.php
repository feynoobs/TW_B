<?php

namespace Tests\Feature;

use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardListApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_boards_endpoint_returns_boards_for_group_slug(): void
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

        $this->getJson('/api/groups/general/boards')
            ->assertOk()
            ->assertJsonPath('boards.0.slug', 'news')
            ->assertJsonPath('boards.0.name', 'ニュース');
    }
}
