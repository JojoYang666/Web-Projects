<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatPlatformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 微信平台信息，属于某一个用户
         */
        Schema::create('wechat_platforms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('appid')->unique();
            $table->string('appsecret')->nullable();
            $table->string('token')->nullable();
            $table->string('authorizer_refresh_token')->nullable();
            $table->string('func_info')->nullable();
            $table->string('remark')->nullable();
            $table->integer('owner_id');
            $table->timestamps();
        });
        /**
         * 用户和微信平台对应关系表，是否可以操作微信平台
         */
        Schema::create('user_platform', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('platform_id');
            $table->string('authorities')->nullable();
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
        Schema::drop('wechat_platforms');
        Schema::drop('user_platform');
    }
}
