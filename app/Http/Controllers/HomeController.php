<?php namespace App\Http\Controllers;

class HomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

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
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('home');
	}

	public function demo() 
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

	    return view('feed', $data);
  	}

}
