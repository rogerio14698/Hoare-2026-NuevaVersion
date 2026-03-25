@extends('adminlte::page')

@section('content_header')
  <h1>
    {{$project->codigo_proyecto}}
    <small>detalle de proyecto</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{ route('eunomia.home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Proyectos</li>
  </ol>
@stop

@section('content')
  <div class="row">
    <div class="col-xs-6">
          <!-- general form elements -->
          <div class="box box-default">

            <!-- /.box-header -->
            <!-- form start -->
          <form action="{{ route('projects.update', $project) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')



          <div class="box-body">


            <div class="form-group">

              <label for="customer_id">Cliente</label>
              <select name="customer_id" id="customer_id" class="form-control">
                <option value="">selecciona un cliente</option>
                @foreach($customers as $id => $customerName)
                  <option value="{{ $id }}" {{ old('customer_id', $project->customer_id) == $id ? 'selected' : '' }}>{{ $customerName }}</option>
                @endforeach
              </select>

            </div>

            <div class="form-group">

              <label for="user_id">Responsable en mg.lab</label>
              <select name="user_id" id="user_id" class="form-control">
                <option value="">selecciona un responsable</option>
                @foreach($users as $id => $userName)
                  <option value="{{ $id }}" {{ old('user_id', $project->user_id) == $id ? 'selected' : '' }}>{{ $userName }}</option>
                @endforeach
              </select>

            </div>


            <div class="form-group">

              <label for="titulo_proyecto">Nombre de Proyecto</label>
              <input type="text" name="titulo_proyecto" id="titulo_proyecto" class="form-control" placeholder="Nombre de Proyecto" value="{{ old('titulo_proyecto', $project->titulo_proyecto) }}">


            </div>



            <label for="fechaentrega_proyecto">Fecha de entrega</label>


            <div class="form-group">

              <div class="input-group date">

                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>

                <input type="text" name="fechaentrega_proyecto" id="fechaentrega_proyecto" class="form-control pull-right" value="{{ old('fechaentrega_proyecto', $project->fechaentrega_proyecto) }}">

              </div>

            </div>




            <div class="form-group">

              <label for="estado_proyecto">Estado de proyecto</label>
              <select name="estado_proyecto" id="estado_proyecto" class="form-control">
                <option value="">Selecciona un estado</option>
                <option value="1" {{ old('estado_proyecto', $project->estado_proyecto) == 1 ? 'selected' : '' }}>En proceso</option>
                <option value="2" {{ old('estado_proyecto', $project->estado_proyecto) == 2 ? 'selected' : '' }}>En espera</option>
                <option value="3" {{ old('estado_proyecto', $project->estado_proyecto) == 3 ? 'selected' : '' }}>Para Facturar</option>
                <option value="4" {{ old('estado_proyecto', $project->estado_proyecto) == 4 ? 'selected' : '' }}>Cerrado</option>
              </select>

            </div>

            @if(!\Auth::user()->isRole('cliente'))
            <div class="form-group">

              <label for="comentario_proyecto">Comentarios</label>
              <textarea name="comentario_proyecto" id="comentario_proyecto" class="form-control" placeholder="Comentários sobre el Proyecto">{{ old('comentario_proyecto', $project->comentario_proyecto) }}</textarea>

            </div>
            @endif



          </div>
          <!-- /.box-body -->

          @if (!\Auth::user()->isRole('cliente'))
          <div class="box-footer">
            <button type="submit" class="btn btn-default">Editar</button>
          </div>
          @endif

                      </form>

          </div>
          <!-- /.box -->
        </div>
      <div class="col-md-6">
          @if( \Auth::user()->compruebaSeguridad('mostrar-tareas') == true || \Auth::user()->isRole('cliente'))

          <div class="box box">
                <div class="box-header">
                    <h3 class="box-title">Tareas del proyecto</h3>

                    <div class="box-tools pull-right">
                        <span data-toggle="tooltip" title="" class="badge bg-gray" data-original-title="{{$cuentatareas}} Tareas">{{$cuentatareas}}</span>
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body" style="display: block;">
                    <div class="box-body">
                        <table id="list" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Tarea</th>
                                <th>Fecha</th>
                                <th>Responsables</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>


                      @foreach ($project->tasks()->orderBy('fechaentrega_tarea', 'asc')->get() as $task)

                        <tr>

                          <td>
                            @if(\Auth::user()->compruebaSeguridad('editar-tarea'))
                              <a href="{{ route('tasks.edit', [$task]) }}">{{ $task->titulo_tarea }}</a>
                            @else
                              {{ $task->titulo_tarea }}
                            @endif
                          </td>

                          @if ($task->fechaentrega_tarea->toDateString() < $fechadehoy)

                              <td class="text-red">{{$task->fechaentrega_tarea->format('d/m/Y')}}</td>
                          @else
                              <td>{{$task->fechaentrega_tarea->format('d/m/Y')}}</td>
                          @endif

                          <td>
                            @foreach ($task->users as $taski)
                              {{$taski->name}}
                            @endforeach

                          </td>

                          <td>
                            @php
                              // Colores válidos de AdminLTE 1.x
                              $allow = ['red','yellow','aqua','blue','light-blue','green','teal','olive','lime','orange','fuchsia','purple','maroon','black','gray'];

                              // Mapeo de colores a clases de label de AdminLTE/Bootstrap
                              $labelMap = [
                                  'success' => 'success',   // Verde
                                  'warning' => 'warning',   // Amarillo/Naranja  
                                  'danger'  => 'danger',    // Rojo
                                  'info'    => 'info',      // Azul claro
                                  'primary' => 'primary',   // Azul
                                  'default' => 'default',   // Gris
                                  'verde'   => 'success',
                                  'amarillo' => 'warning',
                                  'naranja' => 'warning',
                                  'rojo'    => 'danger',
                                  'azul'    => 'primary',
                                  'gris'    => 'default',
                                  'celeste' => 'info',
                                  'green'   => 'success',
                                  'yellow'  => 'warning',
                                  'red'     => 'danger',
                                  'blue'    => 'primary',
                                  'gray'    => 'default',
                                  'grey'    => 'default',
                                  'orange'  => 'warning',
                                  'aqua'    => 'info',
                              ];

                              $raw = $task->taskstate->color ?? 'gray';
                              $raw = strtolower(trim($raw));
                              $labelColor = $labelMap[$raw] ?? 'default';
                          @endphp

                          <small class="label label-{{ $labelColor }}">{{ $task->taskstate->state ?? '' }}</small>

                          </td>
                        </tr>

                        @endforeach


                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box-body -->
              @if( \Auth::user()->compruebaSeguridad('crear-tarea') == true)
              <div class="box-footer clearfix">

                <a href="{{ route('create_WhithProject', $project->id) }}" class="btn btn-block btn-success btn-xs pull-left">Nueva tarea</a>

              </div>
              @endif
            <!-- /.box-footer -->
        </div>
        @endif

        @if(\Auth::user()->compruebaSeguridad('mostrar-comentarios') == true || \Auth::user()->compruebaSeguridad('crear-comentario') == true)
        <div class="box box">
            <div class="box-body">
                @if( \Auth::user()->compruebaSeguridad('mostrar-comentarios') == true)
                    @include('eunomia.comments.list_comments')
                @endif
                @if( \Auth::user()->compruebaSeguridad('crear-comentario') == true)
                    @include('eunomia.comments.form_ins_comments')
                @endif
            </div>
        </div>
        @endif
    </div>
</div>


@endsection

@

@section('js')

<!-- bootstrap datepicker -->
<script src="{{asset('vendor/adminlte/plugins/datepicker/bootstrap-datepicker.js')}}"></script>

<!-- Languaje -->
   <script src="{{asset('vendor/adminlte/plugins/datepicker/locales/bootstrap-datepicker.es.js')}}"></script>


<script type="text/javascript">

//Date picker
$('#fechaentrega_proyecto').datepicker({
  autoclose: true,
  todayHighlight :true,
  weekStart : 1,
  language: 'es',
  format: "yyyy-mm-dd",
});

</script>

<!-- TinyMCE -->
<script src="{{asset('vendor/adminlte/plugins/tinymce/tinymce.min.js')}}"></script>
<script src="{{asset('vendor/adminlte/plugins/tinymce/tinymce_plugin.js')}}"></script>

<!-- DataTables -->

<script src="{{asset("vendor/adminlte/plugins/datatables/jquery.dataTables.min.js")}}"> </script>
<script src="{{asset("vendor/adminlte/plugins/datatables/dataTables.bootstrap.min.js")}}"> </script>

<script>
    $(function () {
        $('#list').DataTable({
            "paging": false,
            "lengthChange": true,
            "searching": false,
            "ordering": true,
            "info": true,
            "stateSave": true,
            "responsive": true,
            "pageLength": 50,
            "displayLength": 50,
            "language": {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            }
        });
    });
</script>

<!-- Comentarios -->
<!-- Bootstrap Dialog -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/js/bootstrap-dialog.min.js"></script>
<script>
    // Comentarios: los handlers de insert/delete/edit están centralizados
    // en form_ins_comments.blade.php con eventos delegados.
</script>

    <script>
        $(document).ready(function(){
           @if(\Auth::user()->isRole('cliente'))
                $('#customer_id').attr('disabled','disabled');
                $('#user_id').attr('disabled','disabled');
                $('#titulo_proyecto').attr('disabled','disabled');
                $('#fechaentrega_proyecto').attr('disabled','disabled');
                $('#estado_proyecto').attr('disabled','disabled');
                tinyMCE.get('comentario_proyecto').setMode('readonly');
           @endif
        });
    </script>

@stop
