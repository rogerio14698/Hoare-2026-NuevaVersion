<?php

namespace App\Http\Controllers;

use App\TodoTask;
use Illuminate\Support\Str as Str;
use Carbon\Carbon;


use Illuminate\Http\Request;

class HoraeTodoTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (\Auth::user()->compruebaSeguridad('crear-to-do-list') == false) {
            return view('eunomia.mensajes.mensaje_error')->with('msj', '..no tiene permisos para acceder a esta sección');
        }

        // validaciones
        $this->validate($request, [
            'titulo_tarea' => 'required',
        ]);

        $todotask = new TodoTask();

        $todotask->titulo_tarea = $request->titulo_tarea;
        if ($request->fechaentrega_tarea != '') {
            try {
                // Log para debugging
                \Log::info('TodoTask store - Fecha recibida: ' . $request->fechaentrega_tarea);
                \Log::info('TodoTask store - Hora recibida: ' . $request->horaentrega_tarea);
                
                // Parsear la fecha que viene en formato dd/mm/yyyy
                $fecha = Carbon::createFromFormat('d/m/Y', $request->fechaentrega_tarea);
                
                if ($request->horaentrega_tarea != '') {
                    // Si hay hora, parsearla y añadirla
                    $hora_parts = explode(':', $request->horaentrega_tarea);
                    $fecha->setTime($hora_parts[0], $hora_parts[1]);
                }
                
                $todotask->fechaentrega_tarea = $fecha->format('Y-m-d H:i:s');
                \Log::info('TodoTask store - Fecha procesada: ' . $todotask->fechaentrega_tarea);
            } catch (\Exception $e) {
                // Si hay error en el parseo, intentar formato alternativo o usar fecha actual
                \Log::error('Error parseando fecha en TodoTask: ' . $e->getMessage());
                $todotask->fechaentrega_tarea = Carbon::now()->format('Y-m-d H:i:s');
            }
        }
        $todotask->comentario_tarea = $request->comentario_tarea;

        $todotask->role_id = $request->role_id;

        $todotask->slug = Str::slug($request->fechaentrega_tarea . '_' . $request->titulo_tarea);

        $todotask->orden     = TodoTask::max('orden') + 1;

        $todotask->save();

        $todotask->users()->attach(\Auth::user()->id);

        return redirect('/eunomia');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TodoTask  $todoTask
     * @return \Illuminate\Http\Response
     */
    public function show(TodoTask $todoTask)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TodoTask  $todoTask
     * @return \Illuminate\Http\Response
     */


    public function edit(Request $request)
    {
        \Log::info('TodoTask Edit - Request recibido:', $request->all());
        
        if (!\Auth::user()->compruebaSeguridad('editar-to-do-list')) {
            return response()->json([
                'ok' => false,
                'message' => '..no tiene permisos para acceder a esta sección'
            ], 403);
        }

        $task = TodoTask::find($request->id);
        if (!$task) {
            return response()->json(['ok' => false, 'message' => 'Tarea no encontrada'], 404);
        }

        $fecha = null;
        $hora  = null;
        if (!empty($task->fechaentrega_tarea)) {
            try {
                $c = Carbon::parse($task->fechaentrega_tarea);
                // Formatos “seguros” para tus inputs
                $fecha = $c->format('Y-m-d');  // o 'd/m/Y' si tu datepicker lo espera así
                $hora  = $c->format('H:i');
            } catch (\Throwable $e) {
                // deja $fecha/$hora en null si falla el parse
            }
        }

        return response()->json([
            'ok'                 => true,
            'id'                 => (int) $task->id,
            'titulo_tarea'       => $task->titulo_tarea,
            'fecha'              => $fecha,  // <-- separado
            'hora'               => $hora,   // <-- separado
            'fechaentrega_tarea' => (string) $task->fechaentrega_tarea, // crudo por si lo necesitas
            'comentario_tarea'   => $task->comentario_tarea,
        ]);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TodoTask  $todoTask
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if (!\Auth::user()->compruebaSeguridad('editar-to-do-list')) {
            return response()->json(['ok' => false, 'message' => '..no tiene permisos para acceder a esta sección'], 403);
        }

        // valida que venga el id y que exista
        $request->validate([
            'id'           => 'required|integer|exists:todotasks,id',
            'titulo_tarea' => 'required|string'
        ]);

        $todoTask = TodoTask::findOrFail($request->id);

        $todoTask->titulo_tarea     = $request->titulo_tarea;
        $todoTask->comentario_tarea = $request->comentario_tarea;
        $todoTask->role_id          = $request->role_id;

        // fecha + hora → un solo campo en DB (o null si no hay fecha)
        $fecha = $request->input('fechaentrega_tarea');
        $hora  = $request->input('horaentrega_tarea');
        
        if ($fecha) {
            try {
                // Parsear la fecha que viene en formato dd/mm/yyyy
                $fechaParsed = Carbon::createFromFormat('d/m/Y', $fecha);
                
                if ($hora) {
                    // Si hay hora, parsearla y añadirla
                    $hora_parts = explode(':', $hora);
                    $fechaParsed->setTime($hora_parts[0], isset($hora_parts[1]) ? $hora_parts[1] : 0);
                } else {
                    // Si no hay hora, poner 00:00
                    $fechaParsed->setTime(0, 0);
                }
                
                $todoTask->fechaentrega_tarea = $fechaParsed->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                // Si hay error en el parseo, usar la fecha actual
                \Log::error('Error parseando fecha en TodoTask update: ' . $e->getMessage());
                $todoTask->fechaentrega_tarea = Carbon::now()->format('Y-m-d H:i:s');
            }
        } else {
            $todoTask->fechaentrega_tarea = null;
        }

        // slug (evita concatenar nulls)
        $todoTask->slug = \Str::slug(trim(($fecha ?? '') . ' ' . ($hora ?? '') . ' ' . $request->titulo_tarea));

        $todoTask->save();

        // Recalcula el badge como hacías en la vista
        [$badgeText, $badgeClass] = $todoTask->devuelveTiempoRestante();

        return response()->json([
            'ok'   => true,
            'data' => [
                'id'           => $todoTask->id,
                'titulo_tarea' => $todoTask->titulo_tarea,
                'badge_text'   => $badgeText,
                'badge_class'  => $badgeClass,
            ],
        ]);
    }




    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TodoTask  $todoTask
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (\Auth::user()->compruebaSeguridad('eliminar-to-do-list') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj', '..no tiene permisos para acceder a esta sección');
        $id = $request->delete_id;
        $todoTask = TodoTask::findOrFail($id);
        $todoTask->delete();

        return redirect('/eunomia');
    }

    // AJAX Reordering function
    public function postIndex(Request $request)
    {
        $source = $request->source;
        $destination = $request->destination;

        //$ordering       = json_decode(Input::get('order'));
        //$rootOrdering   = json_decode(Input::get('rootOrder'));

        $ordering       = json_decode($request->order);
        $rootOrdering   = json_decode($request->rootOrder);

        if ($ordering) {
            foreach ($ordering as $order => $item_id) {
                if ($itemToOrder = TodoTask::findOrFail($item_id)) {
                    $itemToOrder->orden = $order;
                    $itemToOrder->save();
                }
            }
        } else {
            foreach ($rootOrdering as $order => $item_id) {
                if ($itemToOrder = TodoTask::findOrFail($item_id)) {
                    $itemToOrder->orden = $order;
                    $itemToOrder->save();
                }
            }
        }

        return 'ok ';
    }
}
