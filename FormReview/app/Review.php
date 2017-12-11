<?php

namespace App;

//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model;

class Review extends Model
{
    const PASS = 'pass';
    const REFUSE = 'refuse';

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
