<div class="col-md-{{ \Auth::user()->isRole('cliente') ? '12' : '6' }} bg-tareas-col">
    {{-- tareas de la semana --}}
    <div class="card card-primary card-outline movil">
        <div class="card-header">
            <h3 class="card-title"><i class="far fa-fw fa-building"></i> Mis tareas para esta semana</h3>
            <div class="card-tools">
                <span data-toggle="tooltip" title="" class="badge badge-secondary"
                    data-original-title="{{ $tareassemana->count() }} Tareas">{{ $tareassemana->count() }}</span>
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                        class="fas fa-minus"></i></button>
                <button type="button" class="btn btn-tool" data-card-widget="remove"><i
                        class="fas fa-times"></i></button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body p-0" style="display: block;">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                            <tr>
                                <th>Proyecto</th>
                                <th>Tarea</th>
                                <th>Fecha</th>
                                <th></th>
                                <th data-priority="1">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tareassemana as $task)
                                <?php
                                $nombre_tarea = (optional($task->project->customer)->codigo_cliente ?? 'TASK') . '_' . $task->titulo_tarea;
                                ?>
                                <tr>
                                    <td><a
                                            href="{{ route(!Auth::user()->isRole('cliente') ? 'projects.edit' : 'projects.show', [$task->project->id ?? 0]) }}">{{ $task->project ? $task->project->codigo_proyecto : 'Sin proyecto' }}</a>
                                    </td>
                                    </td>
                                    <td>{!! !Auth::user()->isRole('cliente')
                                        ? '<a href="' . route('tasks.edit', [$task]) . '">' . e($nombre_tarea) . '</a>'
                                        : e($nombre_tarea) !!}</td>
                                    </td>
                                    @if ($task->fechaentrega_tarea->toDateString() < $fechayesterday)
                                        <td class="text-red">{{ $task->fechaentrega_tarea->toDateString() }}</td>
                                    @else
                                        <td>{{ $task->fechaentrega_tarea->toDateString() }}</td>
                                    @endif
                                    <td>{!! $task->comments->count() > 0
                                        ? '<img class="comentarios" id="comm_' .
                                            $task->id .
                                            '" alt="' .
                                            $task->comments->count() .
                                            ' comentario(s)" title="' .
                                            $task->comments->count() .
                                            ' comentario(s)" src="' .
                                            asset('/images/comments.png') .
                                            '" width="20">'
                                        : '' !!}
                                    </td>
                                    <td>
                                        @php
                                            // Mapeo de colores a clases de label de AdminLTE/Bootstrap
                                            $labelMap = [
                                                'success' => 'success',
                                                'warning' => 'warning',
                                                'danger' => 'danger',
                                                'info' => 'info',
                                                'primary' => 'primary',
                                                'default' => 'default',
                                                'verde' => 'success',
                                                'amarillo' => 'warning',
                                                'naranja' => 'warning',
                                                'rojo' => 'danger',
                                                'azul' => 'primary',
                                                'gris' => 'default',
                                                'celeste' => 'info',
                                                'green' => 'success',
                                                'yellow' => 'warning',
                                                'red' => 'danger',
                                                'blue' => 'primary',
                                                'gray' => 'default',
                                                'grey' => 'default',
                                                'orange' => 'warning',
                                                'aqua' => 'info',
                                            ];
                                            $taskColor = strtolower(trim($task->taskstate->color ?? 'gray'));
                                            $col = $labelMap[$taskColor] ?? 'default';
                                        @endphp

                                        <small
                                            class="badge badge-{{ $col }}">{{ $task->taskstate->state ?? '' }}</small>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card-body -->

        @if (!\Auth::user()->isRole('cliente'))
            {{-- pie boton añadir tarea --}}
            <div class="card-footer clearfix">
                <a href={{ route('tasks.create') }} class="btn btn-block btn-success btn-xs pull-left">Nueva tarea</a>
            </div>
        @endif
        <!-- /.card-footer -->
    </div>

    {{-- tareas del mes --}}
    <div class="card card-primary card-outline movil">
        <div class="card-header" style="background-color:#F2F2F2">
            <h3 class="card-title"><i class="far fa-fw fa-building"></i> Mis tareas para este mes</h3>
            <div class="card-tools">
                <span data-toggle="tooltip" title="" class="badge badge-secondary"
                    data-original-title="{{ $tareasmes->count() }} Tareas">{{ $tareasmes->count() }}</span>
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                        class="fas fa-minus"></i></button>
                <button type="button" class="btn btn-tool" data-card-widget="remove"><i
                        class="fas fa-times"></i></button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body" style="display: block;">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                            <tr>
                                <th>Proyecto</th>
                                <th>Tarea</th>
                                <th>Fecha</th>
                                <th></th>
                                <th data-priority="1">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tareasmes as $task)
                                <?php
                                $nombre_tarea = (optional($task->project->customer)->codigo_cliente ?? 'TASK') . '_' . $task->titulo_tarea;
                                ?>
                                <tr>
                                    <td><a
                                            href="{{ route(!Auth::user()->isRole('cliente') ? 'projects.edit' : 'projects.show', [$task->project->id ?? 0]) }}">{{ $task->project ? $task->project->codigo_proyecto : 'Sin proyecto' }}</a>
                                    </td>
                                    </td>
                                    <td><a href="{{ route('tasks.edit', [$task]) }}">{{ $nombre_tarea }}</a></td>
                                    @if ($task->fechaentrega_tarea->toDateString() < $fechayesterday)
                                        <td class="text-red">{{ $task->fechaentrega_tarea->toDateString() }}</td>
                                    @else
                                        <td>{{ $task->fechaentrega_tarea->toDateString() }}</td>
                                    @endif
                                    <td>{!! $task->comments->count() > 0
                                        ? '<img class="comentarios" id="comm_' .
                                            $task->id .
                                            '" alt="' .
                                            $task->comments->count() .
                                            ' comentario(s)" title="' .
                                            $task->comments->count() .
                                            ' comentario(s)" src="' .
                                            asset('/images/comments.png') .
                                            '" width="20">'
                                        : '' !!}
                                    </td>
                                    <td>
                                        @php
                                            // Mapeo de colores a clases de label de AdminLTE/Bootstrap
                                            $labelMap = [
                                                'success' => 'success',
                                                'warning' => 'warning',
                                                'danger' => 'danger',
                                                'info' => 'info',
                                                'primary' => 'primary',
                                                'default' => 'default',
                                                'verde' => 'success',
                                                'amarillo' => 'warning',
                                                'naranja' => 'warning',
                                                'rojo' => 'danger',
                                                'azul' => 'primary',
                                                'gris' => 'default',
                                                'celeste' => 'info',
                                                'green' => 'success',
                                                'yellow' => 'warning',
                                                'red' => 'danger',
                                                'blue' => 'primary',
                                                'gray' => 'default',
                                                'grey' => 'default',
                                                'orange' => 'warning',
                                                'aqua' => 'info',
                                            ];
                                            $taskColor = strtolower(trim($task->taskstate->color ?? 'gray'));
                                            $col = $labelMap[$taskColor] ?? 'default';
                                        @endphp

                                        <small
                                            class="badge badge-{{ $col }}">{{ $task->taskstate->state ?? '' }}</small>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card-body -->

        @if (!\Auth::user()->isRole('cliente'))
            {{-- pie boton añadir tarea --}}
            <div class="card-footer clearfix">
                <a href={{ route('tasks.create') }} class="btn btn-block btn-success btn-xs pull-left">Nueva tarea</a>
            </div>
        @endif
        <!-- /.card-footer -->
    </div>

    {{-- y ahora las tareas que faltan --}}

    <div class="card card-secondary card-outline movil">
        <div class="card-header" style="background-color:#F2F2F2">
            <h3 class="card-title"><i class="far fa-fw fa-building"></i> Más tareas</h3>
            <div class="card-tools">
                <span data-toggle="tooltip" title="" class="badge badge-secondary"
                    data-original-title="{{ $tareasparamastarde->count() }} Tareas">{{ $tareasparamastarde->count() }}</span>
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                        class="fas fa-minus"></i></button>
                <button type="button" class="btn btn-tool" data-card-widget="remove"><i
                        class="fas fa-times"></i></button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body" style="display: block;">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                            <tr>
                                <th>Proyecto</th>
                                <th>Tarea</th>
                                <th>Fecha</th>
                                <th></th>
                                <th data-priority="1">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tareasparamastarde as $task)
                                <?php
                                $nombre_tarea = (optional($task->project->customer)->codigo_cliente ?? 'TASK') . '_' . $task->titulo_tarea;
                                ?>
                                <tr>
                                    <td>
                                        <a
                                            href="{{ route(!Auth::user()->isRole('cliente') ? 'projects.edit' : 'projects.show', [$task->project->id ?? 0]) }}">
                                            {{ $task->project ? $task->project->codigo_proyecto : 'Sin proyecto' }}
                                        </a>
                                    </td>
                                    </td>
                                    <td><a href="{{ route('tasks.edit', [$task]) }}">{{ $nombre_tarea }}</a></td>
                                    <td>{{ $task->fechaentrega_tarea->toDateString() }}</td>
                                    <td>{!! $task->comments->count() > 0
                                        ? '<img class="comentarios" id="comm_' .
                                            $task->id .
                                            '" alt="' .
                                            $task->comments->count() .
                                            ' comentario(s)" title="' .
                                            $task->comments->count() .
                                            ' comentario(s)" src="' .
                                            asset('/images/comments.png') .
                                            '" width="20">'
                                        : '' !!}
                                    </td>
                                    <td>
                                        @php
                                            // Mapeo de colores a clases de label de AdminLTE/Bootstrap
                                            $labelMap = [
                                                'success' => 'success',
                                                'warning' => 'warning',
                                                'danger' => 'danger',
                                                'info' => 'info',
                                                'primary' => 'primary',
                                                'default' => 'default',
                                                'verde' => 'success',
                                                'amarillo' => 'warning',
                                                'naranja' => 'warning',
                                                'rojo' => 'danger',
                                                'azul' => 'primary',
                                                'gris' => 'default',
                                                'celeste' => 'info',
                                                'green' => 'success',
                                                'yellow' => 'warning',
                                                'red' => 'danger',
                                                'blue' => 'primary',
                                                'gray' => 'default',
                                                'grey' => 'default',
                                                'orange' => 'warning',
                                                'aqua' => 'info',
                                            ];
                                            $taskColor = strtolower(trim($task->taskstate->color ?? 'gray'));
                                            $col = $labelMap[$taskColor] ?? 'default';
                                        @endphp

                                        <small
                                            class="badge badge-{{ $col }}">{{ $task->taskstate->state ?? '' }}</small>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card-body -->

        @if (!\Auth::user()->isRole('cliente'))
            {{-- pie boton añadir tarea --}}
            <div class="card-footer clearfix">
                <a href={{ route('tasks.create') }} class="btn btn-block btn-success btn-xs pull-left">Nueva tarea</a>
            </div>
        @endif
        <!-- /.card-footer -->
    </div>

    @if (!\Auth::user()->isRole('cliente'))
        <!-- Aquí va el chat -->


        <!-- Comments card -->
        <div class="card card-success card-outline movil">
            <div class="card-header">
                <h3 class="card-title"><i class="far fa-comments"></i> Últimos comentarios</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body chat" style="height: 330px; overflow: auto;" id="chat-card">

            </div>
            <!-- /.chat -->
        </div>
        <!-- /.card (comments card) -->
        <!-- ESTADISTICAS DE PROYECTOS -->
        {{-- <div class="card card-success card-outline movil">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-pie"></i> Estadística de proyectos / tareas</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="chart-responsive">
                            <canvas id="pieChartProyectos" height="320" width="646"
                                style="width: 323px; height: 160px;"></canvas>
                        </div>
                        <!-- ./chart-responsive -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-6">
                        <div class="chart-responsive">
                            <canvas id="pieChartTareas" height="320" width="646"
                                style="width: 323px; height: 160px;"></canvas>
                        </div>
                        <!-- ./chart-responsive -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.card-body -->
        </div> --}}
    @endif
</div>
