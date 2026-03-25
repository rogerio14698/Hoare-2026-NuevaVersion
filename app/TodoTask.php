<?php

namespace App;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
//use jpmurray\LaravelCountdown\Countdown;
use DateTime;

class TodoTask extends Model
{
    protected $table = 'todotasks';

    public function users(){
        return $this->belongsToMany('App\User');
    }

    /**
     * Esta función devuelve un array con la etiqueta de tiempo restante y el tiempo que queda para finalizar la tarea
     */
    public function devuelveTiempoRestante(){
        Carbon::setLocale('es');
        $fecha_finaliza_todo = Carbon::parse($this->fechaentrega_tarea);
        $timezone = 'Europe/Madrid';
        date_default_timezone_set($timezone);
        $fecha_ahora = Carbon::parse(date('Y-m-d H:i:s'),$timezone);
        $diferencia_horas = $fecha_ahora->diffInHours($fecha_finaliza_todo);
        $texto = '';
        $color = '';
        $semanas = 0;
        $dias = 0;
        $horas = 0;
        $minutos = 0;

        $dt1 = new DateTime($this->fechaentrega_tarea);
        $dt2 = new DateTime(date('Y-m-d H:i:s'));
        $fechaF = $dt1->diff($dt2);
        //$dt1 = new DateTime(date('Y-m-d H:i:s'));
        //$dt2 = new DateTime($this->fechaentrega_tarea);
        //$i = $dt1->diff($dt2);
        $texto = $fechaF->format(($fechaF->y>0?'%y año(s)':'') . ($fechaF->m>0?' %m mese(s)':'') . ($fechaF->d>0?' %d dia(s)':'') . ($fechaF->h>0?' %h hora(s)':'') . ($fechaF->i>0?' %i minuto(s)':'') . ($fechaF->s>0?' %S segundo(s)':''));
        /*if($fecha_ahora->diffInYears($fecha_finaliza_todo) > 0){
            $texto .= $fecha_ahora->diffInYears($fecha_finaliza_todo).($fecha_ahora->diffInYears($fecha_finaliza_todo)>1?' años ':' año ');
            $semanas = 53*$fecha_ahora->diffInYears($fecha_finaliza_todo);
        }

        if($fecha_ahora->diffInWeeks($fecha_finaliza_todo) - $semanas > 0){
            $texto .= ($fecha_ahora->diffInWeeks($fecha_finaliza_todo) - $semanas).($fecha_ahora->diffInWeeks($fecha_finaliza_todo)>1?' semanas ':' semana ');
            $dias = ($fecha_ahora->diffInWeeks($fecha_finaliza_todo) - $semanas)*7;
        }

        if($fecha_ahora->diffInDays($fecha_finaliza_todo) - $dias > 0){
            $texto .= ($fecha_ahora->diffInDays($fecha_finaliza_todo) - $dias).($fecha_ahora->diffInDays($fecha_finaliza_todo)>1?' días ':' día ');
            $horas = ($fecha_ahora->diffInDays($fecha_finaliza_todo) - $dias)*24;
        }

        if($fecha_ahora->diffInHours($fecha_finaliza_todo) - $horas > 0){
            $texto .= ($fecha_ahora->diffInHours($fecha_finaliza_todo) - $horas).($fecha_ahora->diffInHours($fecha_finaliza_todo)>1?' horas ':' hora ');
            $minutos = ($fecha_ahora->diffInHours($fecha_finaliza_todo) - $horas)*60;
        }

        if($fecha_ahora->diffInMinutes($fecha_finaliza_todo) - $minutos > 0){
            $texto .= ($fecha_ahora->diffInMinutes($fecha_finaliza_todo) - $minutos).($fecha_ahora->diffInMinutes($fecha_finaliza_todo)>1?' minutos ':' minuto ');
        }*/

        if ($diferencia_horas >= 24) {
            $color = 'bg-aqua';
        }

        if ($diferencia_horas < 24 && $diferencia_horas >= 12) {
            $color = 'bg-blue';
        }

        if ($diferencia_horas < 12 && $diferencia_horas >= 8) {
            $color = 'bg-green';
        }

        if ($diferencia_horas < 8 && $diferencia_horas >= 2) {
            $color = 'bg-yellow';
        }

        if ($diferencia_horas < 2) {
            $color = 'bg-red';
        }

        $array = [$texto,$color];
        return($array);
    }

}
