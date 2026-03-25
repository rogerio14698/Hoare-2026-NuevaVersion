@extends('adminlte::page')

@section('content_header')
  <div class="alinearHeader">
    <h1 class="fuenteTitulo">Histórico de tareas</h1>
    <div>
      <a href="{{ route('tasks.index') }}" class="btn-modificar">Volver</a>
      @if( \Auth::user()->compruebaSeguridad('crear-tarea') == true)
        <a href="{{ route('tasks.create') }}" class="btn-modificar">Añadir</a>
      @endif
    </div>
  </div>
@stop

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <table id="list" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>Codigo Proyecto</th>
              <th>Tarea</th>
              <th>Fecha límite</th>
              <th>Responsable's</th>
              <th>Estado</th>
              <th>Comentarios</th>
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
              <th>Comentarios</th>
              @if(\Auth::user()->compruebaSeguridad('editar-tarea') == true || \Auth::user()->compruebaSeguridad('eliminar-tarea') == true)
                <th>Acciones</th>
              @endif
            </tr>
            </tfoot>
            <tbody>

@foreach ($tasks as $task)

  <tr>
    <td>{{ $task->project ? $task->project->codigo_proyecto : '' }}</td>
    <td>{{ $task->titulo_tarea }}</td>
    <td>{{ $task->fechaentrega_tarea ? $task->fechaentrega_tarea->toDateString() : '' }}</td>
    <td>
      @if($task->users && count($task->users) > 0)
        @foreach ($task->users as $taski)
          {{ $taski->name }}
        @endforeach
      @endif
    </td>
    @php
      // Mapeo de colores a clases de color AdminLTE
      $labelMap = [
          'success' => 'success', 'warning' => 'warning', 'danger' => 'danger',
          'info' => 'info', 'primary' => 'primary', 'default' => 'secondary',
          'verde' => 'success', 'amarillo' => 'warning', 'naranja' => 'warning',
          'rojo' => 'danger', 'azul' => 'primary', 'gris' => 'secondary', 'celeste' => 'info',
          'green' => 'success', 'yellow' => 'warning', 'red' => 'danger',
          'blue' => 'primary', 'gray' => 'secondary', 'grey' => 'secondary',
          'orange' => 'warning', 'aqua' => 'info',
          'purple' => 'primary', 'black' => 'dark',
      ];
      $taskColor = $task->taskstate ? strtolower(trim($task->taskstate->color ?? 'gray')) : 'gray';
      $bgColor = $labelMap[$taskColor] ?? 'secondary';
    @endphp
    <td><span class="badge badge-{{ $bgColor }}">{{ $task->taskstate ? $task->taskstate->state : '' }}</span></td>
    <td class="text-center">
      {!! ($task->comments && $task->comments->count() > 0) ? '<img class="comentarios" id="comm_' . $task->id . '" alt="' . $task->comments->count() . ' comentario(s)" title="' . $task->comments->count() . ' comentario(s)" src="' . asset('/images/comments.png') . '" width="20" style="cursor: pointer;">' : '' !!}
    </td>
    @if(\Auth::user()->compruebaSeguridad('editar-tarea') == true || \Auth::user()->compruebaSeguridad('eliminar-tarea') == true)
    <td>
      @if( \Auth::user()->compruebaSeguridad('editar-tarea') == true)
        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning btn-sm">
          <i class="fas fa-edit"></i>
        </a>
      @endif
      @if( \Auth::user()->compruebaSeguridad('eliminar-tarea') == true)
        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="display:inline" class="form_eliminar">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger btn-sm">
            <i class="fas fa-trash"></i>
          </button>
        </form>
      @endif
    </td>
    @endif
  </tr>

@endforeach

        </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('css')
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables/dataTables.bootstrap.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables/extensions/Responsive/css/dataTables.responsive.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables/extensions/Buttons/css/buttons.bootstrap.min.css') }}">
  <style>
    .dt-buttons {
      margin-bottom: 15px;
    }
    .dt-buttons .btn {
      margin-right: 5px;
    }
  </style>
