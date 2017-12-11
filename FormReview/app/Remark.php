<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Remark extends Model
{
    protected $connection = 'mongodb';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function formData()
    {
        return $this->belongsTo('App\FormData');
    }
}
