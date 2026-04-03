<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Podcast;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->text(100),
            'url' => fake()->url(),
            'audio_url' => fake()->url() . '/episode.mp3',
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'is_mark_as_read' => false,
            'is_mark_as_favorite' => false,
            'user_id' => User::factory(),
            'podcast_id' => Podcast::factory(),
        ];
    }

    public function read(): static
    {
        return $this->state(fn () => ['is_mark_as_read' => true]);
    }

    public function favorite(): static
    {
        return $this->state(fn () => ['is_mark_as_favorite' => true]);
    }
}
