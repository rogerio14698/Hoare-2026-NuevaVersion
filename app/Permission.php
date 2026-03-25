<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public function modulo()
    {
        return $this->belongsTo('App\Modulo', 'model', 'id');
    }
}