@stop

@section('js')
  <!-- DataTables -->
  <script src="{{ asset('vendor/adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('vendor/adminlte/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
  <script src="{{ asset('vendor/adminlte/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js') }}"></script>
  <script src="{{ asset('vendor/adminlte/plugins/datatables/extensions/Responsive/js/dataTables.responsive.js') }}"></script>
  <script src="{{ asset('vendor/adminlte/plugins/datatables/extensions/Buttons/js/dataTables.buttons.min.js') }}"></script>
  <script src="{{ asset('vendor/adminlte/plugins/datatables/extensions/Buttons/js/buttons.bootstrap.min.js') }}"></script>
  <script src="{{ asset('vendor/adminlte/plugins/datatables/extensions/Buttons/js/buttons.html5.min.js') }}"></script>
  <script src="{{ asset('vendor/adminlte/plugins/datatables/extensions/RowReorder/js/dataTables.rowReorder.min.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

  <script>
    $(function () {
      $('#list').DataTable({
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
        initComplete: function () {
          var i = 1;
          this.api().columns().every( function () {
            if (i==1 || i==4) {
              var column = this;
              var select = $('<select class="form-control form-control-sm"><option value=""></option></select>')
                      .appendTo($(column.footer()).empty())
                      .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                        );

                        column
                                .search(val ? '^' + val + '$' : '', true, false)
                                .draw();
                      });

              column.data().unique().sort().each(function (d, j) {
                select.append('<option value="' + d + '">' + d + '</option>')
              });
            }
            i++;
          } );
        },
        dom: 'Blfrtip',
        buttons: [
          {
            extend: 'copy',
            className: 'btn btn-sm btn-secondary',
            text: '<i class="fas fa-copy"></i> Copiar'
          },
          {
            extend: 'excel',
            className: 'btn btn-sm btn-success',
            text: '<i class="fas fa-file-excel"></i> Excel',
            title: 'LISTADO DE TAREAS',
            exportOptions: {
              columns: [0,1,2,3,4]
            }
          },
          {
            extend: 'csv',
            className: 'btn btn-sm btn-info',
            text: '<i class="fas fa-file-csv"></i> CSV',
            title: 'LISTADO DE TAREAS',
            footer: true,
            exportOptions: {
              columns: [0,1,2,3,4]
            }
          },
          {
            extend: 'pdf',
            className: 'btn btn-sm btn-danger',
            text: '<i class="fas fa-file-pdf"></i> PDF',
            title: 'LISTADO DE TAREAS',
            orientation: 'landscape',
            pageSize: 'A4',
            footer: true,
            exportOptions: {
              columns: [0,1,2,3,4]
            }
          }
        ]
      });
    });
  </script>

  <script language="JavaScript">
      $('.btn-danger').click(function(e){
          e.preventDefault();
          var boton = this;
          
          if (confirm('¿Está seguro que desea eliminar el registro?')) {
              $(boton).parent().submit();
          }
      });
  </script>

  <script language="JavaScript">
    @foreach($tasks as $task)
    @if($task->comments->count() > 0)
    $('#comm_{{$task->id}}').click(function(){
      // Crear modal Bootstrap 5
      var modalHtml = `
        <div class="modal fade" id="modal_comm_{{$task->id}}" tabindex="-1" role="dialog">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header bg-primary">
                <h5 class="modal-title">Comentarios tarea "{{$task->titulo_tarea}}"</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="text-center">
                  <i class="fas fa-spinner fa-spin fa-2x"></i> Cargando...
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              </div>
            </div>
          </div>
        </div>
      `;
      
      $('body').append(modalHtml);
      var modal = $('#modal_comm_{{$task->id}}');
      
      // Cargar contenido
      modal.find('.modal-body').load('/eunomia/tasks/muestraComentarios/{{$task->id}}');
      
      // Mostrar modal
      modal.modal('show');
      
      // Limpiar al cerrar
      modal.on('hidden.bs.modal', function () {
        $(this).remove();
      });
    });
    @endif
    @endforeach
  </script>
@stop
