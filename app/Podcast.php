<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Podcast extends Model {

	protected $fillable = ['name', 'machine_name', 'feed_url', 'feed_thumbnail_location'];

}
