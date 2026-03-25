<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
                <table id="list" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Fecha entrega</th>
                        <th>Responsable</th>
                        <th>Estado</th>
                
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($projects as $project)
                        <tr>
                            <td><a href="{{ route(!Auth::user()->isRole('cliente') ? 'projects.edit' : 'projects.show', $project) }}">{{ $project->codigo_proyecto }}</a> </td>
                            <td>{{$project->fechaentrega_proyecto}}</td>
                            <td>{{$project->user->name}}</td>
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
                              $projectColor = strtolower(trim(optional($project->projectstate)->color ?? 'gray'));
                              $bgColor = $labelMap[$projectColor] ?? 'gray';
                            @endphp
                            <td><small class="label bg-{{ $bgColor }}">{{$project->projectstate->state}}</small></td><td>
                                @if (\Illuminate\Support\Str::startsWith($project->codigo_proyecto,'SER_'))
                                    {!! $project->solicitado_nfs!=''?'<span class="text-green">'.$project->solicitado_nfs.'</span>':'<span class="text-red">No solicitado</span>' !!}
                                @endif
                            </td>
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