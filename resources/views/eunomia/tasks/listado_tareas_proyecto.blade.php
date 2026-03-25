<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
                <table id="list" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Codigo Proyecto</th>
                        <th>Tarea</th>
                        <th>Fecha límite</th>
                        <th>Responsable's</th>
                        <th>Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($tasks as $task)
                        <?php
                        $nombre_tarea = $task->project->customer->codigo_cliente . '_' . $task->titulo_tarea;
                        ?>
                        <tr>
                            <td>{{$task->project->codigo_proyecto}}</td>
                            <td>{{$nombre_tarea}}</td>
                            <td>{{$task->fechaentrega_tarea->toDateString()}}</td>
                            <td>
                                @foreach ($task->users as $taski)
                                    {{$taski->name}}
                                @endforeach
                            </td>
                            @php
                              // Mapeo de colores a clases de color AdminLTE
                              $labelMap = [
                                  'success' => 'green', 'warning' => 'yellow', 'danger' => 'red',
                                  'info' => 'aqua', 'primary' => 'blue', 'default' => 'gray',
                                  'verde' => 'green', 'amarillo' => 'yellow', 'naranja' => 'orange',
                                  'rojo' => 'red', 'azul' => 'blue', 'gris' => 'gray', 'celeste' => 'light-blue',
                                  'green' => 'green', 'yellow' => 'yellow', 'red' => 'red',
                                  'blue' => 'blue', 'gray' => 'gray', 'grey' => 'gray',
                                  'orange' => 'orange', 'aqua' => 'aqua',
                                  'purple' => 'purple', 'black' => 'black',
                              ];
                              $taskColor = strtolower(trim($task->taskstate->color ?? 'gray'));
                              $bgColor = $labelMap[$taskColor] ?? 'gray';
                            @endphp
                            <td><small class="label bg-{{ $bgColor }}">{{$task->taskstate->state}}</small></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->