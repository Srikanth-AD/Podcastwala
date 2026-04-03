<?php

namespace Tests\Unit\Models;

use App\Models\Item;
use App\Models\Podcast;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PodcastTest extends TestCase
{
    use RefreshDatabase;

    public function test_podcast_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $podcast = Podcast::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $podcast->user->id);
    }

    public function test_podcast_has_many_items(): void
    {
        $user = User::factory()->create();
        $podcast = Podcast::factory()->create(['user_id' => $user->id]);
        Item::factory()->count(3)->create([
            'user_id' => $user->id,
            'podcast_id' => $podcast->id,
        ]);

        $this->assertCount(3, $podcast->items);
    }

    public function test_deleting_podcast_cascades_to_items(): void
    {
        $user = User::factory()->create();
        $podcast = Podcast::factory()->create(['user_id' => $user->id]);
        Item::factory()->count(2)->create([
            'user_id' => $user->id,
            'podcast_id' => $podcast->id,
        ]);

        $podcast->delete();

        $this->assertDatabaseCount('items', 0);
    }
}
