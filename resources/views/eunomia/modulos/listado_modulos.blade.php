@extends('adminlte::page')

@section('content_header')
  <div class="alinearHeader">
    <h1 class="fuenteTitulo">Listado de Módulos</h1>
    {{--@include('eunomia.includes.configuracionAdmin.configNav')--}}

  </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">

            <div class="card-body bgUsuario">
                <div class="card-header">
                    <h3 class="card-title">Módulos del sistema</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="list" class="table table-bordered table-striped w-100">

                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Slug</th>
                                <th>Imagenes</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($modulos as $modulo)

                                <tr>
                                    <td>{{$modulo->nombre}}</td>
                                    <td>{{$modulo->descripcion}}</td>
                                    <td>{{$modulo->slug}}</td>
                                    <td class="text-center">
                                        @if($modulo->imagen == 1)
                                            <i class="fas fa-check-circle text-success" title="Activado"></i>
                                        @else
                                            <i class="fas fa-times-circle text-danger" title="Desactivado"></i>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        @if(\Auth::user()->compruebaSeguridad('editar-modulo') == true)
                                            <a href="{{route('modulos.edit', $modulo)}}" class="btn-modificar">Editar</a>
                                        @endif
                                        @if(\Auth::user()->compruebaSeguridad('eliminar-modulo') == true)
                                            <form action="{{ route('modulos.destroy', $modulo->id) }}" method="POST" style="display:inline" class="form_eliminar">
                                              @csrf
                                              @method('DELETE')
                                              <button type="submit" class="btn-modificar">Eliminar</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>

                            @endforeach

                        </tbody>

                        <tfoot>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Slug</th>
                                <th>Imagenes</th>
                                <th>Acciones</th>
                            </tr>
                        </tfoot>
                    </table>
                  </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

@endsection

@section('css')
  <!-- DataTables for Bootstrap 4 -->
   <style>
    /* Espaciado entre botones en celdas de acciones */
    td .btn + .btn,
    td .btn + form,
    td form + .btn {
      margin-left: 5px;
    }
  </style>
@stop

@section('js')
  <!-- DataTables for Bootstrap 4 -->
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>

  <script>
  $(function () {
    var $t = $('#list');

    // Si ya estaba inicializada, destrúyela
    if ($.fn.dataTable.isDataTable($t)) {
      $t.DataTable().destroy();
    }

    // Inicializa DataTables
    $t.DataTable({
      paging: true,
      lengthChange: true,
      searching: true,
      ordering: true,
      info: true,
      stateSave: true,
      responsive: true,
      language: { url: "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json" },
      initComplete: function () {
        var api = this.api();

        // Filtros en columnas: Slug(2), Imagenes(3)
        [2, 3].forEach(function(idx){
          var column = api.column(idx);
          var $footerCell = $(column.footer());
          if (!$footerCell.length) return;

          var select = $('<select><option value=""></option></select>')
            .appendTo($footerCell.empty())
            .on('change', function () {
              var val = $.fn.dataTable.util.escapeRegex($(this).val());
              column.search(val ? '^' + val + '$' : '', true, false).draw();
            });

          column.data().unique().sort().each(function (d) {
            select.append('<option value="' + d + '">' + d + '</option>');
          });
        });
      }
    });
  });
  </script>

  <!-- Confirmación de eliminación -->
  <script>
  $(function(){
    $('.btn-danger').on('click', function(e){
      e.preventDefault();
      var form = $(this).closest('form');
      if (confirm('¿Está seguro que desea eliminar este módulo?')) {
        form.submit();
      }
    });
  });
  </script>
@stop
