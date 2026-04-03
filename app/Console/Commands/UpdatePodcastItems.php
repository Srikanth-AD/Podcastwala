<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\Podcast;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Willvincent\Feeds\Facades\FeedsFacade as Feeds;

class UpdatePodcastItems extends Command
{
    protected $signature = 'podcasts:update';

    protected $description = 'Fetch new episodes from subscribed podcast RSS feeds';

    public function handle(): int
    {
        $uniquePodcasts = Podcast::query()
            ->select('feed_url', 'machine_name')
            ->distinct()
            ->get();

        foreach ($uniquePodcasts as $podcast) {
            $subscribers = Podcast::where('machine_name', $podcast->machine_name)
                ->select('user_id', 'id as podcast_id')
                ->get();

            try {
                $items = Feeds::make($podcast->feed_url)->get_items();
            } catch (\Exception $e) {
                $this->error("Failed to fetch feed: {$podcast->feed_url} - {$e->getMessage()}");
                continue;
            }

            $cutoff = now()->subHours(48)->timestamp;

            foreach ($items as $item) {
                if ($item->get_date('U') <= $cutoff) {
                    break;
                }

                $enclosure = $item->get_enclosure();
                if (!$enclosure) {
                    continue;
                }

                foreach ($subscribers as $subscriber) {
                    $exists = Item::where('title', strip_tags($item->get_title()))
                        ->where('user_id', $subscriber->user_id)
                        ->where('podcast_id', $subscriber->podcast_id)
                        ->exists();

                    if (!$exists) {
                        Item::create([
                            'user_id' => $subscriber->user_id,
                            'title' => strip_tags($item->get_title()),
                            'description' => strip_tags(Str::limit($item->get_description(), 100)),
                            'published_at' => $item->get_date('Y-m-d'),
                            'url' => $item->get_permalink(),
                            'audio_url' => $enclosure->get_link(),
                            'podcast_id' => $subscriber->podcast_id,
                        ]);
                    }
                }
            }
        }

        $this->info('Podcast items updated successfully.');

        return Command::SUCCESS;
    }
}
