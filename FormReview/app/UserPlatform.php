<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPlatform extends Model
{
    protected $table = 'user_platform';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function platform()
    {
        return $this->belongsTo('App\Wechat\WechatPlatform');
    }
}
