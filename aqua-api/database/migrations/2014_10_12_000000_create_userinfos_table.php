<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserinfosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_infos', function(Blueprint $table)
		{	
			$table->increments('id');
			$table->string('openid')->unique();
			$table->string('name')->default('');
			$table->string('nickname')->default('');
			$table->string('phone')->default('');
			$table->string('password')->default('');
			$table->string('access_token')->default('');
			$table->string('code')->default('');
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
		Schema::drop('user_infos');
	}

}
