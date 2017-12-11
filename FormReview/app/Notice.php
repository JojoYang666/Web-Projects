<?php

namespace App;

//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model;

class Notice extends Model
{
    const TYPE_REVIEW = 1;//审核类型
    const TYPE_ADMIN = 2;//通知管理员类型
    const WAY_WECHAT=1;
    const CONTENT_EVALUATION = '{EVALUATION}';//微信通知
    const CONTENT_NICKNAME = '{NICKNAME}';//微信昵称
    const CONTENT_USER_LINK = '{USER_LINK}';
    const CONTENT_CUSTOM = '{CUSTOM}';
    const CONTENT_BR = '<br>';
    const CONTENT_TIME = '{TIME}';//表单链接

    protected $connection = 'mongodb';
    protected $table = 'notices';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function form()
    {
        return $this->belongsTo('App\Form');
    }
}
