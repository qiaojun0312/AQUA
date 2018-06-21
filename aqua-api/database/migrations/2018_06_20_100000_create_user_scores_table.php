<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserScoresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_scores', function(Blueprint $table)
		{	
			$table->increments('id')->unique();
			$table->string('openid')->default('');
			$table->integer('user_id')->default(0);
			$table->integer('score')->default(0);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_scores');
	}

}
