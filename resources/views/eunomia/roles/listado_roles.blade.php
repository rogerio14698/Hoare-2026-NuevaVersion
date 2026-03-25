@extends('adminlte::page')


@section('content_header')
    <div class="alinearHeader">
        <h1 class="fuenteTitulo">Listado de Roles</h1>
        @if( \Auth::user()->compruebaSeguridad('crear-rol') == true)
            <a href="{{ route('roles.create') }}" class="btn-modificar">Nuevo Rol</a>
        @endif
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Listado de roles</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="list" class="table table-bordered table-striped w-100">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Acciones</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach ($roles as $rol)
                                    <tr>
                                        <td>{{$rol->name}}</td>
                                        <td class="text-nowrap">
                                            @if( \Auth::user()->compruebaSeguridad('editar-rol') == true)
                                                <a href="{{ route('roles.edit', $rol) }}" class="btn-modificar">Editar</a>
                                            @endif
                                            @if( \Auth::user()->compruebaSeguridad('eliminar-rol') == true)
                                                <form action="{{ route('roles.destroy', $rol->id) }}" method="POST" style="display:inline" class="form_eliminar">
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
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
 <style>
            /* Unifica bordes y estilo con usuarios */
            table.dataTable.table-bordered {
                border-collapse: collapse !important;
            }
            table.dataTable.table-bordered th,
            table.dataTable.table-bordered td {
                border-width: 1px !important;
            }
            td .btn + .btn {
                margin-left: 5px;
            }
        </style>
@stop

@section('js')

    <!-- page script -->

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/rowreorder/1.2.0/js/dataTables.rowReorder.min.js"></script>
    <script>
      $(function () {
        table = $('#list').DataTable({
          paging: true,
          lengthChange: true,
          searching: true,
          ordering: true,
          info: true,
          stateSave: true,
          responsive: true,
          language: {
            url: "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
          }
        });
      });
    </script>
@stop
