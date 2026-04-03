<?php

namespace Database\Factories;

use App\Models\Podcast;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PodcastFactory extends Factory
{
    protected $model = Podcast::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);
        return [
            'name' => $name,
            'machine_name' => Str::slug($name, ''),
            'feed_url' => fake()->url(),
            'web_url' => fake()->url(),
            'feed_thumbnail_location' => null,
            'user_id' => User::factory(),
        ];
    }
}
