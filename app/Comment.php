<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public function users(){
        return $this->belongsToMany('App\User')->withTimestamps();
    }

    public function projects(){
        return $this->belongsToMany('App\Project')->withTimestamps();
    }

    public function tasks(){
        return $this->belongsToMany('App\Task')->withTimestamps();
    }

    public function comment_user(){
        return $this->belongsTo('App\CommentUser', 'id', 'comment_id');
    }

    public function comment_project(){
        return $this->belongsTo('App\CommentProject', 'id', 'comment_id');
    }

    public function comment_task(){
        return $this->belongsTo('App\CommentTask', 'id', 'comment_id');
    }
}
