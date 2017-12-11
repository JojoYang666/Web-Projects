<?php

namespace App\Wechat;

use Jenssegers\Mongodb\Eloquent\Model;

class WechatPlatform extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'wechat_platforms';

    public function owner()
    {
        return $this->belongsTo('App\User');
    }

    /*
     * 公众号授权给开发者的权限集列表，ID为1到15时分别代表：
     */
    public static $func = [
        '',
        '消息管理权限',
        '用户管理权限',
        '帐号服务权限',
        '网页服务权限',
        '微信小店权限',
        '微信多客服权限',
        '群发与通知权限',
        '微信卡券权限',
        '微信扫一扫权限',
        '微信连WIFI权限',
        '素材管理权限',
        '微信摇周边权限',
        '微信门店权限',
        '微信支付权限',
        '自定义菜单权限',
    ];


}
