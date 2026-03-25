@extends('adminlte::page')

@section('content_header')
  <h1>
    Listado
    <small>Proyectos</small>
  </h1>
  @if( \Auth::user()->compruebaSeguridad('crear-proyecto') == true)
    <h2>{!! link_to_route('projects.create', 'Nuevo', null, array('class' => 'btn btn-block btn-success btn-xs')) !!}</h2>
  @endif

  <ol class="breadcrumb">
    <li><a href="/eunomia"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Proyectos</li>
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
              <th>Codigo</th>
              <th>Fecha entrega</th>
              <th>Responsable</th>
              <th>Estado</th>
              <th>Nº orden trabajo Seresco</th>
              <th></th>
              <th>Acciones</th>
            </tr>
            </thead>

            <tfoot>
            <tr>
              <th>Codigo</th>
              <th>Fecha entrega</th>
              <th>Responsable</th>
              <th>Estado</th>
              <th>Nº orden trabajo Seresco</th>
              <th></th>
              <th>Acciones</th>
            </tr>
            </tfoot>
            <tbody>

@foreach ($projects as $project)

  <tr>
    <td>{{link_to_route('projects.show',$project->codigo_proyecto, array($project), array()) }} </td>
    <td>{{$project->fechaentrega_proyecto}}</td>
    <td>{{$project->user->name}}</td>

    <td><small class="label bg-{{$project->projectstate->color}}">{{$project->projectstate->state}}</small></td>
    <td>
      @if (starts_with($project->codigo_proyecto,'SER_'))
        {!! $project->solicitado_nfs!=''?'<span class="text-green">'.$project->solicitado_nfs.'</span>':'<span class="text-red">No solicitado</span>' !!}
      @endif
    </td>
    <td>{!! $project->tasks->count()>0?'<img class="tareas" id="task_' . $project->id . '" alt="' . $project->tasks->count() . ' tarea(s)" title="' . $project->tasks->count() . ' tarea(s)" src="' . asset('/images/tasks.png') . '" width="20">':'' !!} {!! $project->comments->count()>0?'<img class="comentarios" id="comm_' . $project->id . '" alt="' . $project->comments->count() . ' comentario(s)" title="' . $project->comments->count() . ' comentario(s)" src="' . asset('/images/comments.png') . '" width="20">':'' !!}</td>
    <td>@if( \Auth::user()->compruebaSeguridad('editar-proyecto') == true)
        {{ link_to_route(!\Auth::user()->isRole('cliente')?'projects.edit':'projects.show', !\Auth::user()->isRole('cliente')?'Editar':'Ver', $project, array('class' => 'btn btn btn-warning btn-xs')) }}
      @endif
      @if( \Auth::user()->compruebaSeguridad('eliminar-proyecto') == true)
        {{ Form::open(array('method'=> 'DELETE', 'route' => array('projects.destroy', $project->id),'style'=>'display:inline','class'=>'form_eliminar')) }}
        {{ Form::submit('Eliminar', array('class' => 'btn btn btn-danger btn-xs')) }}
        {{ Form::close() }}
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

@endsection

@section('css')
  <!-- DataTables -->
  <link rel="stylesheet" href="{{asset("vendor/adminlte/plugins/datatables/dataTables.bootstrap.css")}}">
    <link rel="stylesheet" href="{{asset("vendor/adminlte/plugins/datatables/extensions/Tabletools/css/dataTables.tableTools.css")}}">

  <!-- Bootstrap Dialog -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css" />

  <link rel="stylesheet" href="{{asset('css/eunomia.css')}}">

@stop

