<?php namespace App\Http\Controllers;
use App\Item;
use Auth;
use DB;
use Request;

class ItemController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->middleware('auth');
	}

	/**
	 * [markAsRead mark a podcast item as read]
	 * @return array
	 */
	public function markAsRead() {
		$result['status'] = 0;
		$result['message'] = 'Something went wrong, please try again';

		$itemId = trim(strip_tags(Request::get('itemId')));

		$item = DB::table('items')->select('user_id')
		                          ->where('user_id', '=', Auth::user()->id)
		                          ->where('id', '=', $itemId)
		                          ->first();

		// if item with id exists in DB and is owned by the authenticated user
		if ($item) {
			$podcastItem = Item::findOrFail($itemId);
			$podcastItem->is_mark_as_read = 1;
			$podcastItem->save();
			$result['status'] = 1;
			$result['message'] = 'This item has been marked as read';
		}
		return $result;
	}

	/**
	 * [markAllPrevAsRead mark all previous item in a podcast as read]
	 * @return array
	 */
	public function markAllPrevAsRead() {
		$result['status'] = 0;
		$result['message'] = 'Something went wrong, please try again';

		$itemId = trim(strip_tags(Request::get('itemId')));

		$item = DB::table('items')->select('published_at', 'podcast_id')
		                          ->where('user_id', '=', Auth::user()->id)
		                          ->where('id', '=', $itemId)
		                          ->first();
		if ($item) {
			$items = DB::table('items')
				->select('id', 'published_at')
				->where('user_id', '=', Auth::user()->id)
				->where('podcast_id', '=', $item->podcast_id)
				->where('is_mark_as_read', '!=', 1)
				->where('published_at', '<', $item->published_at)
				->get();

			$itemIds = [];

			foreach ($items as $record) {
				$record = Item::findOrFail($record->id);
				$record->is_mark_as_read = 1;
				$record->save();

				array_push($itemIds, $record->id);
			}

			// @todo need to limit the item ids sent to 15 (descending order)

			$result['status'] = 1;
			$result['data'] = $itemIds;
			$result['message'] = 'Previous items in this podcast has been marked as read';
		}
		return $result;
	}

	/**
	 * [markAsFavorite mark a podcast item as favorite]
	 * @return array
	 */
	public function markAsFavorite() {
		$result['status'] = 0;
		$result['message'] = 'Something went wrong, please try again';

		$itemId = trim(strip_tags(Request::get('itemId')));

		$item = DB::table('items')->select('user_id')
		                          ->where('user_id', '=', Auth::user()->id)
		                          ->where('id', '=', $itemId)
		                          ->first();

		// if item with id exists in DB and is owned by the authenticated user
		if ($item) {

			$podcastItem = Item::findOrFail($itemId);
			$result['currentValue'] = !$podcastItem->is_mark_as_favorite;
			$podcastItem->is_mark_as_favorite = !$podcastItem->is_mark_as_favorite; // opposite of current value
			$podcastItem->save();

			$result['status'] = 1;
			$result['message'] = 'This item has been updated';
		}
		return $result;
	}

	/**
	 * Return a view to display item search results for a given query
	 */

	public function search() {
		$query = trim(strip_tags(Request::get('query')));
		$items = DB::table('items')
			->where('title', 'LIKE', "%$query%")
			->orWhere('description', 'LIKE', "%$query%")
			->get();

		return view('items.searchresults')->with([
			'items' => $items,
			'query' => $query,
		]);
	}
}