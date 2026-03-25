<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommentTask extends Model
{
    protected $table = 'comment_task';

    public function task(){
        return $this->belongsTo('App\Task','task_id','id');
    }
}
