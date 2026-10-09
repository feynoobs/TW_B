<?php

namespace Tests\Feature;

use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupListApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_groups_endpoint_returns_group_list(): void
    {
        Group::create([
            'slug' => 'general',
            'name' => '一般',
            'sort' => 1,
            'status' => 0,
        ]);

        $this->getJson('/api/groups')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.slug', 'general')
            ->assertJsonPath('0.name', '一般');
    }
}
