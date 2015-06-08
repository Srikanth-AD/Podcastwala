<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration {

	/**
	 * Setup database table for items in each podcast
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('items', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->integer('user_id')->unsigned();
			$table->string('title');
			$table->longText('description');
			$table->string('url');
			$table->string('audio_url');
			$table->date('published_at');
			$table->integer('podcast_id')->unsigned();
			$table->foreign('podcast_id')
                    ->references('id')
                    ->on('podcasts')
                    ->onDelete('cascade');
            $table->foreign('user_id')
				->references('id')
				->on('users')
				->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('items');
	}

}
