<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->unique();
            $table->string('openid')->nullable();
            $table->string('nickname')->nullable()->change();
            $table->tinyInteger('sex')->nullable()->change();
            $table->string('province')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('country')->nullable()->change();
            $table->string('headimgurl')->nullable()->change();
            $table->string('privilege')->nullable()->change();
            $table->string('unionid')->nullable()->change();
            $table->string('password', 60);
            $table->rememberToken();
            $table->tinyInteger('super')->default(0);
            $table->timestamps();
        });
        Schema::create('user_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('realname')->nullable();
            $table->string('number')->nullable();
            $table->string('class')->nullable();
            $table->string('grade')->nullable();
            $table->string('major')->nullable();
            $table->string('school')->nullable();
            $table->tinyInteger('eductional_system')->nullable();
            $table->string('college')->nullable();
            $table->string('zone')->nullable();
            $table->string('address')->nullable();
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
        Schema::drop('users');
        Schema::drop('user_info');
    }
}
