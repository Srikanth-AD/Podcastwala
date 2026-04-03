<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Podcast;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_mark_item_as_read(): void
    {
        $user = User::factory()->create();
        $podcast = Podcast::factory()->create(['user_id' => $user->id]);
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'podcast_id' => $podcast->id,
        ]);

        $response = $this->actingAs($user)->post('/item/mark-as-read', [
            'itemId' => $item->id,
        ]);

        $response->assertJson(['status' => 1]);
        $this->assertTrue($item->fresh()->is_mark_as_read);
    }

    public function test_cannot_mark_other_users_item_as_read(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $podcast = Podcast::factory()->create(['user_id' => $user2->id]);
        $item = Item::factory()->create([
            'user_id' => $user2->id,
            'podcast_id' => $podcast->id,
        ]);

        $response = $this->actingAs($user1)->post('/item/mark-as-read', [
            'itemId' => $item->id,
        ]);

        $response->assertJson(['status' => 0]);
        $this->assertFalse($item->fresh()->is_mark_as_read);
    }

    public function test_toggle_favorite(): void
    {
        $user = User::factory()->create();
        $podcast = Podcast::factory()->create(['user_id' => $user->id]);
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'podcast_id' => $podcast->id,
        ]);

        // Mark as favorite
        $response = $this->actingAs($user)->post('/item/mark-as-favorite', [
            'itemId' => $item->id,
        ]);

        $response->assertJson(['status' => 1, 'currentValue' => true]);
        $this->assertTrue($item->fresh()->is_mark_as_favorite);

        // Unfavorite
        $response = $this->actingAs($user)->post('/item/mark-as-favorite', [
            'itemId' => $item->id,
        ]);

        $response->assertJson(['status' => 1, 'currentValue' => false]);
        $this->assertFalse($item->fresh()->is_mark_as_favorite);
    }

    public function test_mark_all_previous_as_read(): void
    {
        $user = User::factory()->create();
        $podcast = Podcast::factory()->create(['user_id' => $user->id]);

        $oldItem = Item::factory()->create([
            'user_id' => $user->id,
            'podcast_id' => $podcast->id,
            'published_at' => now()->subDays(5),
        ]);

        $currentItem = Item::factory()->create([
            'user_id' => $user->id,
            'podcast_id' => $podcast->id,
            'published_at' => now()->subDay(),
        ]);

        $newerItem = Item::factory()->create([
            'user_id' => $user->id,
            'podcast_id' => $podcast->id,
            'published_at' => now(),
        ]);

        $response = $this->actingAs($user)->post('/item/mark-all-prev-read', [
            'itemId' => $currentItem->id,
        ]);

        $response->assertJson(['status' => 1]);

        $this->assertTrue($oldItem->fresh()->is_mark_as_read);
        $this->assertFalse($currentItem->fresh()->is_mark_as_read);
        $this->assertFalse($newerItem->fresh()->is_mark_as_read);
    }

    public function test_search_scoped_to_authenticated_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $podcast1 = Podcast::factory()->create(['user_id' => $user1->id]);
        $podcast2 = Podcast::factory()->create(['user_id' => $user2->id]);

        Item::factory()->create([
            'user_id' => $user1->id,
            'podcast_id' => $podcast1->id,
            'title' => 'Laravel Tips',
        ]);

        Item::factory()->create([
            'user_id' => $user2->id,
            'podcast_id' => $podcast2->id,
            'title' => 'Laravel Secrets',
        ]);

        $response = $this->actingAs($user1)->get('/item/search?query=Laravel');

        $response->assertSee('Laravel Tips');
        $response->assertDontSee('Laravel Secrets');
    }

    public function test_search_requires_authentication(): void
    {
        $this->get('/item/search?query=test')->assertRedirect(route('login'));
    }
}
