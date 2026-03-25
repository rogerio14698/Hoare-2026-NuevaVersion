<?php

namespace App\Http\Controllers;

use App\Task;
use App\Project;
use App\User;
use Carbon\Carbon;
use users;
use Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Str as Str;

class HoraeTaskHistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->compruebaSeguridad('mostrar-tareas') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');

        $tasks = Task::with(['project','users','comments','taskstate'])
            ->when(\Auth::user()->isRole('cliente'), function($query) {
                $query->join('projects','project_id','projects.id');
            })
            ->when(\Auth::user()->isRole('trabajador'), function($query) {
                $query->where('role_id', '=', Auth::user()->role_id);
            })
            ->when(\Auth::user()->isRole('cliente'), function($query){
                $query->where('customer_id',Auth::user()->customer_id);
            })
            ->where('estado_tarea',4)
            ->paginate(50);

        return view('eunomia.tasks.list_taskshist', compact('tasks'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::user()->compruebaSeguridad('crear-tarea') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $projects = Project::pluck( 'codigo_proyecto', 'id');
        $users = User::pluck( 'name', 'id');

        return view('eunomia.tasks.form_ins_tasks', compact('projects', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(\Auth::user()->compruebaSeguridad('crear-tarea') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $task = new task;

        $task->project_id = $request->project_id;
        $task->titulo_tarea = $request->titulo_tarea;
        $task->fechaentrega_tarea = $request->fechaentrega_tarea;
        $task->estado_tarea = $request->estado_tarea;
        $task->comentario_tarea = $request->comentario_tarea;

        $CodigoProyectoSeleccionado = Project::where('id', $request->project_id)->first();

        $task->slug = Str::slug($CodigoProyectoSeleccionado->codigo_proyecto . '_' .$request->titulo_tarea);
        $task->save();

        $task->users()->attach($request->input('user_id'));

        return redirect('eunomia/tasks');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        if(\Auth::user()->compruebaSeguridad('editar-tarea') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $projects = Project::pluck( 'codigo_proyecto', 'id');
        $users = User::pluck( 'name', 'id');
        $myusers = $task->users->pluck('id')->ToArray();
        $fechatareaoriginal = $task->fechaentrega_tarea->toDateString();

        return view('eunomia.tasks.form_edit_tasks', compact('projects', 'users', 'myusers', 'fechatareaoriginal'))->withTask($task);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        if(\Auth::user()->compruebaSeguridad('editar-tarea') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');

        $task->project_id = $request->project_id;
        $task->titulo_tarea = $request->titulo_tarea;
        $task->fechaentrega_tarea = $request->fechaentrega_tarea;
        $task->estado_tarea = $request->estado_tarea;
        $task->comentario_tarea = $request->comentario_tarea;

        $CodigoProyectoSeleccionado = Project::where('id', $request->project_id)->first();

        $task->slug = Str::slug($CodigoProyectoSeleccionado->codigo_proyecto . '_' .$request->titulo_tarea);
        $task->save();

        $task->users()->sync($request->input('user_id'));

        return redirect('eunomia/tasks');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        if(\Auth::user()->compruebaSeguridad('eliminar-tarea') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $task->delete();

        return redirect('eunomia/hist');
    }
}
