<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskUser extends Model
{
    protected $table = 'task_user';

    public function user(){
        return $this->belongsTo('App\User','user_id','id');
    }
}
