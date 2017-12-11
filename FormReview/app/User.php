<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phone', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function forms()
    {
        //未接受的不要出现
        return $this->belongsToMany('App\Form', 'form_admin', 'user_id', 'form_id')->wherePivot('handle', true);
    }
    public function ownForms()
    {
        //未接受的不要出现
        return $this->hasMany('App\Form', 'creator');
    }

    /**
     * 目前只显示用户自己创建的，后期有时间再加增加微信平台管理员功能
     */
    public function platforms()
    {
//        return $this->belongsToMany('App\Wechat\WechatPlatform', 'user_platform', 'user_id', 'platform_id');
        return $this->hasMany('App\Wechat\WechatPlatform','owner_id');
    }
    public function formadmins()
    {
        return $this->hasMany('App\FormAdmin', 'userId', 'users.id');
    }
    public function reviews()
    {
        return $this->hasMany('App\Review');
    }
    public function remarks()
    {
        return $this->hasMany('App\Remark');
    }
    public function info()
    {
        return $this->hasOne('App\UserInfo');
    }

    public function data()
    {
        return $this->hasMany('App\FormData');
    }

}
