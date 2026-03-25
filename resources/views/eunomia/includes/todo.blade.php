<!--Nuevo Header para el Todo List -Sin Boostrap -->
<div class="todoList-card">
    <header>
        <!--Contador para contar todas las tareas totales en la base de datos -->
        <h3>To do List: <span>{{ $todotasks->count() }}</span></h3>
    </header>

    <div class="todo-contendor">
        <!--Aqui se incluyen las tareas de esta semana;
        hay que ver como se puede filtrar para solo esta semana-->

        <ul class="todo-lista">
            @foreach ($todotasks as $task)
                @php
                    //Primero obetenermos la fecha de entrega de la tarea
                    $fechaEntrega = \Carbon\Carbon::parse($task->fechaentrega_tarea);
                    //Luego obtenemos la fecha actual
                    $fechaActual = \Carbon\Carbon::now();
                    //Calculamos la diferencia en días entre la fecha de entrega y la fecha actual
                    $diasRestantes = $fechaEntrega->diffInDays($fechaActual, true);
                    //Verificamos si la tarea está vencida comparando la fecha actual con la fecha de entrega
                    $vencida = $fechaActual->gt($fechaEntrega);
                    //Mostramos el resultado
                    $mensajeDiasRestantes = $diasRestantes > 0 ? "$diasRestantes días restantes" : 'Vencida';
                @endphp
                <li class="todo-item-contenedor">

                    <div class="todo-fila">
                        <div class="drag-icon">
                            <i class="fa fa-arrows-alt"></i>
                        </div>
                        <div class="todo-contenido">
                            <div class="tituloTarea">{{ $task->titulo_tarea }}</div>
                            <div class="todo-comentario">
                                <div class="todo-contadorTiempo">
                                    @if ($vencida)
                                        <span class="todo-vencida">Vencida</span>
                                    @else
                                        <span class="todo-diasRestantes">{{ $mensajeDiasRestantes }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <span class="comentarioTarea">{{ $task->comentario_tarea }}</span>

                        <div class="todo-acciones">
                            <a href="#editModal" data-toggle="modal" class="action-btn"><i class="fa fa-edit"></i></a>
                            <a href="#" class="delete_toggle action-btn" rel="{{ $task->id }}"><i
                                    class="fa fa-trash"></i></a>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
        <button class="btn-nueva-tarea" data-toggle="modal" data-target="#newModal">
            AÑADIR NUEVA TAREA</button>

    </div>
</div>

<!--Incluimos los modales para edición -->
@include('eunomia.includes.modalesToDo')
