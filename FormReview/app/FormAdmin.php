<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FormAdmin extends Model
{
    protected $table = 'form_admin';

    const INIT=0;
    const ACCEPT=1;
    const REFUSE = 2;
    const FORBIDDEN = 3;

    /**
     * 筛选掉不是自己邀请的
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotSelfInvite($query)
    {
        return $query->where('inviter_id', '!=', Auth::user()->id);
    }
    /**
     * 不是邀请自己
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotInviteSelf($query)
    {
        return $query->where('user_id', '!=', Auth::user()->id);
    }
    /**
     * 未删除自己邀请的消息
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotDelUser($query)
    {
        return $query->where('user_del_msg', false);
    }
    /**
     * 未删除邀请自己的消息
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotDelInviter($query)
    {
        return $query->where('inviter_del_msg', false);
    }

    /**
     * 未处理
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotHandle($query)
    {
        return $query->where('handle', self::INIT);
    }
    /**
     * 已接收
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAccepted($query)
    {
        return $query->where('handle', self::ACCEPT);
    }

    public function inviter()
    {
        return $this->belongsTo('App\User','inviter_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function form()
    {
        return $this->belongsTo('App\Form');
    }
}
