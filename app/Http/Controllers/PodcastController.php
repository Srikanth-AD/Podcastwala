<?php namespace App\Http\Controllers;
use App\Item;
use App\Podcast;
use Auth;
use DB;
use Feeds;
use File;
use Illuminate\Support\Facades\Redirect;
use Image;
use Request;

class PodcastController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->middleware('auth');
	}

	/**
	 * Return the list of podcast items that are un-read
	 * @return  view
	 */
	public function index() {
		$items = DB::table('items')
			->where('user_id', '=', Auth::user()->id)
			->where('is_mark_as_read', '!=', 1)
			->orderBy('published_at', 'desc')->paginate(15);

		$podcasts = DB::table('podcasts')
			->where('user_id', '=', Auth::user()->id)
			->get();

		$data = array(
			'podcasts' => $podcasts,
			'items' => $items,
		);

		return view('podcasts.list', $data);
	}

	/**
	 * Return a view to manage podcasts
	 * @return view
	 */
	public function manage() {
		return view('podcasts.manage');
	}

	/**
	 * Return a view to manage settings
	 * @return view
	 */
	public function settings() {
		return view('podcasts.settings');
	}

	/**
	 * Add a podcast to the database
	 */
	public function add() {

		// create "images" directory under "public" directory if it doesn't exist
		if (!File::exists(public_path() . '/images')) {
			File::makeDirectory(public_path() . '/images');
		}

		if (Request::get('feed_url')) {
			$feed = Feeds::make(Request::get('feed_url'));
			$feed->force_feed(true);
			$feed->handle_content_type();
			$podcastName = $feed->get_title();

			if ($podcastName && $podcastName != '') {
				// check if the feed's first item has an audio file in its enclosure
				if (strpos($feed->get_items()[0]->get_enclosure()->get_type(), 'audio') !== false) {
					$podcastMachineName = strtolower(preg_replace('/\s+/', '', $podcastName));

					// Save the feed thumbnail to file system and save file path to database
					$img = Image::make($feed->get_image_url())->resize(100, 100);
					$img->save(public_path('images/' . $podcastMachineName . '.png'));

					Podcast::create([
						'name' => $podcastName ? $podcastName : '',
						'machine_name' => $podcastMachineName,
						'feed_url' => Request::get('feed_url'),
						'feed_thumbnail_location' => 'images/' . $podcastMachineName . '.png',
						'user_id' => Auth::user()->id,
						'web_url' => $feed->get_link(),
					]);

					foreach ($feed->get_items() as $item) {
						Item::create([
							'podcast_id' => DB::table('podcasts')
								->select('id', 'machine_name')
								->where('machine_name', '=', $podcastMachineName)->first()->id,
							'user_id' => Auth::user()->id,
							'url' => $item->get_permalink(),
							'audio_url' => $item->get_enclosure()->get_link(),
							'title' => $item->get_title(),
							'description' => strip_tags(str_limit($item->get_description(), 100)),
							'published_at' => $item->get_date('Y-m-d H:i:s'),
						]);
					}

					// @todo Podcast was added
					return redirect('podcast/player');
				} else {
					// @todo flash msg
					return 'This doesn\'t seem to be an RSS feed with audio files. Please try another feed.';
				}
			} else {
				// @todo Could not add podcast
				return 'Sorry, this feed cannot be imported. Please try another feed';
			}
		} else {
			// @todo use validation
			return 'Invalid feed URL given.';
		}
	}

	/**
	 * Delete a podcast
	 */
	public function delete() {
		$result = ['status' => 0, 'message' => 'Something went wrong'];

		if (Request::get('feedMachineName')) {
			$podcastId = DB::table('podcasts')->select('id', 'machine_name')
			                                  ->where('machine_name', '=', Request::get('feedMachineName'))
			                                  ->where('user_id', '=', Auth::user()->id)
			                                  ->first()->id;

			if ($podcastId) {
				$podcast = Podcast::find($podcastId);
				$podcast->delete();
				// @todo success
				$result['status'] = 1;
				$result['message'] = 'Podcast was deleted';
			} else {
				// @todo DB delete failed
			}
		} else {
			// @todo invalid feed machine name given
		}
		return $result;
	}

	/**
	 * Return the list of favorites for a user to a view
	 * @return [type] [description]
	 */
	public function favorites() {
		$items = DB::table('items')
			->where('user_id', '=', Auth::user()->id)
			->where('is_mark_as_favorite', '!=', 0)
			->orderBy('published_at', 'desc')->paginate(15);

		$data = array(
			'items' => $items,
		);

		return view('podcasts.favorites', $data);
	}

}
