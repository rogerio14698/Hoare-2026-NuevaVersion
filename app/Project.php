<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
  public function customer()
      {
          return $this->belongsTo('App\Customer');
      }

  public function user()
      {
          return $this->belongsTo('App\User');
      }

  public function tasks()
          {
              return $this->hasmany('App\Task');
          }

    public function comments(){
        return $this->belongsToMany('App\Comment');
    }

    public function projectstate(){
        return $this->belongsTo('App\ProjectState','estado_proyecto','id');
    }

    public function tareas_abiertas(){
      return Task::where('project_id',$this->id)
          ->where('estado_tarea','<>',4)->count();
    }
    
}
