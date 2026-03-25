<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RolesUsuario extends Model
{
    protected $table = 'role_user';

    public function roles(){
        return $this->belongsTo('App\Rol','role_id','id');
    }

    public function users(){
        return $this->hasMany('App\User','user_id','id');
    }
}
