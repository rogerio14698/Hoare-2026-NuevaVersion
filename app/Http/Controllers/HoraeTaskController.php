<?php

namespace App\Http\Controllers;

use App\Task;
use App\Project;
use App\TaskState;
use App\User;
use Carbon\Carbon;
use users;
use Auth;
use Redirect;
use Mail;
use App\Comment;
use App\Customer;
use DB;

use Illuminate\Http\Request;
use Illuminate\Support\Str as Str;

class HoraeTaskController extends Controller
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
        // Mostrar todas las tareas a todos los usuarios (solo se excluyen las con estado 4)
        $tasks = Task::with(['project.customer','users','taskstate','comments'])
            ->where('estado_tarea','!=',4)
            ->get();

        return view('eunomia.tasks.list_tasks', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        \Log::info('Task Create - Usuario: ' . \Auth::user()->id . ', Role: ' . \Auth::user()->role_id);
        \Log::info('Task Create - Seguridad crear-tarea: ' . (\Auth::user()->compruebaSeguridad('crear-tarea') ? 'true' : 'false'));
        
        if(\Auth::user()->compruebaSeguridad('crear-tarea') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $proyectoid = null;
        $projects = Project::where('role_id','=',Auth::user()->role_id)->where('estado_proyecto','!=',4)->orderBy('id', 'desc')->pluck( 'codigo_proyecto', 'id');
        $users = User::where('baja', 0)->orderBy('name', 'asc')->pluck( 'name', 'id');
        $customers = Customer::select(DB::raw("concat(codigo_cliente, '_',nombre_cliente) as cono_cliente"),'id')->orderBy('codigo_cliente', 'asc')->pluck( 'cono_cliente', 'id');
        $task_states = TaskState::all()->pluck('state','id');
        return view('eunomia.tasks.form_ins_tasks', compact('projects', 'users', 'proyectoid','customers','task_states'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function create_WhithProject($project)
    {
        if(\Auth::user()->compruebaSeguridad('crear-tarea') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $proyecto = Project::findOrFail($project);
        $customer = $proyecto->customer;
        $projects = Project::where('role_id','=',Auth::user()->role_id)->orderBy('id', 'desc')->where('estado_proyecto','!=',4)->pluck( 'codigo_proyecto', 'id');
        $users = User::where('baja', 0)->orderBy('name', 'asc')->pluck( 'name', 'id');
        $customers = Customer::select(DB::raw("concat(codigo_cliente, '_',nombre_cliente) as cono_cliente"),'id')->orderBy('codigo_cliente', 'asc')->pluck( 'cono_cliente', 'id');
        $task_states = TaskState::all()->pluck('state','id');
        return view('eunomia.tasks.form_ins_tasks', compact('projects', 'users', 'proyecto','customers','customer','task_states'));
    }

     /**
      * Store a newly created resource in storage.
      *
      * @param  \Illuminate\Http\Request  $request
      * @return \Illuminate\Http\Response
      */
    public function store(Request $request)
    {
        \Log::info('Task Store - Usuario: ' . \Auth::user()->id . ', Role: ' . \Auth::user()->role_id);
        \Log::info('Task Store - Seguridad crear-tarea: ' . (\Auth::user()->compruebaSeguridad('crear-tarea') ? 'true' : 'false'));
        
        if(\Auth::user()->compruebaSeguridad('crear-tarea') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        
        // DEBUG temporal - ver qué datos llegan
        \Log::info('Task Store - Datos recibidos:', $request->all());
        
        //dd($request);

        // validaciones
        $this->validate($request, [
            'titulo_tarea' => 'required|string|max:255',
            'fechaentrega_tarea' => 'required|date',
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|array|min:1',
        ], [
            'titulo_tarea.required' => 'El título de la tarea es obligatorio.',
            'fechaentrega_tarea.required' => 'La fecha de entrega es obligatoria.',
            'fechaentrega_tarea.date' => 'La fecha de entrega debe ser una fecha válida.',
            'project_id.required' => 'Debe seleccionar un proyecto.',
            'project_id.exists' => 'El proyecto seleccionado no existe.',
            'user_id.required' => 'Debe asignar al menos un responsable.',
        ]);
        
        // Debug temporal - quitar después
        // dd($request->all());
        
        $previousurl = $request->previous;
        $task = new task;

        $task->project_id = $request->project_id;
        $task->titulo_tarea = $request->titulo_tarea;
        $task->fechaentrega_tarea = ($request->fechaentrega_tarea . ' ' . $request->horaentrega_tarea . ':00');
        $task->fechainicio_tarea = ($request->fechainicio_tarea . ' ' . $request->horanicio_tarea . ':00');
        $task->comentario_tarea = $request->comentario_tarea;
        $task->estado_tarea = $request->estado_tarea;

        $CodigoProyectoSeleccionado = Project::find($request->project_id);
        
        if (!$CodigoProyectoSeleccionado) {
            return back()->withErrors(['project_id' => 'El proyecto seleccionado no existe.'])->withInput();
        }

        $task->role_id = $request->role_id;

        $task->slug = Str::slug($CodigoProyectoSeleccionado->codigo_proyecto . '_' .$request->titulo_tarea);


        $task->save();

        $task->users()->attach($request->input('user_id'));

        //Enviar email al/los usuario/s avisando que se ha creado una nueva tarea.
        setlocale(LC_TIME, 'Spanish');
        foreach ($request->user_id as $user) {
          if ($user != Auth::user()->id) {

              $usuario = User::findOrFail($user);
              $email = $usuario->email;
              $fecinicio_tarea = Carbon::createFromFormat('Y-m-d H:i:s', $task->fechainicio_tarea);
              $fechainicio_tarea = $fecinicio_tarea->format('d/m/Y H:i:s');
              if (Mail::send('eunomia.includes.emails.email_tarea_nueva', [
                      'codigo_proyecto' => $CodigoProyectoSeleccionado->codigo_proyecto,
                      'titulo_proyecto' => $CodigoProyectoSeleccionado->titulo_proyecto,
                      'titulo_tarea' => $task->titulo_tarea,
                      'fechainicio_tarea' => $fechainicio_tarea,
                      'fechaentrega_tarea' => $task->fechaentrega_tarea,
                      'estado_tarea' => $task->taskstate->state,
                      'comentarios_tarea' => $task->comentario_tarea], function ($msj) use ($email) {
                      $msj->subject('Nueva tarea en Horae');
                      $msj->to($email);
                      $msj->bcc('sistemas@mglab.es');
                  }) == true) {

              }
          }
        }

        return redirect($previousurl);
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
        $projects = Project::where('role_id','=',Auth::user()->role_id)->orderBy('id', 'desc')->where('estado_proyecto','!=',4)->pluck( 'codigo_proyecto', 'id');
        $users = User::where('baja', 0)->orderBy('name', 'asc')->pluck( 'name', 'id');
        $myusers = $task->users->pluck('id')->ToArray();
        $task_states = TaskState::all()->pluck('state','id');
        $fechatareaoriginalinicio = $task->fechainicio_tarea->toDateString();
        $fechatareaoriginalentrega = $task->fechaentrega_tarea->toDateString();
        $horatareaoriginalinicio = $task->fechainicio_tarea->toTimeString();
        $horatareaoriginalentrega = $task->fechaentrega_tarea->toTimeString();

        $comments = $task->comments()->orderBy('date','DESC')->get();

        //$project_task = $task->project;

        return view('eunomia.tasks.form_edit_tasks', compact('projects', 'users', 'myusers', 'fechatareaoriginalinicio', 'fechatareaoriginalentrega', 'horatareaoriginalinicio', 'horatareaoriginalentrega','comments','task_states'))->withTask($task);
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
        $previousurl = $request->previous;

        $task->project_id = $request->project_id;
        $task->titulo_tarea = $request->titulo_tarea;
        $task->fechaentrega_tarea = ($request->fechaentrega_tarea . ' ' . $request->horaentrega_tarea . ':00');
        $task->fechainicio_tarea = ($request->fechainicio_tarea . ' ' . $request->horanicio_tarea . ':00');
        $task->estado_tarea = $request->estado_tarea;
        $task->comentario_tarea = $request->comentario_tarea;

        $CodigoProyectoSeleccionado = Project::find($request->project_id);
        
        if (!$CodigoProyectoSeleccionado) {
            return back()->withErrors(['project_id' => 'El proyecto seleccionado no existe.'])->withInput();
        }

        $task->slug = Str::slug($CodigoProyectoSeleccionado->codigo_proyecto . '_' .$request->titulo_tarea);
        $task->save();

        $task->users()->sync($request->input('user_id'));

        //Enviar email al/los usuario/s avisando que se ha creado una nueva tarea.
        setlocale(LC_TIME, 'Spanish');
        foreach ($request->user_id as $user) {
            if ($user != Auth::user()->id) {
                $usuario = User::findOrFail($user);
                $email = $usuario->email;
                $fecinicio_tarea = Carbon::createFromFormat('Y-m-d H:i:s', $task->fechainicio_tarea);
                $fechainicio_tarea = $fecinicio_tarea->format('d/m/Y H:i:s');
                if (Mail::send('eunomia.includes.emails.email_tarea_actualizacion', [
                        'codigo_proyecto' => $CodigoProyectoSeleccionado->codigo_proyecto,
                        'titulo_proyecto' => $CodigoProyectoSeleccionado->titulo_proyecto,
                        'titulo_tarea' => $task->titulo_tarea,
                        'fechainicio_tarea' => $fechainicio_tarea,
                        'fechaentrega_tarea' => $task->fechaentrega_tarea,
                        'estado_tarea' => $task->taskstate->state,
                        'comentarios_tarea' => $task->comentario_tarea
                    ], function ($msj) use ($email) {
                        $msj->subject('Actualización tarea en Horae');
                        $msj->to($email);
                        $msj->bcc('sistemas@mglab.es');
                    }) == true) {

                }
            }
        }

        return redirect($previousurl);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Task $task)
    {
        if(\Auth::user()->compruebaSeguridad('eliminar-tarea') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $previousurl = $request->previous;
        $task->delete();
        return redirect('eunomia/tasks');
    }


    /**
     * Añade los proyectos a un select.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response json
     */
    public function addProjects(Request $request,$customer_id=null){
        if ($request->ajax()){
            $projects = Project::where('estado_proyecto','!=',3)
                ->where('estado_proyecto','!=',4)
                ->when($customer_id > 0, function($query) use($customer_id) {
                    return $query->where('customer_id',$customer_id);
                })->orderBy('codigo_proyecto')->get();

        }
        return response()->json($projects);
    }
}
