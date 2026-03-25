<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommentUser extends Model
{
    protected $table = 'comment_user';

    public function user(){
        return $this->belongsTo('App\User','user_id','id');
    }
}
