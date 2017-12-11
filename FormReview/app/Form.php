<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Form
 *
 * @property int id
 * @property string title
 * @property boolean filterBlacklist
 * @package App
 */
class Form extends Model
{
    use SoftDeletes;
    //自定义页面常亮
    const REVIEW_WAITING = 1;//等待审核
    const REVIEW_PASS = 2;//审核通过
    const REVIEW_REFUSE = 3;//审核拒绝
    const CUSTOM_STOP = 4;//停用自定义页面
    const CUSTOM_STATUS = 'customStatus';
    public static $customStatus=[
        0 => '未使用自定义页面',
        self::REVIEW_WAITING => '等待审核',
        self::REVIEW_PASS => '审核通过',
        self::REVIEW_REFUSE => '审核拒绝',
        self::CUSTOM_STOP => '停用自定义页面',

    ];

    protected $dates = ['deleted_at'];

    const INVITE_ACCEPT = 1;
    const INVITE_DENY = 2;

    const STATUS_INIT = 0;
    public static $status = array(0 => '初审中', 1 => '初审通过', -1 => '初审拒绝', 2 => '复审通过', -2 => '复审拒绝', 3 => '三审通过', -3 => '三审拒绝', 4 => '最终审通过', -4 => '最终审拒绝');

    public static $authorities=[
        'show' => '查看概述',//unused
        'update' => '编辑表单',
        'datalist' => '查看统计数据',
        'review'=>'审批',
        'remark'=>'评论',
        'report' => '查看报表',
    ];

    public function owner()
    {
        return $this->belongsTo('App\User','creator');
    }
    public function users()
    {
        return $this->belongsToMany('App\User','form_admin','form_id','user_id');
    }
    public function admins()
    {
        return $this->hasMany('App\FormAdmin');
    }
    public function notices()
    {
        return $this->hasMany('App\Notice');
    }

    public function formData()
    {
        return $this->hasMany('App\FormData','fid');
    }
    public function remindNotice()
    {
        return $this->hasOne('App\Notice');
    }

    /**
     * 限制查找未删除表单
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUndelete($query)
    {
        return $query;
    }
    /**
     * 限制查找已发布
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublish($query)
    {
        return $query->where('publish', 1);
    }
    /**
     * 限制查找用为微信中
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWechat($query)
    {
        return $query->where('wechat', 1);
    }
}
