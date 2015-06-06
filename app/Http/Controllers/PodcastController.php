<?php namespace App\Http\Controllers;

class PodcastController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Show the podcast listen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		$feed = \Feeds::make(array('http://ttlpodcast.com/feeds/rss.xml',
	    						'http://shoptalkshow.com/feed/podcast/',
	    						'http://responsivewebdesign.com/podcast/feed.xml',
	    						'http://feeds.5by5.tv/webahead'));
	    $feed->set_cache_duration(3600);
	    $feed->set_item_limit(10);
	    $data = array(
	      'title'     => $feed->get_title(),
	      'permalink' => $feed->get_permalink(),
	      'items'     => $feed->get_items(),
	    );  

	    return view('podcast-list', $data);
	}

}
