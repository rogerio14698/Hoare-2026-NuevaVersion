<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{

    protected $dates = ['fechaentrega_tarea', 'fechainicio_tarea'];

    public function project(){
          return $this->belongsTo('App\Project','project_id','id');
    }

    public function users(){
        return $this->belongsToMany('App\User');
    }

    public function comments(){
        return $this->belongsToMany('App\Comment', 'comment_task');
    }

    public function taskstate(){
        return $this->belongsTo('App\TaskState','estado_tarea','id');
    }

    public function firstuser(){
        return TaskUser::where('task_id',$this->id)->first();
    }

}
