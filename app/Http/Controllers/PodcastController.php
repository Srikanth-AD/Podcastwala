<?php namespace App\Http\Controllers;
use Feeds;
use DB;
use Request;
use App\Podcast;
use Illuminate\Support\Facades\Redirect;

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
	 * Return the list of podcasts to the "podcast-list" view
	 * @return  view
	 */
	public function index()
	{
		$feedUrls = DB::table('podcasts')->select('feed_url')->lists('feed_url');		
		
		$feed = Feeds::make($feedUrls);
	    $feed->set_cache_duration(3600);
	    $feed->set_item_limit(5);
	    $data = array(
	      'title'     => $feed->get_title(),
	      'permalink' => $feed->get_permalink(),
	      'items'     => $feed->get_items(),
	    );  

	    return view('podcasts.list', $data);
	}

	/**
	 * Return a view to manage podcasts
	 * @return view
	 */
	public function manage()
	{
		return view('podcasts.manage');
	}

	/**
	 * Return a view to manage settings
	 * @return view
	 */
	public function settings()
	{
		return view('podcasts.settings');
	}

	/**
 	* Add a podcast to the database
 	*/
	public function add()
	{
		if(Request::get('feed_url'))
        {
        	$feed = Feeds::make(Request::get('feed_url'));
        	$feed->force_feed(true);
        	$feed->handle_content_type();
        	$podcastName = $feed->get_title();

        	if($podcastName && $podcastName != '')
        	{
        		Podcast::create([
                'name' => $podcastName ? $podcastName : '',
				'machine_name' => strtolower(preg_replace('/\s+/', '', $podcastName)),
                'feed_url' => Request::get('feed_url')
            	]);
            	// @todo Podcast was added
            	return redirect('podcast/player');
        	} 
        	else 
        	{
        		// @todo Could not add podcast
        		return 'Sorry, this feed cannot be imported. Please try another feed';
        	}
        }
	}

	/**
	 * Delete a podcast
	 */
	public function delete()
	{
		if(Request::get('feedMachineName'))
        {
        	$podcastId = DB::table('podcasts')->select('id','machine_name')->where('machine_name','=', 'shoptalk')->first()->id;
        	if($podcastId)
        	{
        		$podcast = Podcast::find($podcastId);
        		$podcast->delete();
        		// @todo success
        	} 
        	else 
        	{
        		// @todo db delete failed
        	}
        }
        else {
        	// @todo invalid feed machine name given
        }
	}

}
