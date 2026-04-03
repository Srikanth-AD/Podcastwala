<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Podcast;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PodcastControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get('/podcast/player')->assertRedirect(route('login'));
        $this->get('/podcast/manage')->assertRedirect(route('login'));
        $this->get('/podcast/favorites')->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_player(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/podcast/player')
            ->assertStatus(200);
    }

    public function test_player_shows_unread_items_only(): void
    {
        $user = User::factory()->create();
        $podcast = Podcast::factory()->create(['user_id' => $user->id]);

        $unreadItem = Item::factory()->create([
            'user_id' => $user->id,
            'podcast_id' => $podcast->id,
            'is_mark_as_read' => false,
            'title' => 'Unread Episode',
        ]);

        $readItem = Item::factory()->read()->create([
            'user_id' => $user->id,
            'podcast_id' => $podcast->id,
            'title' => 'Read Episode',
        ]);

        $response = $this->actingAs($user)->get('/podcast/player');

        $response->assertSee('Unread Episode');
        $response->assertDontSee('Read Episode');
    }

    public function test_user_can_only_see_own_items(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $podcast1 = Podcast::factory()->create(['user_id' => $user1->id]);
        $podcast2 = Podcast::factory()->create(['user_id' => $user2->id]);

        Item::factory()->create([
            'user_id' => $user1->id,
            'podcast_id' => $podcast1->id,
            'title' => 'User1 Episode',
        ]);

        Item::factory()->create([
            'user_id' => $user2->id,
            'podcast_id' => $podcast2->id,
            'title' => 'User2 Episode',
        ]);

        $response = $this->actingAs($user1)->get('/podcast/player');

        $response->assertSee('User1 Episode');
        $response->assertDontSee('User2 Episode');
    }

    public function test_manage_page_shows_user_podcasts(): void
    {
        $user = User::factory()->create();
        $podcast = Podcast::factory()->create([
            'user_id' => $user->id,
            'name' => 'My Test Podcast',
        ]);

        $response = $this->actingAs($user)->get('/podcast/manage');

        $response->assertStatus(200);
        $response->assertSee('My Test Podcast');
    }

    public function test_delete_podcast_removes_it(): void
    {
        $user = User::factory()->create();
        $podcast = Podcast::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post('/podcast/delete', [
            'feedMachineName' => $podcast->machine_name,
        ]);

        $response->assertJson(['status' => 1]);
        $this->assertDatabaseMissing('podcasts', ['id' => $podcast->id]);
    }

    public function test_cannot_delete_other_users_podcast(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $podcast = Podcast::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->post('/podcast/delete', [
            'feedMachineName' => $podcast->machine_name,
        ]);

        $response->assertJson(['status' => 0]);
        $this->assertDatabaseHas('podcasts', ['id' => $podcast->id]);
    }

    public function test_favorites_shows_only_favorited_items(): void
    {
        $user = User::factory()->create();
        $podcast = Podcast::factory()->create(['user_id' => $user->id]);

        Item::factory()->favorite()->create([
            'user_id' => $user->id,
            'podcast_id' => $podcast->id,
            'title' => 'Fav Episode',
        ]);

        Item::factory()->create([
            'user_id' => $user->id,
            'podcast_id' => $podcast->id,
            'title' => 'Regular Episode',
        ]);

        $response = $this->actingAs($user)->get('/podcast/favorites');

        $response->assertSee('Fav Episode');
        $response->assertDontSee('Regular Episode');
    }
}
