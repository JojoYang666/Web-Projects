<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserInfo extends Authenticatable
{

    protected $table = 'user_info';

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
