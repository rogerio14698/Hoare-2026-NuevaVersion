@extends('adminlte::page')

@section('content_header')
<h1>
  Listado
  <small>Tareas</small>
</h1>
<div class="row">
  @if( \Auth::user()->compruebaSeguridad('crear-tarea') == true)
  <div class="col-md-6">
    <h2><a href= {{ route('eunomia.tasks.create') }} class="btn btn-block btn-success btn-xs"><i class="fa fa-plus"></i> Añadir </a></h2>
  </div>
  @endif
  <div class="col-md-6">
    <h2><a href=/eunomia/hist/ class="btn btn-block btn-danger btn-xs"><i class="fa fa-eye"></i> Tareas cerradas </a></h2>
  </div>
</div>



<ol class="breadcrumb">
  <li><a href="/eunomia"><i class="fa fa-dashboard"></i> Home</a></li>
  <li class="active">Tareas</li>
</ol>
@stop

@section('content')
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
              <th></th>
              @if(\Auth::user()->compruebaSeguridad('editar-tarea') == true || \Auth::user()->compruebaSeguridad('eliminar-tarea') == true)
              <th>Acciones</th>
              @endif
            </tr>
          </thead>

          <tfoot>
            <tr>
              <th>Codigo Proyecto</th>
              <th>Tarea</th>
              <th>Fecha límite</th>
              <th>Responsable's</th>
              <th>Estado</th>
              <th></th>
              @if(\Auth::user()->compruebaSeguridad('editar-tarea') == true || \Auth::user()->compruebaSeguridad('eliminar-tarea') == true)
              <th>Acciones</th>
              @endif
            </tr>
          </tfoot>
          <tbody>

            @foreach ($tasks as $task)
            <?php
            $nombre_tarea = $task->project->customer->codigo_cliente . '_' . $task->titulo_tarea;
            ?>

            <tr>
              <td>{{$task->project->codigo_proyecto}}</td>
              <td>{{$nombre_tarea}}</td>
              <td>{{$task->fechaentrega_tarea->toDateString()}}




              </td>
              <td>
                @foreach ($task->users as $taski)
                {{$taski->name}}
                @endforeach

              </td>

              <td><small class="label bg-{{$task->taskstate->color}}">{{$task->taskstate->state}}</small></td>
              <td>{!! $task->comments->count()>0?'<img class="comentarios" id="comm_' . $task->id . '" alt="' . $task->comments->count() . ' comentario(s)" title="' . $task->comments->count() . ' comentario(s)" src="' . asset('/images/comments.png') . '" width="20">':'' !!}</td>
              @if(\Auth::user()->compruebaSeguridad('editar-tarea') == true || \Auth::user()->compruebaSeguridad('eliminar-tarea') == true)
              <td>@if( \Auth::user()->compruebaSeguridad('editar-tarea') == true)
                {{ link_to_route('tasks.edit', 'Editar', $task, array('class' => 'btn btn btn-warning btn-xs')) }}
                @endif
                @if( \Auth::user()->compruebaSeguridad('eliminar-tarea') == true)
                {{ Form::open(array('method'=> 'DELETE', 'route' => array('tasks.destroy', $task->id),'style'=>'display:inline','class'=>'form_eliminar')) }}
                {{ Form::submit('Eliminar', array('class' => 'btn btn btn-danger btn-xs btn_eliminar')) }}
                {{ Form::close() }}
                @endif

              </td>
              @endif
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

@endsection

@section('css')
<!-- DataTables -->


<!-- Bootstrap Dialog -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css" />



@stop

