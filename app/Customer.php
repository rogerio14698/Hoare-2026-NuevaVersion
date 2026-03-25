<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function projects(){
        return Project::where('customer_id',$this->id)
            ->where('estado_proyecto','!=',4)->count();
    }
}
