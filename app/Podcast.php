<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Podcast extends Model {

	protected $fillable = ['name', 'user_id', 'machine_name', 'web_url', 'feed_url', 'feed_thumbnail_location'];

	/**
	 * A podcast has many items
	 */
	public function items()
    {
        return $this->hasMany('App\Item');
    }

}
