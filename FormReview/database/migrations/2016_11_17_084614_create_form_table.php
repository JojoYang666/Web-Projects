<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('pic')->nullable();
            $table->integer('creator')->index();
            $table->boolean('publish')->default(false);
            $table->boolean('delete')->default(false);
            $table->string('wechat')->nullable();
            $table->boolean('filterBlacklist')->default(false);
            $table->boolean('showReview')->default(false);//是否展示管理员审核意见
            $table->boolean('showRemark')->default(false);//是否展示管理员评论
            $table->smallInteger('reviewTimes')->default(0);
            $table->longText('fieldNames')->nullable()->change();
            $table->longText('fields')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->text('customView')->nullable();
            $table->tinyInteger('customStatus')->default(0);
            $table->string('customReview')->nullable();
            $table->tinyInteger('limitTimes')->default(-1)->comment('限制提交次数');
            $table->string('limitBy')->default('IP')->comment('根据什么来限制次数');
        });

        Schema::create('form_admin', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_id');
            $table->integer('user_id');
            $table->integer('inviter_id');
            $table->string('authorities')->nullable();
            $table->string('remark')->nullable();
            $table->string('stage')->nullable();
            $table->string('conditions',1024)->nullable();
            $table->tinyInteger('handle')->default(0);
            $table->tinyInteger('inviter_del_msg')->default(0)->comment('邀请者删除消息');
            $table->tinyInteger('user_del_msg')->default(0)->comment('被邀请者删除消息');
            $table->timestamps();
        });

        Schema::create('form_data', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_id');
            $table->smallInteger('status')->default(0);
            $table->string('data')->nullable();
            $table->string('openid')->nullable();
            $table->ipAddress('ip');
            $table->softDeletes();
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
        Schema::drop('forms');
        Schema::drop('form_admin');
        Schema::drop('form_data');
    }
}
