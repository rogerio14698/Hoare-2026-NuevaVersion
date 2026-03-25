<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectState extends Model
{
    public function project(){
        return $this->hasMany('App\Project','id','estado_proyecto');
    }
}
