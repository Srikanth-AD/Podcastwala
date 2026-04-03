<?php

namespace Tests\Unit\Models;

use App\Models\Item;
use App\Models\Podcast;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_many_podcasts(): void
    {
        $user = User::factory()->create();
        $podcast = Podcast::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->podcasts->contains($podcast));
        $this->assertCount(1, $user->podcasts);
    }

    public function test_user_has_many_items_through_podcasts(): void
    {
        $user = User::factory()->create();
        $podcast = Podcast::factory()->create(['user_id' => $user->id]);
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'podcast_id' => $podcast->id,
        ]);

        $this->assertTrue($user->items->contains($item));
    }

    public function test_user_password_is_hidden(): void
    {
        $user = User::factory()->create();
        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }
}
