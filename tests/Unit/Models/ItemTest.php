<?php

namespace Tests\Unit\Models;

use App\Models\Item;
use App\Models\Podcast;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_belongs_to_podcast(): void
    {
        $user = User::factory()->create();
        $podcast = Podcast::factory()->create(['user_id' => $user->id]);
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'podcast_id' => $podcast->id,
        ]);

        $this->assertEquals($podcast->id, $item->podcast->id);
    }

    public function test_item_casts_booleans(): void
    {
        $user = User::factory()->create();
        $podcast = Podcast::factory()->create(['user_id' => $user->id]);
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'podcast_id' => $podcast->id,
            'is_mark_as_read' => true,
            'is_mark_as_favorite' => false,
        ]);

        $this->assertIsBool($item->is_mark_as_read);
        $this->assertIsBool($item->is_mark_as_favorite);
        $this->assertTrue($item->is_mark_as_read);
        $this->assertFalse($item->is_mark_as_favorite);
    }

    public function test_item_casts_published_at_to_datetime(): void
    {
        $user = User::factory()->create();
        $podcast = Podcast::factory()->create(['user_id' => $user->id]);
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'podcast_id' => $podcast->id,
            'published_at' => '2024-01-15 10:30:00',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $item->published_at);
    }

    public function test_item_defaults_to_unread_and_unfavorited(): void
    {
        $user = User::factory()->create();
        $podcast = Podcast::factory()->create(['user_id' => $user->id]);
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'podcast_id' => $podcast->id,
        ]);

        $fresh = Item::find($item->id);
        $this->assertFalse($fresh->is_mark_as_read);
        $this->assertFalse($fresh->is_mark_as_favorite);
    }
}
