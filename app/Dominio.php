<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dominio extends Model
{
    protected $table = 'dominios';

    Public function customer(){
        return $this->belongsTo('App\Customer','customer_id','id');
    }
    Public function agente_dominio(){
        return $this->belongsTo('App\AgenteDominio','agente_dominio_id','id');
    }

}