@section('js')
  {{-- MARCA DE DEPURACIÓN --}}
  <script>console.log("VISTA ACTUAL: list_task.blade.old.php");</script>

  {{-- jQuery (aseguramos que esté cargado primero) --}}
  @if(!isset($jQuery))
    <script src="{{ asset('vendor/adminlte/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
  @endif

  {{-- DataTables y sus plugins --}}
  <script src="{{ asset('vendor/adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('vendor/adminlte/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
  <script src="{{ asset('vendor/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
  <script src="{{ asset('vendor/jszip/jszip.min.js') }}"></script>
  <script src="{{ asset('vendor/pdfmake/pdfmake.min.js') }}"></script>
  <script src="{{ asset('vendor/pdfmake/vfs_fonts.js') }}"></script>
  <script src="{{ asset('vendor/datatables-buttons/js/buttons.html5.min.js') }}"></script>

  {{-- Bootstrap Dialog --}}
  <script src="{{ asset('vendor/bootstrap3-dialog/js/bootstrap-dialog.min.js') }}"></script>

  <script>
    /* global $, BootstrapDialog */
    $(document).ready(function () {
      // Verificación de dependencias
      if (typeof $ === 'undefined') {
        console.error('Error: jQuery no está cargado');
        return;
      }
      if (typeof $.fn.DataTable === 'undefined') {
        console.error('Error: DataTables no está cargado');
        return;
      }
      if (typeof BootstrapDialog === 'undefined') {
        console.error('Error: BootstrapDialog no está cargado');
        return;
      }

      try {
        // Función para inicializar los diálogos de comentarios
        function initializeCommentDialogs() {
          $('.comentarios').on('click', function() {
            var taskId = $(this).attr('id').replace('comm_', '');
            var taskTitle = $(this).attr('title').replace(' comentario(s)', '');
            
            var dialog = BootstrapDialog.show({
              message: function() {
                var $message = $('<div></div>');
                $message.load('/eunomia/tasks/muestraComentarios/' + taskId);
                return $message[0];
              },
              title: 'Comentarios tarea "' + taskTitle + '"',
              buttons: [{ label: 'Cerrar', action: function(d) { d.close(); } }],
              size: BootstrapDialog.SIZE_WIDE
            });
            
            dialog.getModalHeader().css({ backgroundColor: '#3C8DBC', color: '#FFF' });
          });
        }

        // DataTable
        var table = $('#list').DataTable({
          paging: true,
          lengthChange: true,
          searching: true,
          ordering: true,
          info: true,
          stateSave: true,
          responsive: true,
          pageLength: 50,
          displayLength: 50,
          language: { url: "{{ asset('vendor/datatables/i18n/Spanish.json') }}" },
          initComplete: function () {
            var i = 1;
            this.api().columns().every(function () {
              if (i === 1 || i === 4) {
                var column = this;
                var select = $('<select><option value=""></option></select>')
                  .appendTo($(column.footer()).empty())
                  .on('change', function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    column.search(val ? '^' + val + '$' : '', true, false).draw();
                  });
                column.data().unique().sort().each(function (d) {
                  select.append('<option value="' + d + '">' + d + '</option>');
                });
              }
              i++;
            });
          },
          dom: 'Blfrtip',
          buttons: [
            'copyHtml5',
            { extend: 'excelHtml5', title: 'LISTADO DE TAREAS', exportOptions: { columns: [0,1,2,3,4] } },
            { extend: 'csvHtml5',   title: 'LISTADO DE TAREAS', exportOptions: { columns: [0,1,2,3,4] } },
            { extend: 'pdfHtml5',   title: 'LISTADO DE TAREAS', orientation: 'landscape', pageSize: 'A4',
              exportOptions: { columns: [0,1,2,3,4] } }
          ]
        });

        // Confirmación eliminar
        $('.btn_eliminar').on('click', function (e) {
          e.preventDefault();
          var boton = this;
          BootstrapDialog.confirm({
            title: 'Confirmación',
            message: '¿Está seguro que desea eliminar el registro?',
            type: BootstrapDialog.TYPE_WARNING,
            callback: function(result) {
              if (result) $(boton).closest('form').submit();
            }
          });
        });

        // Inicializar diálogos de comentarios
        initializeCommentDialogs();

      } catch (error) {
        console.error('Error en la inicialización:', error);
      }
    });
  </script>
@endsection
