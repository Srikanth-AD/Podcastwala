<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMarkItemsAsRead extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('items', function($table)
		{
		    $table->boolean('is_mark_as_read');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		if (Schema::hasTable('items'))
		{
		    Schema::table('items', function($table)
			{
			    $table->dropColumn('is_mark_as_read');
			});
		}
	}

}
