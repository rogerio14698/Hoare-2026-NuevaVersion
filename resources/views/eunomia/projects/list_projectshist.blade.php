@extends('adminlte::page')

@section('content_header')
  <div class="alinearHeader">
    <h1 class="fuenteTitulo">Histórico de proyectos</h1>
    <div>
      <a href="{{ route('projects.index') }}" class="btn-modificar">Volver</a>
      @if( \Auth::user()->compruebaSeguridad('crear-proyecto') == true)
        <a href="{{ route('projects.create') }}" class="btn-modificar">Nuevo</a>
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
              <th>Codigo</th>
              <th>Fecha entrega</th>
              <th>Responsable</th>
              <th>Estado</th>

              <th>Info</th>
              <th>Acciones</th>
            </tr>
            </thead>

            <tfoot>
            <tr>
              <th>Codigo</th>
              <th>Fecha entrega</th>
              <th>Responsable</th>
              <th>Estado</th>

              <th>Info</th>
              <th>Acciones</th>
            </tr>
            </tfoot>
            <tbody>

@foreach ($projects as $project)

  <tr>
    <td><a href="{{ route('projects.show', [$project]) }}">{{ $project->codigo_proyecto }}</a> </td>
    <td>{{$project->fechaentrega_proyecto}}</td>
    <td>{{$project->user->name}}</td>

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
      $projectColor = strtolower(trim(optional($project->projectstate)->color ?? 'gray'));
      $bgColor = $labelMap[$projectColor] ?? 'secondary';
    @endphp
    <td><span class="badge badge-{{ $bgColor }}">{{$project->projectstate->state}}</span></td>

    <td class="text-center">{!! $project->tasks->count()>0?'<img class="tareas" id="task_' . $project->id . '" alt="' . $project->tasks->count() . ' tarea(s)" title="' . $project->tasks->count() . ' tarea(s)" src="' . asset('/images/tasks.png') . '" width="20" style="cursor: pointer;">':'' !!} {!! $project->comments->count()>0?'<img class="comentarios" id="comm_' . $project->id . '" alt="' . $project->comments->count() . ' comentario(s)" title="' . $project->comments->count() . ' comentario(s)" src="' . asset('/images/comments.png') . '" width="20" style="cursor: pointer;">':'' !!}</td>
    <td>
      @if( \Auth::user()->compruebaSeguridad('editar-proyecto') == true)
        <a href="{{ route(!\Auth::user()->isRole('cliente')?'projects.edit':'projects.show', $project) }}" class="btn btn-warning btn-sm">
          <i class="fas fa-edit"></i>
        </a>
      @endif
      @if( \Auth::user()->compruebaSeguridad('eliminar-proyecto') == true)
        <form action="{{ route('projects.destroy', $project->id) }}" method="POST" style="display:inline" class="form_eliminar">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Eliminar</button>
        </form>
      @endif
    </td>
  </tr>

@endforeach
        </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection


@section('js')
  <!-- DataTables -->
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>

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
        dom: 'Blfrtip',
        language: {
          url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
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
            title: 'LISTADO DE PROYECTOS',
            exportOptions: {
              columns: [0,1,2,3,4]
            }
          },
          {
            extend: 'csv',
            className: 'btn btn-sm btn-info',
            text: '<i class="fas fa-file-csv"></i> CSV',
            title: 'LISTADO DE PROYECTOS',
            footer: true,
            exportOptions: {
              columns: [0,1,2,3,4]
            }
          },
          {
            extend: 'pdf',
            className: 'btn btn-sm btn-danger',
            text: '<i class="fas fa-file-pdf"></i> PDF',
            title: 'LISTADO DE PROYECTOS',
            orientation: 'landscape',
            pageSize: 'A4',
            footer: true,
            exportOptions: {
              columns: [0,1,2,3,4]
            }
          }
        ],
        initComplete: function () {
          let i = 1;
          this.api().columns().every( function () {
            if (i==3 || i==5) {
              const column = this;
              const select = $('<select class="form-control form-control-sm"><option value=""></option></select>')
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
                select.append('<option value="' + d.replace(/<[^>]*>?/g, '') + '">' + d.replace(/<[^>]*>?/g, '') + '</option>')
              });
            }
            i++;
          } );
        }
      });
    });
  </script>

  <script language="JavaScript">
      $('.btn-danger').click(function(e){
          e.preventDefault();
          const boton = this;

          if (confirm('¿Está seguro que desea eliminar el registro?')) {
              $(boton).parent().submit();
          }
      });
  </script>

  <script language="JavaScript">
    @foreach($projects as $project)
    @if($project->comments->count() > 0)
    $('#comm_{{$project->id}}').click(function(){
      // Crear modal Bootstrap 5
      var modalHtml = `
        <div class="modal fade" id="modal_comm_{{$project->id}}" tabindex="-1" role="dialog">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header bg-primary">
                <h5 class="modal-title">Comentarios proyecto "{{$project->titulo_proyecto}}"</h5>
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
      var modal = $('#modal_comm_{{$project->id}}');
      
      // Cargar contenido
      modal.find('.modal-body').load('/eunomia/projects/muestraComentarios/{{$project->id}}');
      
      // Mostrar modal
      modal.modal('show');
      
      // Limpiar al cerrar
      modal.on('hidden.bs.modal', function () {
        $(this).remove();
      });
    });
    @endif

    @if($project->tasks->count() > 0)
    $('#task_{{$project->id}}').click(function(){
      // Crear modal Bootstrap 5
      var modalHtml = `
        <div class="modal fade" id="modal_task_{{$project->id}}" tabindex="-1" role="dialog">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header bg-primary">
                <h5 class="modal-title">Tareas proyecto "{{$project->titulo_proyecto}}"</h5>
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
      var modal = $('#modal_task_{{$project->id}}');
      
      // Cargar contenido
      modal.find('.modal-body').load('/eunomia/projects/muestraTareasProyecto/{{$project->id}}');
      
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
