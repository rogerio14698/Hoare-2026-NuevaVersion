<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskState extends Model
{
    protected $table = 'task_states';
    public function task(){
        return $this->hasMany('App\Task','id','estado_tarea');
    }
}