@section('js')

  <!-- page script -->

  <!-- DataTables -->

  <script src="{{asset("vendor/adminlte/plugins/datatables/jquery.dataTables.min.js")}}"> </script>
  <script src="{{asset("vendor/adminlte/plugins/datatables/dataTables.bootstrap.min.js")}}"> </script>
  <script src="{{asset("vendor/adminlte/plugins/datatables/extensions/Tabletools/js/dataTables.tableTools.js")}}"> </script>

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
          'copyHtml5',
          {
            extend: 'excelHtml5',
            title: 'LISTADO DE PROYECTOS',
            exportOptions: {
              columns: [0,1,2,3,4]
            }
          },
          {
            extend: 'csvHtml5',
            title: 'LISTADO DE PROYECTOS',
            footer: true,
            exportOptions: {
              columns: [0,1,2,3,4]
            }
          },
          {
            extend: 'pdfHtml5',
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
          var i = 1;
          this.api().columns().every( function () {
            if (i==3 || i==5) {
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
                select.append('<option value="' + d.replace(/<[^>]*>?/g, '') + '">' + d.replace(/<[^>]*>?/g, '') + '</option>')
              });
            }
            i++;
          } );
        }

      });
    });
  </script>

  <!-- Bootstrap Dialog -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/js/bootstrap-dialog.min.js"></script>

  <script language="JavaScript">
      $('.btn-danger').click(function(e){
          e.preventDefault();
          boton = this;

          BootstrapDialog.confirm(
              '¿Está seguro que desea eliminar el registro?', function(result) {

                  if (result) {
                      $(boton).parent().submit();
                  }

              });
      });
  </script>

  @php
    $js = "document.addEventListener('DOMContentLoaded', function() {\n";
    foreach($projects as $project) {
      if($project->comments->count() > 0) {
        $js .= "var commBtn = document.getElementById('comm_{$project->id}');\n";
        $js .= "if (commBtn) {\n";
        $js .= "  commBtn.addEventListener('click', function() {\n";
        $js .= "    var dialog = BootstrapDialog.show({\n";
        $js .= "      message: function(dialog) {\n";
        $js .= "        var $message = $('<div></div>');\n";
        $js .= "        var pageToLoad = dialog.getData('pageToLoad');\n";
        $js .= "        $message.load(pageToLoad);\n";
        $js .= "        return $message[0];\n";
        $js .= "      },\n";
        $js .= "      data: {\n";
        $js .= "        'pageToLoad': '/eunomia/projects/muestraComentarios/{$project->id}'\n";
        $js .= "      },\n";
        $js .= "      title: 'Comentarios proyecto \\\"" . addslashes($project->titulo_proyecto) . "\\\"',\n";
        $js .= "      buttons: [{\n";
        $js .= "        label: 'Cerrar',\n";
        $js .= "        action: function(dialogItself){ dialogItself.close(); }\n";
        $js .= "      }]\n";
        $js .= "    });\n";
        $js .= "    dialog.setSize(BootstrapDialog.SIZE_WIDE);\n";
        $js .= "    dialog.getModalHeader().css('background-color', '#3C8DBC');\n";
        $js .= "    dialog.getModalHeader().css('color', '#FFF');\n";
        $js .= "  });\n";
        $js .= "}\n";
      }
      if($project->tasks->count() > 0) {
        $js .= "var taskBtn = document.getElementById('task_{$project->id}');\n";
        $js .= "if (taskBtn) {\n";
        $js .= "  taskBtn.addEventListener('click', function() {\n";
        $js .= "    var dialog = BootstrapDialog.show({\n";
        $js .= "      message: function(dialog) {\n";
        $js .= "        var $message = $('<div></div>');\n";
        $js .= "        var pageToLoad = dialog.getData('pageToLoad');\n";
        $js .= "        $message.load(pageToLoad);\n";
        $js .= "        return $message[0];\n";
        $js .= "      },\n";
        $js .= "      data: {\n";
        $js .= "        'pageToLoad': '/eunomia/projects/muestraTareasProyecto/{$project->id}'\n";
        $js .= "      },\n";
        $js .= "      title: 'Tareas proyecto \\\"" . addslashes($project->titulo_proyecto) . "\\\"',\n";
        $js .= "      buttons: [{\n";
        $js .= "        label: 'Cerrar',\n";
        $js .= "        action: function(dialogItself){ dialogItself.close(); }\n";
        $js .= "      }]\n";
        $js .= "    });\n";
        $js .= "    dialog.setSize(BootstrapDialog.SIZE_WIDE);\n";
        $js .= "    dialog.getModalHeader().css('background-color', '#3C8DBC');\n";
        $js .= "    dialog.getModalHeader().css('color', '#FFF');\n";
        $js .= "  });\n";
        $js .= "}\n";
      }
    }
    $js .= "});\n";
  @endphp
  <script language="JavaScript">
    {!! $js !!}
  </script>
@stop
