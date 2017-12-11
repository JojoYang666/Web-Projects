<?php
/**
 * Created by PhpStorm.
 * User: lyndon
 * Date: 17-3-16
 * Time: 下午2:50
 */

namespace App;


use Illuminate\Support\Facades\Auth;

class RedisKey
{


    public static function inviteMsgNumKey()
    {
        return 'inviteMsgNumKey_'.Auth::user()->id;
    }

    public static function formDataKey()
    {
        return 'formDataKey_'.Auth::user()->id;
    }

    public static function userInfoKey($openid)
    {
        return 'userInfoKey_'.$openid;
    }
}