<div class="tarea-card">
    @php
        //Aqui voy a definir el array map de los colores de las etiquetas.
        //Para que cada estado tenga su propio color //
        $labelMap = [
            'en proceso' => 'bgColorEnProceso',
            'por hacer' => 'bgColorPorHacer',
            'en produccion' => 'bgColorEnProduccion',
            'en espera' => 'bgColorEnEspera',
            'cliente' => 'bgColorCliente',
            'facturar' => 'bgColorFacturar',
            'cerrado' => 'bgColorCerrado',
            'rol' => 'bgColorRol',
            'default' => 'bgColorDefault',
        ];
    @endphp


<header>
    <h3>Tareas <span>Sobrevista</span></h3>
</header>
    <div class="tarea-contenido">
        <div class="tarea-body">
            <!-- Mapeos/variables por tarea se calculan dentro de cada loop -->

            {{-- SECCIÓN: ESTA SEMANA --}}
            <div class="tarea-estaSemana">
                <div class="tarea-header">
                    <div class="tarea-tituloHeader">
                        {{-- El círculo negro con el conteo real --}}
                        <span class="tarea-conteo">{{ $tareassemana->count() }}</span>
                        <h3>TAREAS PARA ESTA SEMANA</h3>
                    </div>
                    @if (!\Auth::user()->isRole('cliente'))
                        <div class="tarea-btn-nuevaTarea">
                            <a href="{{ route('tasks.create') }}"><button><i class="fa fa-plus"></i></button></a>
                        </div>
                    @endif
                </div>



                @foreach ($tareassemana as $task)
                    @php
                        // Lógica de nombre antiguo: CLIENTE_Tarea
                        $nombre_tarea =
                            (optional($task->project->customer)->codigo_cliente ?? 'TASK') . '_' . $task->titulo_tarea;
                        // Clase de vencimiento
                        $esVencida = $task->fechaentrega_tarea->toDateString() < $fechayesterday;
                        // Determinar la clase CSS basada en el estado (usa el mapa)
                        $tareaColor = strtolower(trim($task->taskstate->state ?? 'default'));
                        $tareaColorClass = $labelMap[$tareaColor] ?? $labelMap['default'];
                    @endphp
                    <div class="tarea-fila">
                        <div class="tarea-cliente">
                            <a
                                href="{{ route(!Auth::user()->isRole('cliente') ? 'projects.edit' : 'projects.show', [$task->project->id ?? 0]) }}">
                                <h5>{{ $task->project ? $task->project->codigo_proyecto : 'SIN PROYECTO' }}</h5>
                            </a>
                        </div>

                        <div class="tarea-titulo">
                            {{-- Si no es cliente, puede clickar para editar --}}
                            @if (!Auth::user()->isRole('cliente'))
                                <a href="{{ route('tasks.edit', [$task]) }}">
                                    <h5>{{ $nombre_tarea }}</h5>
                                </a>
                            @else
                                <h5>{{ $nombre_tarea }}</h5>
                            @endif
                        </div>


                        <div class="tarea-fecha {{ $esVencida ? 'text-danger-custom' : '' }}">
                            <h5>{{ $task->fechaentrega_tarea->format('d/m/Y') }}</h5>
                        </div>

                        {{-- Clase dinámica basada en el color de la DB --}}
                        <div class="tarea-estado {{ $tareaColorClass }}">
                            <h5>{{ $task->taskstate->state ?? 'SIN ESTADO' }}</h5>
                        </div>
                    </div>
                @endforeach
            </div>
            {{-- Fin sección esta semana --}}

        

            {{-- SECCIÓN: MÁS TAREAS (Repite la misma lógica para $tareasparamastarde) --}}
            <div class="tareas-todasTareas">
                <div class="tarea-header">
                    <div class="tarea-tituloHeader">
                        <span class="tarea-conteo">{{ $tareasparamastarde->count() }}</span>
                        <h3>MÁS TAREAS</h3>
                    </div>
                    <div class="tarea-btn-nuevaTarea">
                        <a href="{{ route('tasks.create') }}"><button><i class="fa fa-plus"></i></button></a>
                    </div>
                </div>
            </div>
            {{-- Aquí iría el @foreach de $tareasparamastarde similar al de arriba --}}
            @foreach ($tareasparamastarde as $task)
                @php
                    $nombre_tarea =
                        (optional($task->project->customer)->codigo_cliente ?? 'TASK') . '_' . $task->titulo_tarea;
                    $esVencida = $task->fechaentrega_tarea->toDateString() < $fechayesterday;
                    $tareaColor = strtolower(trim($task->taskstate->state ?? 'default'));
                    $tareaColorClass = $labelMap[$tareaColor] ?? $labelMap['default'];
                @endphp
                <div class="tarea-fila">
                    <div class="tarea-cliente">
                        <a
                            href="{{ route(!Auth::user()->isRole('cliente') ? 'projects.edit' : 'projects.show', [$task->project->id ?? 0]) }}">
                            <h5>{{ $task->project ? $task->project->codigo_proyecto : 'SIN PROYECTO' }}</h5>
                        </a>

                    </div>
                    <div class="tarea-titulo">
                        {{-- Si no es cliente, puede clickar para editar --}}
                        @if (!Auth::user()->isRole('cliente'))
                            <a href="{{ route('tasks.edit', [$task]) }}">
                                <h5>{{ $nombre_tarea }}</h5>
                            </a>
                        @else
                            <h5>{{ $nombre_tarea }}</h5>
                        @endif
                    </div>
                    <div class="tarea-fecha">
                        <h5>{{ $task->fechaentrega_tarea->toDateString() }}</h5>
                    </div>
                    <!--Aqui tengo que preguntar para los comentarios de la tarea, si se pone o no
                    Caso se ponga ver como -->


                    <div class="tarea-estado {{ $tareaColorClass }}">
                        <h5>{{ $task->taskstate->state ?? 'SIN ESTADO' }}</h5>
                    </div>
                </div>
            @endforeach

        </div>

    </div>
</div>
