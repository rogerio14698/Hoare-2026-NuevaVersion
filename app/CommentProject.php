<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommentProject extends Model
{
    protected $table = 'comment_project';

    public function project(){
        return $this->belongsTo('App\Project','project_id','id');
    }

}
