<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPodcastWebUrl extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('podcasts', function($table)
		{
		    $table->string('web_url');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		if (Schema::hasTable('podcasts'))
		{
		    Schema::table('podcasts', function($table)
			{
			    $table->dropColumn('web_url');
			});
		}
	}

}
