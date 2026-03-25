@extends('adminlte::page')

@section('content_header')
  <div class="alinearHeader">
    <h1 class="fuenteTitulo">Listado de Tareas</h1>
    <div class="d-flex gap-2 espaciado-fila">
      @if( \Auth::user()->compruebaSeguridad('crear-tarea') == true)
        <a href="{{ route('tasks.create') }}" class="btn-modificar"> Añadir Tarea</a>
      @endif
      <a href="{{ route('hist.index') }}" class="btn-modificar"> Tareas cerradas</a>
    </div>
  </div>
@stop

@section('content')
  <div class="row ">
    <div class="col-12">

      <div class="card card-primary card-outline">
        <!--<div class="card-header">
          <h3 class="card-title fuente-negro">Listado de tareas</h3> 
        </div>-->
        <div class="card-body bg-tarea ">
          <div class="table-responsive">
            <table id="list" class="table table-bordered table-striped w-100 ">
              <thead>
              <tr class="fuente-negro">
                <th>Codigo Proyecto</th>
                <th>Tarea</th>
                <th>Fecha límite</th>
                <th>Responsable's</th>
                <th>Estado</th>
                <th>Info</th>
                <th class="noprint" data-priority="3">Acciones</th>
              </tr>
              </thead>
              <tfoot>
                    <tr class="fuente-negro">
                      <th>Codigo Proyecto</th>
                      <th>Tarea</th>
                      <th>Fecha límite</th>
                      <th>Responsable's</th>
                      <th>Estado</th>
                      <th>Info</th>
                      <th>Acciones</th>
                    </tr>
              </tfoot>
              <tbody>

@foreach ($tasks as $task)
    <?php
        $nombre_tarea = (optional($task->project->customer)->codigo_cliente ?? 'TASK') . '_' . $task->titulo_tarea;
    ?>

  <tr class="fuente-negro">
    <td class="fuente-blancoBold">{{$task->project ? $task->project->codigo_proyecto : 'Sin proyecto'}}</td>
    <td class="fuente-gris">{{$nombre_tarea}}</td>
    <td class="fuente-grisClaro">{{$task->fechaentrega_tarea->toDateString()}}</td>
    <td class="fuente-nombresGris">
      @foreach ($task->users as $taski)
        {{$taski->name}}
      @endforeach
    </td>
    <td >
      @php
        // Mapeo de colores a clases de color AdminLTE
        //Estos colores algunos ya no son validos.
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
        $newLabelMap = [
          'en proceso' => 'ColorEnProceso',
          'en espera'=> 'ColorEnEspera',
          'en produccion'=> 'ColorEnProduccion',
          'cerrado'=> 'ColorCerrado',
          'cliente'=> 'ColorCliente',
          'por hacer'=> 'ColorPorHacer',
          'facturar'=> 'ColorFacturar',
        ];
        /*Entonces lo que hice aqui. Hacer un nuevo array con los nuevos colores:
        y primero busca dentro de los nuevos colores antes de usar los antiguos
        */
// Lo primero es obtener el nombre de la tarea que queramos usar:
        $estadoOriginal = $task->taskstate->state ?? '';
        $estadoLimpio = strtolower(trim($estadoOriginal));

        // Buscar primero en los nuevos colores, luego en el mapa antiguo
        if (isset($newLabelMap[$estadoLimpio])) {
            $bgColor = 'bg' . $newLabelMap[$estadoLimpio];
        } else {
            // Fallback: usar la propiedad 'color' del taskstate si existe, o 'gray'
            $taskColor = strtolower(trim($task->taskstate->color ?? ''));
            $bgColor = $labelMap[$taskColor] ?? 'gray';
        }

      @endphp
      <span class=" {{ $bgColor }} px-2 py-1">{{ $task->taskstate->state }}</span>
    </td>
    <td class="noprint text-center align-middle">
      @if($task->comments->count()>0)
        <img class="comentarios mx-1" id="comm_{{$task->id}}" alt="{{$task->comments->count()}} comentario(s)" title="{{$task->comments->count()}} comentario(s)" src="{{ asset('/images/comments.png') }}" width="20">
      @endif
    </td>
    <td class="text-nowrap">
      @if(Auth::check() && Auth::user()->compruebaSeguridad('editar-tarea'))
        <a href="{{ route('tasks.edit', $task) }}" class="btn-modificar">Editar</a>
      @endif
      @if(Auth::check() && Auth::user()->compruebaSeguridad('eliminar-tarea'))
        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="display:inline" class="form_eliminar">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn-modificar">Eliminar</button>
        </form>
      @endif
    </td>
  </tr>

@endforeach




        </tbody>

          </table>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger">
        <h5 class="modal-title" id="deleteModalLabel">Confirmar eliminación</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body fuente-negro">
        ¿Está seguro que desea eliminar esta tarea?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="confirmDelete">Eliminar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de comentarios -->
<div class="modal fade" id="commentsModal" tabindex="-1" role="dialog" aria-labelledby="commentsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title" id="commentsModalLabel">Comentarios</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="commentsContent">
        <!-- Contenido cargado dinámicamente -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('css')
  <!-- DataTables Bootstrap 4 -->

  <style>
    /* Espaciado entre botones en celdas de acciones */
    td .btn + .btn,
    td .btn + form,
    td form + .btn,
    td form + form {
      margin-left: 5px;
    }
    .dt-buttons .btn {
      margin-right: 0.25rem !important;
    }
    /* Eliminar doble borde/sombra en filas */
    table.dataTable.table-striped > tbody > tr.odd > * {
      box-shadow: none !important;
      border-bottom: 1px solid #dee2e6 !important;
    }
    table.dataTable.table-striped > tbody > tr.even > * {
      box-shadow: none !important;
      border-bottom: 1px solid #dee2e6 !important;
    }
  </style>
@stop

@section('js')

  <!-- page script -->


  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

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
                      var select = $('<select><option value=""></option></select>')
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
              'copyHtml5',
              {
                  extend: 'excelHtml5',
                  title: 'LISTADO DE TAREAS',
                  exportOptions: {
                      columns: [0,1,2,3,4]
                  }
              },
              {
                  extend: 'csvHtml5',
                  title: 'LISTADO DE TAREAS',
                  footer: true,
                  exportOptions: {
                      columns: [0,1,2,3,4]
                  }
              },
              {
                  extend: 'pdfHtml5',
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
      let formToSubmit = null;

      $('.btn_eliminar').click(function(e){
          e.preventDefault();
          formToSubmit = $(this).parent();
          $('#deleteModal').modal('show');
      });

      $('#confirmDelete').click(function(){
          if(formToSubmit) {
              formToSubmit.submit();
          }
      });
  </script>

    <script language="JavaScript">
        @foreach($tasks as $task)
            @if($task->comments->count() > 0)
            $('#comm_{{$task->id}}').click(function(){
                $('#commentsModalLabel').text('Comentarios tarea "{{$task->titulo_tarea}}"');
                $('#commentsContent').load('/eunomia/tasks/muestraComentarios/{{$task->id}}');
                $('#commentsModal').modal('show');
            });
            @endif
        @endforeach
    </script>
@stop
