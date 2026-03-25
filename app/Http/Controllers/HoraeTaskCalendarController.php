<?php

namespace App\Http\Controllers;

use App\HolidayDay;
use Illuminate\Http\Request;
use App\Task;
use Carbon\Carbon;
use Auth;
use App\User;
use App\Project;
use App\PartyDay;

class HoraeTaskCalendarController extends Controller
{
    public function index($usuario = null)
    {
        if(\Auth::user()->compruebaSeguridad('mostrar-calendario') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        if ($usuario == null) {
            $tasks = Task::with(['project.customer'])
                ->when(\Auth::user()->isRole('trabajador'), function($query) {
                    $query->whereHas('users', function($q) {
                        $q->where('user_id', Auth::user()->id);
                    });
                })
                ->where('estado_tarea', '!=', 4)
                ->get();
            $holiday_days = HolidayDay::all();
        } else {
            $yo = User::where('id',$usuario)->first();
            $tasks = $yo->tasks()->with(['project.customer'])
                ->where('estado_tarea', '!=', 4)
                ->get();
            $holiday_days = HolidayDay::where('user_id',$usuario)->get();
        }

        $users = User::where('baja', 0)->get();


        $party_days = PartyDay::all();

        return view('eunomia.tasks.calendar_tasks', compact('tasks','users','usuario','holiday_days','party_days'));
    }

    public function update(Request $request){
        if(\Auth::user()->compruebaSeguridad('editar-calendario') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $solomios = $request->solomios;
        $id = $request->eventid;
        $task = Task::findOrFail($id);
        $mitarea = 0;
        foreach($task->users as $taski){
            if ($taski->id === Auth::user()->id){
                $mitarea = 1;
            }
        }
        if ($solomios==0 || ($solomios==1 && $mitarea==1)) {
            $fechainicio_tarea = $request->start;
            $fechaentrega_tarea = $request->end;

            if (is_object($task)) {
                $task->fechainicio_tarea = Carbon::createFromFormat('Y-m-d H:i:s', $fechainicio_tarea);
                $task->fechaentrega_tarea = Carbon::createFromFormat('Y-m-d H:i:s', $fechaentrega_tarea);
                $task->save();
            }
        } else {
            echo "error";
        }
    }
}
