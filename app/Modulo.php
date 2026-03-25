<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    public function image_sizes()
    {
        return $this->belongsToMany('App\ImageSize');
    }
}
