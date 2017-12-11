<?php

namespace App;

//use Illuminate\Database\Eloquent\Model;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class FormData extends Model
{
    use SoftDeletes;

    protected $connection = 'mongodb';
    protected $table = 'form_data';
    protected $dates = ['deleted_at'];
    const STATUS_INIT = 0;
    public static $status = array(0 => '初审中', 1 => '初审通过', -1 => '初审拒绝', 2 => '复审通过', -2 => '复审拒绝', 3 => '三审通过', -3 => '三审拒绝', 4 => '最终审通过', -4 => '最终审拒绝');

    /**
     * 限制查找未删除表单
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    /*public function scopeUndelete($query)
    {
        return $query->where('delete', false);
    }*/

    public function reviews()
    {
        return $this->hasMany('App\Review');
    }
    public function remarks()
    {
        return $this->hasMany('App\Remark');
    }
    public function form()
    {
        return $this->belongsTo('App\Form','fid');
    }
}
