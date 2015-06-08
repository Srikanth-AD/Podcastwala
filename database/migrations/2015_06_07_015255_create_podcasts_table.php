<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePodcastsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('podcasts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->string('name');
			$table->string('machine_name');
			$table->string('feed_url');
			$table->string('feed_thumbnail_location')->nullable();
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')
				->references('id')
				->on('users')
				->onDelete('cascade');
			$table->unique(['machine_name','user_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('podcasts');
	}

}
