@php
    // Normalizar contexto: intentar obtener $project desde variables disponibles
    $project = $project ?? ($task->project ?? null) ?? null;

    // Determinar lista de tareas a mostrar de forma segura
    if ($project) {
        $__tasks_list = $project->tasks()->orderBy('fechaentrega_tarea', 'asc')->get();
    } elseif (isset($tasks) && $tasks instanceof \Illuminate\Support\Collection) {
        $__tasks_list = $tasks;
    } elseif (isset($task) && isset($task->project)) {
        $__tasks_list = $task->project->tasks()->orderBy('fechaentrega_tarea', 'asc')->get();
    } else {
        $__tasks_list = collect();
    }

    // Valores por defecto cuando la vista es incluida desde distintos contextos
    $cuentatareas = $cuentatareas ?? $__tasks_list->count();
    $fechadehoy = $fechadehoy ?? \Carbon\Carbon::now()->toDateString();
@endphp

<div class="tasks-container">
    @if( \Auth::user()->compruebaSeguridad('mostrar-tareas') == true || \Auth::user()->isRole('cliente'))

        <div class="tasks-card">
            <div class="tasks-card-header">
                
                <div class="tasks-card-tools">
                    <h3 class="tasks-card-title">Tareas del proyecto : <span class="task-count-badge">{{ $cuentatareas }} </span> </h3>
                </div>
            </div>

            <div class="tasks-card-body">
                <table id="list" class="tasks-table">
                    <thead>
                        <tr>
                            <th>Tarea</th>
                            <th>Fecha</th>
                            <th>Responsables</th>
                            <th>Comentario</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($project->tasks()->orderBy('fechaentrega_tarea', 'asc')->get() as $task)
                            <?php
                                $nombre_tarea = (optional($task->project->customer)->codigo_cliente ?? 'PROJ') . '_' . $task->titulo_tarea;
                            ?>
                            <tr>
                                <td>
                                    @if( \Auth::user()->compruebaSeguridad('editar-tarea'))
                                        <a href="{{ route('tasks.edit', $task) }}">{{ $nombre_tarea }}</a>
                                    @else
                                        {{ $nombre_tarea }}
                                    @endif
                                </td>

                                @if ($task->fechaentrega_tarea->toDateString() < $fechadehoy)
                                    <td class="text-red">{{ $task->fechaentrega_tarea->format('d/m/Y') }}</td>
                                @else
                                    <td>{{ $task->fechaentrega_tarea->format('d/m/Y') }}</td>
                                @endif

                                <td>
                                    @foreach ($task->users as $taski)
                                        {{ $taski->name }}
                                    @endforeach
                                </td>

                                <td>{!! $task->comments->count()>0?'<img class="comentarios" id="comm_' . $task->id . '" alt="' . $task->comments->count() . ' comentario(s)" title="' . $task->comments->count() . ' comentario(s)" src="' . asset('/images/comments.png') . '" width="20">':'' !!}</td>

                                <td>
                                    @php
                                        $labelMap = [
                                            'success' => 'success', 'warning' => 'warning', 'danger'  => 'danger',
                                            'info' => 'info', 'primary' => 'primary', 'default' => 'secondary',
                                            'verde' => 'success', 'amarillo' => 'warning', 'naranja' => 'warning',
                                            'rojo' => 'danger', 'azul' => 'primary', 'gris' => 'secondary',
                                            'celeste' => 'info', 'green' => 'success', 'yellow' => 'warning',
                                            'red' => 'danger', 'blue' => 'primary', 'gray' => 'secondary',
                                            'grey' => 'secondary', 'orange' => 'warning', 'aqua' => 'info',
                                        ];
                                        $raw = $task->taskstate->color ?? 'gray';
                                        $raw = strtolower(trim($raw));
                                        $labelColor = $labelMap[$raw] ?? 'secondary';
                                    @endphp
                                    <span class="task-badge task-badge-{{ $labelColor }}">{{ $task->taskstate->state ?? '' }}</span>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>

    @endif
</div>
