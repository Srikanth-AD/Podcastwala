<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Podcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Willvincent\Feeds\Facades\FeedsFacade as Feeds;

class PodcastController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $items = Item::with('podcast')
            ->where('user_id', Auth::id())
            ->where('is_mark_as_read', false)
            ->orderBy('published_at', 'desc')
            ->paginate(15);

        $podcasts = Podcast::where('user_id', Auth::id())->get();

        return view('podcasts.list', compact('items', 'podcasts'));
    }

    public function manage()
    {
        $podcasts = Podcast::where('user_id', Auth::id())->get();

        return view('podcasts.manage', compact('podcasts'));
    }

    public function settings()
    {
        return view('podcasts.settings');
    }

    public function add(Request $request)
    {
        $request->validate([
            'feed_url' => 'required|url',
        ]);

        if (!File::exists(public_path('images'))) {
            File::makeDirectory(public_path('images'));
        }

        $feed = Feeds::make($request->input('feed_url'));
        $feed->force_feed(true);
        $feed->handle_content_type();
        $podcastName = $feed->get_title();

        if (!$podcastName) {
            return redirect()->back()->withErrors(['feed_url' => 'Sorry, this feed cannot be imported. Please try another feed.']);
        }

        $feedItems = $feed->get_items();
        if (empty($feedItems) || !$feedItems[0]->get_enclosure() || !str_contains($feedItems[0]->get_enclosure()->get_type() ?? '', 'audio')) {
            return redirect()->back()->withErrors(['feed_url' => 'This doesn\'t seem to be an RSS feed with audio files. Please try another feed.']);
        }

        $podcastMachineName = Str::slug($podcastName, '');

        $imageUrl = $feed->get_image_url();
        if ($imageUrl) {
            $img = Image::read($imageUrl)->resize(100, 100);
            $img->save(public_path('images/' . $podcastMachineName . '.png'));
        }

        $podcast = Podcast::create([
            'name' => $podcastName,
            'machine_name' => $podcastMachineName,
            'feed_url' => $request->input('feed_url'),
            'feed_thumbnail_location' => $imageUrl ? 'images/' . $podcastMachineName . '.png' : null,
            'user_id' => Auth::id(),
            'web_url' => $feed->get_link(),
        ]);

        foreach ($feedItems as $item) {
            $enclosure = $item->get_enclosure();
            Item::create([
                'podcast_id' => $podcast->id,
                'user_id' => Auth::id(),
                'url' => $item->get_permalink(),
                'audio_url' => $enclosure ? $enclosure->get_link() : '',
                'title' => $item->get_title(),
                'description' => strip_tags(Str::limit($item->get_description(), 100)),
                'published_at' => $item->get_date('Y-m-d H:i:s'),
            ]);
        }

        return redirect('podcast/player')->with('success', 'Podcast added successfully!');
    }

    public function delete(Request $request)
    {
        $request->validate([
            'feedMachineName' => 'required|string',
        ]);

        $podcast = Podcast::where('machine_name', $request->input('feedMachineName'))
            ->where('user_id', Auth::id())
            ->first();

        if (!$podcast) {
            return response()->json(['status' => 0, 'message' => 'Podcast not found']);
        }

        $podcast->delete();

        return response()->json(['status' => 1, 'message' => 'Podcast was deleted']);
    }

    public function favorites()
    {
        $items = Item::with('podcast')
            ->where('user_id', Auth::id())
            ->where('is_mark_as_favorite', true)
            ->orderBy('published_at', 'desc')
            ->paginate(15);

        return view('podcasts.favorites', compact('items'));
    }

    public function autoUpdate()
    {
        Artisan::call('podcasts:update');

        return redirect('podcast/player');
    }
}
