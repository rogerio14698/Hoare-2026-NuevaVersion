@extends('adminlte::page')

@section('content_header')
  <h1>
    Listado
    <small>Histórico de tareas</small>
  </h1>
  @if( \Auth::user()->compruebaSeguridad('crear-tarea') == true)
    <h2><a href= {{ route('eunomia.tasks.create') }} class="btn btn-block btn-success btn-xs"><i class="fa fa-plus"></i> Añadir </a></h2>
  @endif

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

  <tr>
    <td>{{$task->project->codigo_proyecto}}</td>
    <td>{{$task->titulo_tarea}}</td>
    <td>{{$task->fechaentrega_tarea->toDateString()}}




    </td>
      <td>
        @foreach ($task->users as $taski)
          {{$taski->name}}
        @endforeach

</td>

    <td><small class="label bg-{{$task->taskstate->color}}">{{$task->taskstate->state}}</small></td>
    <td>{!! $task->comments->count()>0?'<img class="comentarios" id="comm_' . $task->id . '" data-task-title="' . $task->titulo_tarea . '" alt="' . $task->comments->count() . ' comentario(s)" title="' . $task->comments->count() . ' comentario(s)" src="' . asset('/images/comments.png') . '" width="20">':'' !!}</td>
    @if(\Auth::user()->compruebaSeguridad('editar-tarea') == true || \Auth::user()->compruebaSeguridad('eliminar-tarea') == true)
    <td>@if( \Auth::user()->compruebaSeguridad('editar-tarea') == true)
        {{ link_to_route('tasks.edit', 'Editar', $task, array('class' => 'btn btn btn-warning btn-xs')) }}
      @endif
      @if( \Auth::user()->compruebaSeguridad('eliminar-tarea') == true)
        {{ Form::open(array('method'=> 'DELETE', 'route' => array('tasks.destroy', $task->id),'style'=>'display:inline','class'=>'form_eliminar')) }}
        {{ Form::submit('Eliminar', array('class' => 'btn btn btn-danger btn-xs')) }}
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
  {{-- DataTables y sus plugins --}}
  <script src="{{ asset('vendor/adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('vendor/adminlte/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>

  {{-- Bootstrap Dialog --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/js/bootstrap-dialog.min.js"></script>

  <script>
    $(document).ready(function() {
      // Inicialización de DataTable
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
        language: {
          url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
        initComplete: function() {
          // Filtros en columnas específicas
          this.api().columns([0,3]).each(function(index) {
            var column = this;
            var select = $('<select><option value=""></option></select>')
              .appendTo($(column.footer()).empty())
              .on('change', function() {
                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                column.search(val ? '^' + val + '$' : '', true, false).draw();
              });

            column.data().unique().sort().each(function(d) {
              select.append('<option value="' + d + '">' + d + '</option>');
            });
          });
        },
        dom: 'Blfrtip',
        buttons: [
          'copyHtml5',
          {
            extend: 'excelHtml5',
            title: 'LISTADO DE TAREAS',
            exportOptions: { columns: [0,1,2,3,4] }
          },
          {
            extend: 'csvHtml5',
            title: 'LISTADO DE TAREAS',
            footer: true,
            exportOptions: { columns: [0,1,2,3,4] }
          },
          {
            extend: 'pdfHtml5',
            title: 'LISTADO DE TAREAS',
            orientation: 'landscape',
            pageSize: 'A4',
            footer: true,
            exportOptions: { columns: [0,1,2,3,4] }
          }
        ]
      });

      // Manejador de botones de eliminar
      $(document).on('click', '.btn-danger', function(e) {
        e.preventDefault();
        var $form = $(this).closest('form');
        BootstrapDialog.confirm({
          title: 'Confirmar eliminación',
          message: '¿Está seguro que desea eliminar el registro?',
          type: BootstrapDialog.TYPE_WARNING,
          closable: true,
          btnCancelLabel: 'Cancelar',
          btnOKLabel: 'Eliminar',
          callback: function(result) {
            if (result) {
              $form.submit();
            }
          }
        });
      });

      // Manejador de comentarios
      $(document).on('click', '[id^="comm_"]', function() {
        var taskId = this.id.split('_')[1];
        var taskTitle = $(this).data('task-title');
        
        BootstrapDialog.show({
          title: 'Comentarios tarea "' + taskTitle + '"',
          message: function(dialog) {
            var $message = $('<div></div>');
            $message.load('/eunomia/tasks/muestraComentarios/' + taskId);
            return $message[0];
          },
          size: BootstrapDialog.SIZE_WIDE,
          buttons: [{
            label: 'Cerrar',
            action: function(dialog) {
              dialog.close();
            }
          }],
          onshow: function(dialog) {
            dialog.getModalHeader().css({
              'background-color': '#3C8DBC',
              'color': '#FFF'
            });
          }
        });
      });
    });
  </script>
@stop
