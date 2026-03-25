<?php

namespace App\Http\Controllers;

use App\Fichaje;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use App\User;


class FichajeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $puedeVerTodos = \Auth::user()->compruebaSeguridad('ver-todos-fichajes');

        // Si NO puede ver todos Y tampoco tiene mostrar-fichajes → sin acceso
        if (!$puedeVerTodos && !\Auth::user()->compruebaSeguridad('mostrar-fichajes'))
            return view('eunomia.mensajes.mensaje_error')->with('msj', '..no tiene permisos para acceder a esta sección');

        $filtro_estado = $request->get('estado', 'activos');

        if ($puedeVerTodos) {
            $query = User::query();

            switch($filtro_estado) {
                case 'todos':
                    break;
                case 'inactivos':
                    $query->where('baja', 1);
                    break;
                case 'activos':
                default:
                    $query->where('baja', 0);
                    break;
            }

            $users = $query->get();
        } else {
            // Solo ve su propio registro
            $users = User::where('id', \Auth::user()->id)->get();
        }

        return view('eunomia.fichajes.listado_fichajes', compact('users', 'filtro_estado', 'puedeVerTodos'));
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
        $targetUserId = (int) $request->input('user_id', \Auth::user()->id);

        // Si intenta fichar para otro usuario, requiere permiso explícito.
        if ($targetUserId !== (int) \Auth::user()->id && !\Auth::user()->compruebaSeguridad('ver-todos-fichajes')) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Sin permisos para crear fichajes de otro usuario'], 403);
            }
            return back()->withErrors(['error' => 'Sin permisos para crear fichajes de otro usuario']);
        }

        //comprobamos el último estado del fichaje
        $fichaje = Fichaje::where('user_id', $targetUserId)->orderBy('fecha', 'desc')->first();


        if ($request->filled('tipo')) {
            $tipo = $request->tipo;
        } elseif (is_object($fichaje)) {
            $tipo = $fichaje->tipo == 'entrada' ? 'salida' : 'entrada';
        } else {
            $tipo = 'entrada';
        }

        // VALIDACIÓN 1: No permitir dos tipos consecutivos iguales
        if (is_object($fichaje) && $fichaje->tipo == $tipo) {
            return response()->json([
                'error' => 'No se puede hacer ' . ($tipo == 'entrada' ? 'check in' : 'check out') . ' consecutivo. Último registro: ' . $fichaje->tipo
            ], 422);
        }

        if ($request->filled('fecha') && $request->filled('hora')) {
            $fecha = Carbon::parse($request->fecha . ' ' . $request->hora)->format('Y-m-d H:i:s');
        } elseif ($request->filled('hora')) {
            $fecha = Carbon::parse(date('Y-m-d') . ' ' . $request->hora)->format('Y-m-d H:i:s');
        } else {
            $fecha = date('Y-m-d H:i:s');
        }

        // VALIDACIÓN 2: No permitir fichajes a la misma hora exacta
        $fichaje_misma_hora = Fichaje::where('user_id', $targetUserId)
            ->where('fecha', $fecha)
            ->first();
            
        if ($fichaje_misma_hora) {
            return response()->json([
                'error' => 'Ya existe un fichaje (' . $fichaje_misma_hora->tipo . ') a las ' . \Carbon\Carbon::parse($fecha)->format('H:i') . ' del ' . \Carbon\Carbon::parse($fecha)->format('d/m/Y')
            ], 422);
        }

        $fichaje = new Fichaje;

        $fichaje->user_id = $targetUserId;
        $fichaje->fecha = $fecha;
        $fichaje->tipo = $tipo;
        $fichaje->comentarios = $request->comentarios;

        $fichaje->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'tipo' => $tipo]);
        }

        if ($request->filled('hora') && !$request->filled('fecha')) {
            return redirect('eunomia/home');
        }

        return back()->with('success', 'Fichaje guardado correctamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Fichaje  $fichaje
     * @return \Illuminate\Http\Response
     */
    public function show(Fichaje $fichaje)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Fichaje  $fichaje
     * @return \Illuminate\Http\Response
     */
    public function edit(Fichaje $fichaje)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Fichaje  $fichaje
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $fichaje = Fichaje::findOrFail($request->fichaje_id);
        
        if (is_object($fichaje)) {
            $fecha = $request->fecha . ' ' . $request->hora . ':00';
            
            // VALIDACIÓN 1: Verificar que no exista otro fichaje a la misma hora exacta
            $fichaje_misma_hora = Fichaje::where('user_id', $fichaje->user_id)
                ->where('id', '!=', $fichaje->id)  // Excluir el fichaje actual
                ->where('fecha', $fecha)
                ->first();
                
            if ($fichaje_misma_hora) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'error' => 'Ya existe un fichaje (' . $fichaje_misma_hora->tipo . ') a las ' . \Carbon\Carbon::parse($fecha)->format('H:i') . ' del ' . \Carbon\Carbon::parse($fecha)->format('d/m/Y')
                    ], 422);
                }
                return back()->withErrors(['error' => 'Ya existe un fichaje a esa hora']);
            }
            
            // VALIDACIÓN 2: Verificar secuencia lógica de fichajes
            // Obtener el fichaje anterior y posterior (excluyendo el actual)
            $fichaje_anterior = Fichaje::where('user_id', $fichaje->user_id)
                ->where('id', '!=', $fichaje->id)
                ->where('fecha', '<', $fecha)
                ->orderBy('fecha', 'desc')
                ->first();
                
            $fichaje_posterior = Fichaje::where('user_id', $fichaje->user_id)
                ->where('id', '!=', $fichaje->id)
                ->where('fecha', '>', $fecha)
                ->orderBy('fecha', 'asc')
                ->first();
            
            // Verificar que no se cree una secuencia ilógica con el anterior
            if ($fichaje_anterior && $fichaje_anterior->tipo == $fichaje->tipo) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'error' => 'No se puede tener dos ' . ($fichaje->tipo == 'entrada' ? 'check ins' : 'check outs') . ' consecutivos. Fichaje anterior: ' . $fichaje_anterior->tipo . ' a las ' . \Carbon\Carbon::parse($fichaje_anterior->fecha)->format('H:i')
                    ], 422);
                }
                return back()->withErrors(['error' => 'Secuencia de fichajes inválida']);
            }
            
            // Verificar que no se cree una secuencia ilógica con el posterior
            if ($fichaje_posterior && $fichaje_posterior->tipo == $fichaje->tipo) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'error' => 'No se puede tener dos ' . ($fichaje->tipo == 'entrada' ? 'check ins' : 'check outs') . ' consecutivos. Fichaje posterior: ' . $fichaje_posterior->tipo . ' a las ' . \Carbon\Carbon::parse($fichaje_posterior->fecha)->format('H:i')
                    ], 422);
                }
                return back()->withErrors(['error' => 'Secuencia de fichajes inválida']);
            }
            
            // Si pasa la validación, actualizar
            $fichaje->fecha = $fecha;
            $fichaje->comentarios = $request->comentarios;
            $fichaje->save();

            // Si es petición AJAX, devolver JSON
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Fichaje actualizado correctamente']);
            }
            
            return redirect('eunomia/home');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Fichaje  $fichaje
     * @return \Illuminate\Http\Response
     */
    public function destroy(Fichaje $fichaje)
    {
        $esPropio = $fichaje->user_id == \Auth::user()->id;
        if (!$esPropio && !\Auth::user()->compruebaSeguridad('ver-todos-fichajes'))
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para eliminar este fichaje');
        
        $fichaje->delete();
        
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Fichaje eliminado correctamente']);
        }
        
        return back()->with('success', 'Fichaje eliminado correctamente');
    }

    /**
     * Get fichajes for a specific user (AJAX)
     *
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function getFichajesUsuario($userId, Request $request = null)
    {
        if(\Auth::user()->compruebaSeguridad('mostrar-fichajes') == false)
            return response()->json(['error' => 'Sin permisos'], 403);

        // Sin permiso de ver todos, solo puede ver sus propios fichajes
        if(!\Auth::user()->compruebaSeguridad('ver-todos-fichajes') && \Auth::user()->id != $userId)
            return response()->json(['error' => 'Sin permisos para ver fichajes de otro usuario'], 403);
        
        // Parámetros de filtro - usar request() helper si $request es null
        $requestObj = $request ?: request();
        $periodo = $requestObj->get('periodo', 'mes_actual');
        $page = (int)$requestObj->get('page', 1);
        $perPage = 20; // Fichajes por página
        
        // Construir query base
        $query = Fichaje::where('user_id', $userId);
        
        // Aplicar filtro de período
        switch ($periodo) {
            case 'mes_actual':
                $query->whereMonth('fecha', \Carbon\Carbon::now()->month)
                      ->whereYear('fecha', \Carbon\Carbon::now()->year);
                break;
            case 'ultimo_mes':
                $query->where('fecha', '>=', \Carbon\Carbon::now()->subMonth());
                break;
            case 'ultimos_3_meses':
                $query->where('fecha', '>=', \Carbon\Carbon::now()->subMonths(3));
                break;
            case 'todos':
                // Sin filtro adicional
                break;
        }
        
        // Obtener total para paginación
        $total = $query->count();
        
        // Aplicar paginación
        $fichajes = $query->orderBy('fecha', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();
        
        $totalPages = ceil($total / $perPage);
        
        // Información sobre el período seleccionado
        $periodo_texto = [
            'mes_actual' => 'Mes actual (' . \Carbon\Carbon::now()->format('F Y') . ')',
            'ultimo_mes' => 'Últimos 30 días',
            'ultimos_3_meses' => 'Últimos 3 meses',
            'todos' => 'Todos los fichajes'
        ];
        
        $html = '<div class="fichajes-container" data-user-id="' . $userId . '">
                    <!-- Controles de filtro -->
                    <div class="row" style="margin-bottom: 10px;">
                        <div class="col-md-8">
                            <div class="btn-group btn-group-sm filtros-periodo">
                                <button type="button" class="btn ' . ($periodo == 'mes_actual' ? 'btn-primary' : 'btn-default') . '" data-periodo="mes_actual">Mes actual</button>
                                <button type="button" class="btn ' . ($periodo == 'ultimo_mes' ? 'btn-primary' : 'btn-default') . '" data-periodo="ultimo_mes">Último mes</button>
                                <button type="button" class="btn ' . ($periodo == 'ultimos_3_meses' ? 'btn-primary' : 'btn-default') . '" data-periodo="ultimos_3_meses">3 meses</button>
                                <button type="button" class="btn ' . ($periodo == 'todos' ? 'btn-primary' : 'btn-default') . '" data-periodo="todos">Todos</button>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <small class="text-muted">
                                <i class="fa fa-info-circle"></i> 
                                ' . $periodo_texto[$periodo] . ': ' . $total . ' fichaje(s)
                            </small>
                        </div>
                    </div>';
        
        if ($total == 0) {
            $html .= '<div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> 
                        No hay fichajes para el período seleccionado.
                      </div>
                    </div>';
            return $html;
        }
        
        $html .= '<table class="table table-striped table-condensed">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Tipo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        foreach ($fichajes as $fichaje) {
            $fecha = \Carbon\Carbon::parse($fichaje->fecha)->format('d/m/Y');
            $hora = \Carbon\Carbon::parse($fichaje->fecha)->format('H:i:s');
            $fecha_fichaje_campo = \Carbon\Carbon::parse($fichaje->fecha)->format('Y-m-d');
            $tipo = $fichaje->tipo == 'entrada' ? 'Entrada' : 'Salida';
            
            $html .= '<tr>
                        <td>' . $fecha . '</td>
                        <td>' . $hora . '</td>
                        <td>' . $tipo . '</td>
                        <td>';
            
            if(\Auth::user()->compruebaSeguridad('editar-fichaje')) {
                $html .= '<button type="button" class="btn btn-warning btn-xs btn-editar-fichaje" 
                            data-fichaje-id="' . $fichaje->id . '"
                            data-fecha="' . $fecha_fichaje_campo . '"
                            data-hora="' . \Carbon\Carbon::parse($fichaje->fecha)->format('H:i') . '"
                            data-tipo="' . $fichaje->tipo . '"
                            data-comentarios="' . htmlspecialchars($fichaje->comentarios) . '">
                            Editar-usuario?? ver 
                          </button> ';
                
                $html .= '<form method="POST" action="' . route('fichajes.destroy', $fichaje->id) . 
                '" style="display:inline;" class="form_eliminar_fichaje">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-xs">Eliminar</button>
                          </form>';
                $html.= '<form method="POST" action="' .
                 route('modificaHoraFichaje', ['fichaje_id' => $fichaje->id]) . 
                 '" style="display:inline;" class="form_modificar_hora_fichaje"> ' .
                  csrf_field() . ' <input type="hidden" name="fichaje_id" value="' .
                   $fichaje->id . '"> <input type="hidden" name="fecha" value="' .
                    $fecha_fichaje_campo . '"> <input type="hidden" name="hora" value="' . 
                    \Carbon\Carbon::parse($fichaje->fecha)->format('H:i') .
                     '"> 
                     <button type="submit" class="btn btn-info btn-xs">Modificar Fichaje</button> 
                    </form>';
            }
            
            $html .= '</td></tr>';
        }
        
        $html .= '</tbody></table>';
        
        // Controles de paginación
        if ($totalPages > 1) {
            $html .= '<div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                Página ' . $page . ' de ' . $totalPages . ' 
                                (mostrando ' . $fichajes->count() . ' de ' . $total . ' fichajes)
                            </small>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="btn-group btn-group-sm paginacion-fichajes">';
            
            // Botón anterior
            if ($page > 1) {
                $html .= '<button type="button" class="btn btn-default" data-page="' . ($page - 1) . '" data-user-id="' . $userId . '" data-periodo="' . $periodo . '">
                            <i class="fa fa-chevron-left"></i> Anterior
                          </button>';
            }
            
            // Páginas
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            
            if ($start > 1) {
                $html .= '<button type="button" class="btn btn-default" data-page="1" data-user-id="' . $userId . '" data-periodo="' . $periodo . '">1</button>';
                if ($start > 2) {
                    $html .= '<span class="btn btn-default disabled">...</span>';
                }
            }
            
            for ($i = $start; $i <= $end; $i++) {
                $html .= '<button type="button" class="btn ' . ($i == $page ? 'btn-primary' : 'btn-default') . '" data-page="' . $i . '" data-user-id="' . $userId . '" data-periodo="' . $periodo . '">' . $i . '</button>';
            }
            
            if ($end < $totalPages) {
                if ($end < $totalPages - 1) {
                    $html .= '<span class="btn btn-default disabled">...</span>';
                }
                $html .= '<button type="button" class="btn btn-default" data-page="' . $totalPages . '" data-user-id="' . $userId . '" data-periodo="' . $periodo . '">' . $totalPages . '</button>';
            }
            
            // Botón siguiente 
            if ($page < $totalPages) {
                $html .= '<button type="button" class="btn btn-default" data-page="' . ($page + 1) . '" data-user-id="' . $userId . '" data-periodo="' . $periodo . '">
                            Siguiente <i class="fa fa-chevron-right"></i>
                          </button>';
            }
            
            $html .= '    </div>
                        </div>
                      </div>';
        }
        
        // Información sobre el rango de fechas
        if ($fichajes->count() > 0) {
            $fecha_mas_reciente = $fichajes->first()->fecha;
            $fecha_mas_antigua = $fichajes->last()->fecha;
            
            $html .= '<div class="text-muted" style="font-size: 11px; margin-top: 10px;">
                        <i class="fa fa-calendar"></i> 
                        Desde: ' . \Carbon\Carbon::parse($fecha_mas_antigua)->format('d/m/Y H:i') . ' 
                        hasta: ' . \Carbon\Carbon::parse($fecha_mas_reciente)->format('d/m/Y H:i') . '
                      </div>';
        }
        
        $html .= '</div>'; // Cerrar fichajes-container
        
        return $html;
    }

    /**
     * Mostrar interfaz para modificar fichajes de un usuario
     *
     * @param int $userId
     * @return \Illuminate\Http\Response
     */
    public function modificarFichajesUsuario($userId)
    {
        if(\Auth::user()->compruebaSeguridad('mostrar-fichajes') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj', '..no tiene permisos para acceder a esta sección');

        // Sin permiso de ver todos, solo puede modificar sus propios fichajes
        if(!\Auth::user()->compruebaSeguridad('ver-todos-fichajes') && \Auth::user()->id != $userId)
            return view('eunomia.mensajes.mensaje_error')->with('msj', '..no tiene permisos para ver los fichajes de otro usuario');

        $user = User::findOrFail($userId);

        $fichajes = Fichaje::where('user_id', $userId)
            ->orderBy('fecha', 'desc')
            ->paginate(15);

        return view('eunomia.fichajes.modificar_fichajes', compact('user', 'fichajes'));
    }

    public function recargaTiempoTrabajado(Request $request)
    {
        $tiempo_trabajado_hoy =  $this->sumaHorasDia(date('Y-m-d'));
        //Comprobamos si el día actual esta entre el 15/06 y el 15/09 para aplicar el horario de verano de 7 horas en vez de 8
        $fecha_inicio = date('Y') . '-06-15 00:00:00';
        $fecha_fin = date('Y') . '-09-15 23:59:00';
        $fecha = date('Y-m-d H:i:s');
        if ($this->check_in_range($fecha_inicio, $fecha_fin, $fecha)) {
            $tiempo_total = new DateTime('07:00:00');
            $horas = 7;
        } else {
            //Comprobamos si es viernes para indicarle que son 6 horas
            if (date('w') == 5) {
                $tiempo_total = new DateTime('06:00:00');
                $horas = 6;
            } else {
                $tiempo_total = new DateTime('08:00:00');
                $horas = 8;
            }
        }

        $diferencia = $tiempo_total->diff($tiempo_trabajado_hoy);
        $frase = '';
        $frase_hora_salida = '';
        if ($request->estado == 'entrada')
            if ($diferencia->invert) {
                $frase = '<br><strong>Ánimo sólo</strong> te quedan <strong>' . $diferencia->h . '</strong> hora(s) y <strong>' . $diferencia->i . '</strong> minuto(s) para terminar tu <strong>jornada laboral</strong> de <strong>' . $horas . '</strong> horas.';
                $hora_actual = new DateTime(date('H:i:s'));
                $hora_actual->modify('+' . $diferencia->h . ' hours');
                $hora_actual->modify('+' . $diferencia->i . ' minutes');
                $frase_hora_salida = '<br>Hora <strong>estimada</strong> de salida: <strong>' . $hora_actual->format('H') . ':' . $hora_actual->format('i') . '.';
            } else {
                $frase = '<br><strong>Te estás pasando,</strong> chic@. Para el carro que te va a sentar mal. Te sobran <strong>' . $diferencia->h . '</strong> hora(s) y <strong>' . $diferencia->i . '</strong> minuto(s) de las <strong>' . $horas . '</strong> horas.';
            }
        return '<strong>' . $tiempo_trabajado_hoy->format('H') . '</strong> hora(s), <strong>' . $tiempo_trabajado_hoy->format('i') . '</strong> minuto(s).' . $frase . $frase_hora_salida;
    }

    public function muestraTablaTiempoTrabajado(Request $request)
    {
        $intervalo = $request->input('intervalo', 'mes'); // 'mes' por defecto
        $dias = [];
        $dates = [];
        $array_inicio_fin = [];

        // Por si luego calculamos total del mes
        $mes  = (int) $request->input('mes');
        $anio = (int) $request->input('anio');

        if ($intervalo === 'semana') { // semana actual
            $dias = [0 => 'Lunes', 1 => 'Martes', 2 => 'Miércoles', 3 => 'Jueves', 4 => 'Viernes'];

            // Calculamos la semana actual
            $semana_actual = $this->inicio_fin_semana(date('Y-m-d H:i:s'));
            $fi = Carbon::createFromFormat('Y-m-d H:i:s', $semana_actual['fechaInicio']);
            $ff = Carbon::createFromFormat('Y-m-d H:i:s', $semana_actual['fechaFin']);

            // Rango días de la semana actual - solo lunes a viernes
            $dates = [];
            for ($date = $fi->copy(); $date->lte($ff); $date->addDay()) {
                $dayOfWeek = $date->format('N'); // 1=Monday, 7=Sunday
                if ($dayOfWeek >= 1 && $dayOfWeek <= 5) { // Solo lunes a viernes
                    $dates[] = $date->format('Y-m-d');
                }
            }

            // Rango inicio/fin para consulta SQL
            $array_inicio_fin = [
                $fi->copy()->startOfDay()->format('Y-m-d H:i:s'),
                $ff->copy()->endOfDay()->format('Y-m-d H:i:s'),
            ];
        } else {
            // Normalizamos mes/año
            if ($mes < 1 || $mes > 12)  $mes  = (int) date('n');
            if ($anio < 2000 || $anio > 2100) $anio = (int) date('Y');

            // Calculamos los días del mes
            $inicioMes = Carbon::create($anio, $mes, 1)->startOfMonth();
            $finMes    = Carbon::create($anio, $mes, 1)->endOfMonth();

            // Rango días del mes
            $dates = $this->generateDateRange($inicioMes->copy(), $finMes->copy());
            $dias  = $dates;

            // Rango inicio/fin para consulta SQL
            $array_inicio_fin = [
                $inicioMes->copy()->startOfDay()->format('Y-m-d H:i:s'),
                $finMes->copy()->endOfDay()->format('Y-m-d H:i:s'),
            ];
        }

        // Si viene user_id > 0, filtramos por ese usuario
        $user_id = $request->user_id;

        // Relleno de colores por minuto
        $array_horas = [];
        $fichajes = Fichaje::when($request->user_id > 0, function ($q) use ($user_id) {
            return $q->where('user_id', $user_id);
        })->when($request->user_id == null, function ($q) {
            return $q->where('user_id', \Auth::id());
        })->whereBetween('fecha', $array_inicio_fin)
            ->orderBy('fecha')
            ->get();

        // Número total de fichajes en el periodo
        $num_fichajes = $fichajes->count();

        // Rango horas: 8..18 (11 columnas)
        $hStart = 8;
        $hEnd   = 18;

        // Agrupar fichajes por día para optimizar
        $fichajesPorDia = [];
        foreach ($fichajes as $fichaje) {
            $dia = Carbon::parse($fichaje->fecha)->format('Y-m-d');
            $fichajesPorDia[$dia][] = $fichaje;
        }

        // Rellenamos array_horas de forma optimizada
        $array_horas = [];
        foreach ($dates as $dia) {
            // Inicializar todas las horas del día en blanco
            for ($h = $hStart; $h <= $hEnd; $h++) {
                for ($m = 0; $m < 60; $m++) {
                    $array_horas[$dia][$h][$m] = '#FFFFFF';
                }
            }

            // Si hay fichajes ese día, procesarlos
            if (isset($fichajesPorDia[$dia])) {
                $fichajes_dia = $fichajesPorDia[$dia];
                \Log::info("Procesando fichajes para día $dia:", ['count' => count($fichajes_dia)]);
                
                $tramos = [];
                
                // Construir tramos entrada-salida
                $entrada = null;
                foreach ($fichajes_dia as $fichaje) {
                    \Log::info("Fichaje:", ['tipo' => $fichaje->tipo, 'fecha' => $fichaje->fecha]);
                    
                    if ($fichaje->tipo === 'entrada') {
                        $entrada = Carbon::parse($fichaje->fecha);
                    } elseif ($fichaje->tipo === 'salida' && $entrada) {
                        $salida = Carbon::parse($fichaje->fecha);
                        $tramos[] = [$entrada, $salida];
                        \Log::info("Tramo completo:", ['entrada' => $entrada->format('H:i'), 'salida' => $salida->format('H:i')]);
                        $entrada = null;
                    }
                }
                
                // Si queda una entrada sin salida, usar hasta ahora o fin del día
                if ($entrada) {
                    $ahora = Carbon::now();
                    $finDia = Carbon::createFromFormat('Y-m-d', $dia)->setTime(18, 0, 0);
                    $salida = ($dia === $ahora->format('Y-m-d') && $ahora->lt($finDia)) ? $ahora : $finDia;
                    $tramos[] = [$entrada, $salida];
                    \Log::info("Tramo sin salida:", ['entrada' => $entrada->format('H:i'), 'salida' => $salida->format('H:i')]);
                }

                \Log::info("Total tramos para día $dia:", ['count' => count($tramos)]);

                // Pintar tramos en el array de horas
                foreach ($tramos as [$inicio, $fin]) {
                    $minutosColoreados = 0;
                    $current = $inicio->copy();
                    while ($current->lte($fin)) {
                        $h = $current->hour;
                        $m = $current->minute;
                        
                        if ($h >= $hStart && $h <= $hEnd) {
                            // Color actual (cian) vs pasado (verde)
                            $esAhora = ($current->format('Y-m-d H') === Carbon::now()->format('Y-m-d H'));
                            $array_horas[$dia][$h][$m] = $esAhora ? '#00C0EF' : '#008D4C';
                            $minutosColoreados++;
                        }
                        
                        $current->addMinute();
                    }
                    \Log::info("Tramo coloreado:", [
                        'inicio' => $inicio->format('H:i'),
                        'fin' => $fin->format('H:i'),
                        'minutos_coloreados' => $minutosColoreados
                    ]);
                }
                
                // DEBUG: Verificar que se están generando datos
                // if (count($tramos) > 0) {
                //     \Log::info("Día $dia: " . count($tramos) . " tramos generados");
                // }
            }
        }

        // Pintamos la tabla
        $cont = 0;
        $tabla = '';

        // Recorremos días
        foreach ($array_horas as $key => $value) {
            $array_horas_dia = [$key . ' 00:00:00', $key . ' 23:59:59'];
            $fichajes_dia = Fichaje::when($request->user_id > 0, function ($q) use ($user_id) {
                return $q->where('user_id', $user_id);
            })
                ->when($request->user_id == null, function ($q) {
                    return $q->where('user_id', \Auth::id());
                })
                ->whereBetween('fecha', $array_horas_dia)
                ->orderBy('fecha')
                ->get();

            // Mostramos el día si tiene fichajes o si es informe completo
            if (
                ($fichajes_dia->count() > 0 && $request->informe_completo == 'false') ||
                ($request->informe_completo == 'true') ||
                (!$request->informe_completo)
            ) {
                // Fila principal del día
                $tabla .= '<tr>';

                // Columna día (con botón desplegable si tiene fichajes)
                if ($intervalo === 'semana') {
                    if ($fichajes_dia->count() > 0) {
                        $tabla .= '<td class="col-dia" style=";padding:4px;width:20%;max-width:20%;font-size:11px;">';
                        $tabla .= '<button type="button" class="btn btn-box-tool" aria-expanded="true" aria-controls="demo' . $cont . '" data-toggle="collapse" data-target="#demo' . $cont . '"><i class="fa fa-plus"></i></button> ';
                    } else {
                        $tabla .= '<td class="col-dia" style=";padding:4px;width:20%;max-width:20%;font-size:11px;">';
                        // Para días sin fichajes en semana, añadimos espacio para alinear con los que sí tienen botón quitado poir coco<span style="margin-left:15px;">&nbsp;</span>
                        $tabla .= '';
                    }
                    $tabla .= '<strong>' . $dias[$cont] . '</strong></td>';
                } else {
                    // Para mes: usar rowspan si hay fichajes
                    $rowspan = ($fichajes_dia->count() > 0) ? ' rowspan="2"' : '';
                    $tabla .= '<td class="col-dia" style=";padding:5px 4px;vertical-align:middle;width:20%;max-width:20%;font-size:11px;"' . $rowspan . '>';
                    $tabla .= '<strong>' . Carbon::createFromFormat('Y-m-d', $dias[$cont])->format('d/m') . '</strong></td>';
                }

                // Columnas horas (8..18): una <td> por cada hora
                foreach ($value as $horaNumero => $minutos) {
                    $tabla .= ($intervalo === 'semana')
                        ? '<td style="border:1px solid #ddd;padding:0;">'
                        : '<td style="border:1px solid #ddd;padding:10px 0 0 0;">';

                    // Normaliza: aseguramos que $minutos sea array de 0..59
                    if (!is_array($minutos)) {
                        $minutos = [];
                    }

                    // Recorremos los 60 minutos para decidir el color de la hora
                    $hayMinutosPasados = false;  // verde
                    $hayMinutosAhora   = false;  // cian
                    $minutosVerdes = 0;
                    $minutosAzules = 0;

                    for ($m = 0; $m < 60; $m++) {
                        if (!isset($minutos[$m])) continue;
                        if ($minutos[$m] === '#008D4C') {
                            $hayMinutosPasados = true;
                            $minutosVerdes++;
                        }
                        if ($minutos[$m] === '#00C0EF') {
                            $hayMinutosAhora = true;
                            $minutosAzules++;
                        }
                        if ($hayMinutosAhora) break; // cian tiene prioridad visual
                    }

                    // Decide color final de la barra de esa hora
                    $bg = 'transparent';
                    $claseColor = 'is-vacio';
                    if ($hayMinutosAhora) {
                        $bg = '#00C0EF';   // color actual
                        $claseColor = 'is-ahora';
                    } elseif ($hayMinutosPasados) {
                        $bg = '#008D4C';   // color pasado
                        $claseColor = 'is-pasado';
                    } else {
                        $bg = '#FFFFFF';   // sin fichajes
                        $claseColor = 'is-vacio';
                    }

                    // DEBUG: Log del color aplicado
                    if ($minutosVerdes > 0 || $minutosAzules > 0) {
                        \Log::info("Hora $horaNumero del día $key:", [
                            'minutos_verdes' => $minutosVerdes,
                            'minutos_azules' => $minutosAzules,
                            'color_final' => $bg,
                            'clase_css' => $claseColor
                        ]);
                    }

                    // Título de ayuda (opcional)
                    setlocale(LC_ALL, 'es_ES');
                    $fecha_es = new DateTime($key);
                    $fecha_es = strftime('%A %d/%m/%Y', $fecha_es->getTimestamp());
                    $title = $fecha_es . ' ' . sprintf('%02d:00–%02d:59', $horaNumero, $horaNumero);

                    // Barra simple "plana" con el color decidido
                    $tabla .= '<div class="barra-tiempo ' . $claseColor . '" title="' . e($title) . '" style="height: 20px; min-height: 20px;"></div>';
                    $tabla .= '</td>';
                }

                // Columna total del día
                if ($intervalo === 'semana') {
                    $tabla .= '<td class="col-total" style="border:1px solid #ddd;width:32%;max-width:32%;font-size:12px;font-weight:bold;"><strong>';
                    $tabla .= $this->sumaHorasDia($key)->format('H:i');
                    $tabla .= '</strong></td>';
                } else {
                    // Para mes: usar rowspan si hay fichajes
                    $rowspan = ($fichajes_dia->count() > 0) ? ' rowspan="2"' : '';
                    $tabla .= '<td class="col-total" style="border:1px solid #ddd;vertical-align:middle;width:32%;max-width:32%;font-size:12px;font-weight:bold;"' . $rowspan . '><strong>';
                    $tabla .= $this->sumaHorasDia($key, $user_id)->format('H:i');
                    $tabla .= '</strong></td>';
                }
                $tabla .= '</tr>';

                // Fila desplegable con fichajes del día
                if ($fichajes_dia->count() > 0) {
                    if ($intervalo === 'semana') {
                        // Para semana: fila desplegable como estaba antes
                        $tabla .= '<tr class="collapse" id="demo' . $cont . '">';
                        $tabla .= '<td colspan="13" style="border:1px solid #ddd;">';
                        $tabla .= '<table width="100%" cellspacing="0" cellpadding="5" style="border-collapse: collapse;">'; 

                        // Mostramos los fichajes del día
                        $cont_reg_dia = 0;
                        foreach ($fichajes_dia as $fichaje_dia) {
                            $tabla .= '<tr>';
                            $tabla .= '<td width="90%" style="border:1px solid #ddd;padding:5px 0 5px 10px;background:' . ($cont_reg_dia % 2 == 0 ? '#F2F2F2' : '#FFFFFF') . '">';
                            $tabla .= $fichaje_dia->tipo == 'entrada'
                                ? '<i class="fa fa-circle" style="color:#008D4C;"></i>'
                                : '<i class="fa fa-circle" style="color:#D73925;"></i>';
                            $fecha_del_dia = Carbon::createFromFormat('Y-m-d H:i:s', $fichaje_dia->fecha);
                            $tabla .= ' <strong>' . $fecha_del_dia->format('H:i') . '</strong>';
                            $tabla .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . e($fichaje_dia->comentarios);
                            $tabla .= '</td>';
                            $tabla .= '<td width="10%" align="right" style="border:1px solid #ddd;padding:5px 10px 5px 0;background:' . ($cont_reg_dia % 2 == 0 ? '#F2F2F2' : '#FFFFFF') . '">';
                            $tabla .= '<button class="btn btn-xs btn-warning btneditarfichaje" id="btneditarfichaje_' . $fichaje_dia->id . '"><i class="fa fa-edit"></i></button>';
                            $tabla .= '</td>';
                            $tabla .= '</tr>';
                            $cont_reg_dia++;
                        }

                        $tabla .= '</table>';
                        $tabla .= '</td>';
                        $tabla .= '</tr>';
                    } else {
                        // Para mes: una sola fila con todos los fichajes del día en sus columnas correspondientes
                        // No incluimos columnas Día y Total porque tienen rowspan desde la fila principal
                        $tabla .= '<tr style="font-size: 10px; background:#F2F2F2;">';
                        
                        // Agrupar fichajes por hora
                        $fichajes_por_hora = [];
                        foreach ($fichajes_dia as $fichaje_dia) {
                            $fecha_fichaje = Carbon::createFromFormat('Y-m-d H:i:s', $fichaje_dia->fecha);
                            $hora_fichaje = $fecha_fichaje->hour;
                            
                            if (!isset($fichajes_por_hora[$hora_fichaje])) {
                                $fichajes_por_hora[$hora_fichaje] = [];
                            }
                            
                            $fichajes_por_hora[$hora_fichaje][] = [
                                'hora' => $fecha_fichaje->format('H:i'),
                                'tipo' => $fichaje_dia->tipo
                            ];
                        }
                        
                        // Columnas de horas (8 a 18) - solo estas columnas, sin Día y Total
                        for ($h = 8; $h <= 18; $h++) {
                            $tabla .= '<td style="border:1px solid #ddd;padding:10px 0 0 0;background:#F2F2F2;">';
                            
                            if (isset($fichajes_por_hora[$h])) {
                                // Mostrar todos los fichajes de esta hora con el mismo estilo que las barras de tiempo
                                $tabla .= '';
                                foreach ($fichajes_por_hora[$h] as $fichaje) {
                                    $icono = $fichaje['tipo'] == 'entrada' 
                                        ? '<i class="fa fa-circle" style="color:#008D4C; font-size: 8px;"></i>' 
                                        : '<i class="fa fa-circle" style="color:#D73925; font-size: 8px;"></i>';
                                    $tabla .= '<div style="font-size: 9px; line-height: 12px; margin: 1px 0;">' . $icono . ' ' . $fichaje['hora'] . '</div>';
                                }
                                $tabla .= '</div>';
                            } else {
                                // Celda vacía con el mismo estilo que las barras de tiempo <div class="barra-tiempo is-vacio" style="height: 20px; min-height: 20px;">jojo</div>
                                $tabla .= '';
                            }
                            
                            $tabla .= '</td>';
                        }
                        
                        $tabla .= '</tr>';
                    }
                }
            }
            $cont++;
        }

        // Fila total del mes
        if ($intervalo === 'mes') {
            $tabla .= '<tr>';
            $tabla .= '<td style="border:1px solid #ddd;"><strong>Total</strong></td>';
            $tabla .= '<td colspan="11" style="border:1px solid #ddd;"></td>'; // 11 horas
            $tiempo_mes = $this->calculaTiempoMes($mes, $anio, $user_id);
            $tabla .= '<td style="border:1px solid #ddd;"><strong>' . $tiempo_mes[0] . ':' . $tiempo_mes[1] . '*</strong></td>';
            $tabla .= '</tr>';
        }

        return $tabla;
    }


    public function calculaTiempoMes($mes, $anio, $user_id)
    {
        $fecha_inicio = $anio . '-' . $mes . '-01';
        $fec = new DateTime($fecha_inicio);
        $fecha_inicio = $fec->modify('first day of this month')->format('Y-m-d');
        $fecha_fin = $fec->modify('last day of this month')->format('Y-m-d');
        $fim = Carbon::createFromFormat('Y-m-d', $fecha_inicio);
        $ffm = Carbon::createFromFormat('Y-m-d', $fecha_fin);
        $dates = $this->generateDateRange($fim, $ffm);
        $tiempo_acumulado = new DateTime($fecha_inicio . ' 00:00:00');
        foreach ($dates as $dia) {
            $tiempo_mes = $this->sumaHorasDia($dia, $user_id);
            $tiempo_acumulado->modify('+' . $tiempo_mes->format('H') . ' hours');
            $tiempo_acumulado->modify('+' . $tiempo_mes->format('i') . ' minutes');
            $tiempo_acumulado->modify('+' . $tiempo_mes->format('s') . ' seconds');
        }

        $horas = ($tiempo_acumulado->format('d') - 1) * 24 + $tiempo_acumulado->format('H');
        if ($horas < 10)
            $horas = '0' . $horas;
        $minutos = $tiempo_acumulado->format('i');

        return [$horas, $minutos];
    }

    public function muestraTiempoTrabajadoSemana()
    {
        // Calculamos el tiempo trabajado en la semana actual
        $semana_actual = $this->inicio_fin_semana(date('Y-m-d H:i:s'));
        $fecha_inicio_semana = Carbon::createFromFormat('Y-m-d H:i:s', $semana_actual['fechaInicio']);
        $fecha_fin_semana = Carbon::createFromFormat('Y-m-d H:i:s', $semana_actual['fechaFin']);
        $dates_semana = $this->generateDateRange($fecha_inicio_semana, $fecha_fin_semana);

        // Sumamos las horas de cada día
        $tiempo_acumulado = new DateTime('00:00:00');
        foreach ($dates_semana as $dia) {
            $tiempo_semana = $this->sumaHorasDia($dia);
            $tiempo_acumulado->modify('+' . $tiempo_semana->format('H') . ' hours');
            $tiempo_acumulado->modify('+' . $tiempo_semana->format('i') . ' minutes');
            $tiempo_acumulado->modify('+' . $tiempo_semana->format('s') . ' seconds');
        }
        // Redondeamos a horas completas
        return 'Llevas trabajado en <strong>esta semana</strong> <strong>' . (($tiempo_acumulado->format('d') - date('d')) * 24 + $tiempo_acumulado->format('H')) . '</strong> hora(s) y <strong>' . $tiempo_acumulado->format('i') . '</strong> minuto(s).';
    }

    private function inicio_fin_semana($fecha)
    {

        $strFecha = strtotime($fecha);

        $diaInicio = "Monday";
        $diaFin = "Friday";

        $fechaInicio = date('Y-m-d H:i:s', strtotime('last ' . $diaInicio, $strFecha));
        $fechaFin = date('Y-m-d H:i:s', strtotime('next ' . $diaFin, $strFecha));

        if (date("l", $strFecha) == $diaInicio) {
            $fechaInicio = date("Y-m-d H:i:s", $strFecha);
        }
        if (date("l", $strFecha) == $diaFin) {
            $fechaFin = date("Y-m-d 23:59:59", $strFecha); // Hasta el final del viernes
        } else {
            // Si no es viernes, asegurar que termine al final del viernes
            $fechaFin = date("Y-m-d 23:59:59", strtotime('next ' . $diaFin, $strFecha));
        }
        return ["fechaInicio" => $fechaInicio, "fechaFin" => $fechaFin];
    }

    private function generateDateRange(Carbon $start_date, Carbon $end_date)
    {
        $dates = [];
        for ($date = $start_date; $date->lte($end_date); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }
        return $dates;
    }

    private function check_in_range($fecha_inicio, $fecha_fin, $fecha)
    {

        $fecha_inicio = strtotime($fecha_inicio);
        $fecha_fin = strtotime($fecha_fin);
        $fecha = strtotime($fecha);

        if (($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {

            return true;
        } else {

            return false;
        }
    }

    public function estableceHoraFichaje()
    {
        return view('eunomia.fichajes.establece_hora_fichaje');
    }

    public function modificaHoraFichaje($fichaje_id)
    {
        try {
            \Log::info("Intentando cargar fichaje ID: " . $fichaje_id);
            
            $fichaje = Fichaje::findOrFail($fichaje_id);
            \Log::info("Fichaje encontrado:", ['id' => $fichaje->id, 'fecha' => $fichaje->fecha]);
            
            if (is_object($fichaje)) {
                $fecha_fichaje_campo = Carbon::createFromFormat('Y-m-d H:i:s', $fichaje->fecha)->format('Y-m-d');
                $fecha_fichaje = Carbon::createFromFormat('Y-m-d H:i:s', $fichaje->fecha)->format('d/m/Y');
                $hora_fichaje = Carbon::createFromFormat('Y-m-d H:i:s', $fichaje->fecha)->format('H:i');
                $comentarios = $fichaje->comentarios;
                
                \Log::info("Datos preparados:", [
                    'fecha_fichaje' => $fecha_fichaje,
                    'hora_fichaje' => $hora_fichaje,
                    'fichaje_id' => $fichaje_id,
                    'comentarios' => $comentarios
                ]);
                
                return view('eunomia.fichajes.modifica_hora_fichaje', compact('fecha_fichaje', 'hora_fichaje', 'fichaje_id', 'comentarios', 'fecha_fichaje_campo'));
            } else {
                \Log::error("Fichaje no es objeto válido");
                return 'ERROR: Fichaje no válido';
            }
        } catch (\Exception $e) {
            \Log::error("Error en modificaHoraFichaje:", [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'fichaje_id' => $fichaje_id
            ]);
            return 'ERROR: ' . $e->getMessage();
        }
    }

    /**
     * Recibe fecha en formato yyyy-mm-dd
     * Devuelve fecha DateTime en formato h:i:s
     */
    private function sumaHorasDia($fecha, $user_id = null)
    {
        $fecha_inicio = $fecha . ' 00:00:00';
        $fecha_fin = $fecha . ' 23:59:59';

        $array_fechas = [$fecha_inicio, $fecha_fin];

        $fichajes = Fichaje::where('user_id', $user_id > 0 ? $user_id : \Auth::user()->id)
            ->whereBetween('fecha', $array_fechas)
            ->orderBy('fecha')->get();

        $cont = 1;
        $tiempo_acumulado = new DateTime('00:00:00');
        $fecha_fichaje_inicio = '';
        $fecha_fichaje_fin = '';
        $tipo_fichaje = 'entrada';
        $fecha_fichaje_actual = '';
        foreach ($fichajes as $fichaje) {
            if ($cont == 1 && $fichaje->tipo == 'salida') { //Quiere decir que no se cerró el fichaje el día anterior
                $fecha_fichaje_inicio = new DateTime($fecha_inicio);
                $fecha_fichaje_fin = new DateTime($fichaje->fecha);
            } elseif ($fichaje->tipo == 'entrada') {
                $fecha_fichaje_inicio = new DateTime($fichaje->fecha);
            } else {
                $fecha_fichaje_fin = new DateTime($fichaje->fecha);
            }

            if ($fecha_fichaje_inicio != '' && $fecha_fichaje_fin != '') { //Hay rango para sacar tiempo
                $tiempo = $fecha_fichaje_inicio->diff($fecha_fichaje_fin);
                $tiempo_acumulado->modify('+' . $tiempo->h . ' hours');
                $tiempo_acumulado->modify('+' . $tiempo->i . ' minutes');
                $tiempo_acumulado->modify('+' . $tiempo->s . ' seconds');
                $fecha_fichaje_inicio = '';
                $fecha_fichaje_fin = '';
            }
            $cont++;
            $fecha_fichaje_actual = $fichaje->fecha;
            $tipo_fichaje = $fichaje->tipo;
        }

        //Si no se ha cerrado aún el fichaje hay que sumar el tiempo desde la última entrada hasta la hora actual
        if ($tipo_fichaje == 'entrada' && $fecha_fichaje_actual != '') {
            $fecha_fichaje_inicio = new DateTime($fecha_fichaje_actual);
            $fecha_fichaje_fin = new DateTime(date('H:i:s'));
            $tiempo = $fecha_fichaje_inicio->diff($fecha_fichaje_fin);
            $tiempo_acumulado->modify('+' . $tiempo->h . ' hours');
            $tiempo_acumulado->modify('+' . $tiempo->i . ' minutes');
            $tiempo_acumulado->modify('+' . $tiempo->s . ' seconds');
        }

        return $tiempo_acumulado;
    }

    public function informeHorasEmpleadoMes($user_id, $mes, $anio, $informe_completo = false)
    {
        $user = User::findOrFail($user_id);
        return view('eunomia.fichajes.informe_horas_empleado_mes', compact('user', 'mes', 'anio', 'informe_completo'));
    }

    public function eligeAnioMesInformeHorasFichaje($user_id)
    {
        $meses = [
            '1' => 'Enero',
            '2' => 'Febrero',
            '3' => 'Marzo',
            '4' => 'Abril',
            '5' => 'Mayo',
            '6' => 'Junio',
            '7' => 'Julio',
            '8' => 'Agosto',
            '9' => 'Septiembre',
            '10' => 'Octubre',
            '11' => 'Noviembre',
            '12' => 'Diciembre'
        ];

        $anios = [];
        for ($i = 2019; $i < date('Y') + 1; $i++) {
            $anios[$i] = $i;
        }

        return view('eunomia.fichajes.elige_anio_mes_informe_horas_fichaje', compact('anios', 'meses', 'user_id'));
    }
}
