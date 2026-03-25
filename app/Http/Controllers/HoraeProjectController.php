<?php

namespace App\Http\Controllers;

use App\Project;
use App\Customer;
use App\User;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Proengsoft\JsValidation\Facades\JsValidatorFacade as JsValidator;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Exception;

class HoraeProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */

// Listado de proyectos con control de permisos y carga de relaciones

public function index()
    {
     if(\Auth::user()->compruebaSeguridad('mostrar-proyectos') == false)
          return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $customers = Customer::all();
        $projects = Project::with(['user', 'projectstate', 'tasks', 'comments'])
        ->when(\Auth::user()->isRole('cliente'), function($query){
             //Clientes solo ven proyectos de su empresa
            $query->where('customer_id',Auth::user()->customer_id);
        })
         //Trabajadores y Admin ven todos los proyectos
        ->where('estado_proyecto','!=',4)->get();
        return view('eunomia.projects.list_projects', compact('projects', 'customers'));

    
       } 
    

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
    public function create()
    {
        if(\Auth::user()->compruebaSeguridad('crear-proyecto') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');

        $customers = Customer::select(DB::raw("concat(codigo_cliente, '_',nombre_cliente) as cono_cliente"),'id')->orderBy('codigo_cliente', 'asc')->pluck( 'cono_cliente', 'id');
        $users = User::where('baja', 0)->orderBy('name', 'asc')->pluck( 'name', 'id');
        $action = null;
        $rules = [
            'user_id' => 'required',
            'titulo_proyecto' => 'required',
            'fechaentrega_proyecto' => 'required'
        ];

        $messages = [
            'user_id.required' => 'El responsable del proyecto es obligatorio',
            'titulo_proyecto.required' => 'El título del proyecto es obligatorio',
            'fechaentrega_proyecto.required' => 'La fecha de entrega es obligatoria'
        ];
        
        $validator = app('jsvalidator')->make($rules, $messages);
        return view('eunomia.projects.form_ins_projects', compact('customers', 'users','action','validator'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function store(Request $request)
    {
        // Debug EXTREMO - registrar TODO
        file_put_contents(storage_path('logs/project_debug.log'), 
            "=== DEBUG PROJECT STORE ===\n" .
            "Time: " . now() . "\n" .
            "Method: " . $request->method() . "\n" .
            "URL: " . $request->url() . "\n" .
            "Headers: " . json_encode($request->headers->all()) . "\n" .
            "All Data: " . json_encode($request->all()) . "\n" .
            "Action: " . $request->action . "\n" .
            "Has _ajax: " . ($request->has('_ajax') ? 'YES' : 'NO') . "\n" .
            "Raw Input: " . $request->getContent() . "\n" .
            "==========================\n\n", 
            FILE_APPEND
        );
        
        // Debug detallado al inicio
        error_log('=== PROJECT STORE INICIADO ===');
        error_log('Method: ' . $request->method());
        error_log('URL: ' . $request->url());
        error_log('Action: ' . $request->action);
        error_log('Has _ajax: ' . ($request->has('_ajax') ? 'YES' : 'NO'));
        error_log('All input: ' . json_encode($request->all()));
        
        if(\Auth::user()->compruebaSeguridad('crear-proyecto') == false) {
            error_log('PROJECT STORE - No permissions');
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        }

        if ($request->action != 1){
            error_log('PROJECT STORE - Validating form data');
            //Validación de los datos del formulario
            $this->validate($request, [
                'user_id' => 'required',
                'titulo_proyecto' => 'required',
                'fechaentrega_proyecto' => 'required'
            ],[
                'user_id.required' => 'El responsable del proyecto es obligatorio',
                'titulo_proyecto.required' => 'El título del proyecto es obligatorio',
                'fechaentrega_proyecto.required' => 'La fecha de entrega es obligatoria'
            ]);
            error_log('PROJECT STORE - Validation passed');
        }

        $project = new project;

        $project->customer_id = $request->customer_id;
        $project->user_id = $request->user_id;
        $project->titulo_proyecto = $request->titulo_proyecto;

        $CodigoClienteSeleccionado = Customer::find($request->customer_id);
        
        if (!$CodigoClienteSeleccionado) {
            return back()->withErrors(['customer_id' => 'El cliente seleccionado no existe.'])->withInput();
        }

        $project->codigo_proyecto = $CodigoClienteSeleccionado->codigo_cliente . '_' .$request->titulo_proyecto;
        $project->estado_proyecto = $request->estado_proyecto;
        $project->fechaentrega_proyecto = Carbon::parse($request->fechaentrega_proyecto);
        $project->comentario_proyecto = $request->comentario_proyecto;
        $project->role_id = $request->role_id;

        $project->slug = Str::slug($CodigoClienteSeleccionado->codigo_cliente . '_' .$request->titulo_proyecto);
        
        error_log('PROJECT STORE - About to save project');
        $project->save();
        error_log('PROJECT STORE - Project saved with ID: ' . $project->id);

        // Comentado temporalmente el envío de email para debug
        /*
        if ($project->user_id != Auth::user()->id) {
            //Enviar email al responsable avisando que se ha creado un nuevo proyecto.
            $usuario = User::findOrFail($project->user_id);
            $email = $usuario->email;
            setlocale(LC_TIME, 'Spanish');
            $fechaentrega_proyecto = $project->fechaentrega_proyecto->format('d/m/Y');
            if (Mail::send('eunomia.includes.emails.email_proyecto_nuevo', [
                'codigo_proyecto' => $project->codigo_proyecto,
                'titulo_proyecto' => $project->titulo_proyecto,
                'nombre_cliente' => $CodigoClienteSeleccionado->nombre_cliente,
                'fechaentrega_proyecto' => $fechaentrega_proyecto,
                'estado_proyecto' => $project->projectstate->state,
                'comentarios_proyecto' => $project->comentario_proyecto
                ], function ($msj) use ($email) {
                    $msj->subject('Nuevo proyecto en Horae');
                    $msj->to($email);
                    $msj->bcc('sistemas@mglab.es');
                }) == true) {
                //Algo
            }
        }
        */

        // Solo devolver JSON si realmente es una petición AJAX o explícitamente marcada
        if ($request->ajax() || $request->has('_ajax')) {
            error_log('PROJECT STORE - Returning JSON response for AJAX');
            return response()->json(['success' => true, 'id' => $project->id, 'codigo_proyecto' => $project->codigo_proyecto]);
        }

        error_log('PROJECT STORE - Returning redirect to projects list');
        return redirect('eunomia/projects')->with('success', 'Proyecto creado correctamente');
    }

  /**
   * Display the specified resource.
   *
   * @param  \App\Customer  $customer
   * @return \Illuminate\Http\Response
   */
    public function show(Request $request, Project $project)
    {
        if(\Auth::user()->compruebaSeguridad('mostrar-proyectos') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $customers = Customer::orderBy('nombre_cliente')->pluck( 'nombre_cliente', 'id');
        $users = User::pluck( 'name', 'id');
    
        $fechadehoy = Carbon::yesterday('Europe/Madrid');

        $cuentatareas =  $project->tasks()->count();

        $comments = $project->comments()->orderBy('date','DESC')->get();

        if (!\Auth::user()->isRole('cliente') || (\Auth::user()->isRole('cliente') && $project->customer_id == (is_object(\Auth::user()->customer)?\Auth::user()->customer->id:0)))
            return view('eunomia.projects.show_projects', compact('customers', 'users', 'fechadehoy', 'cuentatareas','comments'))->withProject($project);
        else
            return redirect('/eunomia');
    }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Customer  $customer
   * @return \Illuminate\Http\Response
   */
    public function edit(Project $project)
    {
        if(\Auth::user()->compruebaSeguridad('editar-proyecto') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        
        // Cargar las relaciones necesarias
        $project->load(['user.departamento', 'customer']);
        
        $customers = Customer::select(DB::raw("concat(codigo_cliente, '_',nombre_cliente) as cono_cliente"),'id')->orderBy('codigo_cliente', 'asc')->pluck( 'cono_cliente', 'id');
        $users = User::where('baja', 0)->orderBy('name', 'asc')->pluck( 'name', 'id');

        $fechadehoy = Carbon::yesterday('Europe/Madrid');

        $cuentatareas =  $project->tasks()->count();

        $comments = $project->comments()->orderBy('date','DESC')->get();
        Carbon::setLocale('es');

        return view('eunomia.projects.form_edit_projects', compact('customers', 'users','comments','cuentatareas','fechadehoy'))->withProject($project);
    }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Customer  $customer
   * @return \Illuminate\Http\Response
   */
    public function update(Request $request, Project $project)
    {
        if(\Auth::user()->compruebaSeguridad('editar-proyecto') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $project->customer_id = $request->customer_id;
        $project->user_id = $request->user_id;
        $project->titulo_proyecto = $request->titulo_proyecto;

        $CodigoClienteSeleccionado = Customer::where('id', $request->customer_id)->first();

        $project->codigo_proyecto = $CodigoClienteSeleccionado->codigo_cliente . '_' .$request->titulo_proyecto;
        $project->estado_proyecto = $request->estado_proyecto;
        $project->fechaentrega_proyecto = $request->fechaentrega_proyecto;
        $project->comentario_proyecto = $request->comentario_proyecto;
        $project->slug = Str::slug($CodigoClienteSeleccionado->codigo_cliente . '_' .$request->titulo_proyecto);
        if (isset($request->solicitado_nfs)){
            if ($request->n_factura != ''){
                $project->solicitado_nfs = $request->n_factura;
            } else {
                $project->solicitado_nfs = 'Solicitado';
            }
        }


        if($request->hasFile('web_preview')){
            ini_set('memory_limit', '512M');
            $dir = '/var/www/vhosts/mglab.es/httpdocs/images/clientes/previsualiza/' . $project->customer->slug . '/';
            if (!File::exists($dir)){
                File::makeDirectory($dir);
            }
            $dir = '/var/www/vhosts/mglab.es/httpdocs/images/clientes/previsualiza/' . $project->customer->slug . '/' . $project->slug . '/';
            if (!File::exists($dir)){
                File::makeDirectory($dir);
            }

            $imagenactual = $project->web_preview;
            File::delete($dir . $imagenactual);
            $imagen = $request->file('web_preview');
            $filename = $project->customer->codigo_cliente . '_' . time() . '.' . $imagen->getClientOriginalExtension();
            Image::make($imagen)->resize(1920, null, function ($constraint) {
                $constraint->upsize();
            })->save($dir . $filename );
            $project->web_preview=$filename;
        }

        $project->save();

        if ($project->user_id != Auth::user()->id) {
            //Enviar email al responsable avisando que se ha editado un nuevo proyecto.
            $usuario = User::findOrFail($project->user_id);
            $email = $usuario->email;
            setlocale(LC_TIME, 'Spanish');
            $fecentrega_proyecto = Carbon::createFromFormat('Y-m-d', $project->fechaentrega_proyecto);
            $fechaentrega_proyecto = $fecentrega_proyecto->format('d/m/Y');
            if (Mail::send('eunomia.includes.emails.email_proyecto_actualizacion', [
                    'codigo_proyecto' => $project->codigo_proyecto,
                    'titulo_proyecto' => $project->titulo_proyecto,
                    'nombre_cliente' => $CodigoClienteSeleccionado->nombre_cliente,
                    'fechaentrega_proyecto' => $fechaentrega_proyecto,
                    'estado_proyecto' => $project->projectstate->state,
                    'comentarios_proyecto' => $project->comentario_proyecto
                ], function ($msj) use ($email) {
                    $msj->subject('Actualización proyecto en Horae');
                    $msj->to($email);
                    $msj->bcc('sistemas@mglab.es');
                }) == true) {
                //Algo
            }

            //Enviar correo a Jorge cuando el estado del proyecto sea facturar.
        }
        return redirect('eunomia/projects');

    }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Customer  $customer
   * @return \Illuminate\Http\Response
   */
    public function destroy(Project $project)
    {
        if(\Auth::user()->compruebaSeguridad('eliminar-proyecto') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $project->delete();
        $project->tasks()->delete();
        return redirect('eunomia/projects');
    }

    public function muestraFormularioProyecto($customer_id){
        $users = User::where('baja', 0)->orderBy('name', 'asc')->pluck( 'name', 'id');
        $customer = Customer::find($customer_id);
        if (!$customer) {
            return response()->view('errors.customer_not_found', ['customer_id' => $customer_id], 404);
        }
        $action = 1;
        $rules = [
            'user_id' => 'required',
            'titulo_proyecto' => 'required',
            'fechaentrega_proyecto' => 'required'
        ];

        $messages = [
            'user_id.required' => 'El responsable del proyecto es obligatorio',
            'titulo_proyecto.required' => 'El título del proyecto es obligatorio',
            'fechaentrega_proyecto.required' => 'La fecha de entrega es obligatoria'
        ];
        $validator = JsValidator::make($rules, $messages, [], '#formulario_proyectos');
        return view('eunomia.projects.formulario_proyectos', compact('action','validator','users','customer'));
    }

    public function muestraTareasProyecto($project_id){
        $tasks = Task::where('project_id',$project_id)
            ->with(['project.customer', 'users', 'taskstate'])
            ->orderBy('fechaentrega_tarea','DESC')->get();

        return view('eunomia.tasks.listado_tareas_proyecto',compact('tasks'));
    }

}
