@extends('adminlte::page')

@section('content_header')
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="alinearHeader">
                    <h3 class="fuenteTitulo">Listado de permisos</h3>
                    <div class="d-flex" style="gap: 8px;">
                        @if (\Auth::user()->compruebaSeguridad('mostrar-modulos') == true)
                            <a href="{{ route('modulos.index') }}" class="btn-nueva-tarea"> Ver Módulos
                            </a>
                        @endif
                        @if (\Auth::user()->compruebaSeguridad('crear-permiso') == true)
                            <a href="{{ route('permisos.create') }}" class="btn-nueva-tarea"> Nuevo Permiso</a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="list" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Modulo</th>
                                    <th>Tipo permiso</th>
                                    <th>Descripción</th>
                                    <th>Slug</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Modulo</th>
                                    <th>Tipo permiso</th>
                                    <th>Descripción</th>
                                    <th>Slug</th>
                                    <th>Acciones</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach ($permissions as $permission)
                                    <tr>
                                        <td>{{ $permission->name }}</td>
                                        <td>{{ optional($permission->modulo)->nombre }}</td>
                                        <td>{{ $permission->permission_type }}</td>
                                        <td>{{ $permission->description }}</td>
                                        <td>{{ $permission->slug }}</td>
                                        <td class="text-nowrap">
                                            @if (\Auth::user()->compruebaSeguridad('editar-permiso') == true)
                                                <a href="{{ route('permisos.edit', $permission) }}"
                                                    class="btn btn-warning btn-sm me-1 mb-1" title="Editar">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                            @endif
                                            @if (\Auth::user()->compruebaSeguridad('eliminar-permiso') == true)
                                                <form action="{{ route('permisos.destroy', $permission->id) }}"
                                                    method="POST" style="display:inline" class="form_eliminar">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-danger btn-sm mb-1 btn-eliminar-permiso"
                                                        title="Eliminar">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </button>
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
@endsection

@section('css')
    <!-- Modal Bootstrap 4 incluido en AdminLTE 3 -->
    <style>
        /* Unifica bordes y estilo con fichajes */
        table.dataTable.table-bordered {
            border-collapse: collapse !important;
        }

        table.dataTable.table-bordered th,
        table.dataTable.table-bordered td {
            border-width: 1px !important;
        }

        td .btn+.btn {
            margin-left: 5px;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/rowreorder/1.2.0/js/dataTables.rowReorder.min.js"></script>
    <script>
        $(function() {
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
                },
                initComplete: function() {
                    var i = 1;
                    this.api().columns().every(function() {
                        if (i == 2 || i == 3) {
                            var column = this;
                            var select = $('<select><option value=""></option></select>')
                                .appendTo($(column.footer()).empty())
                                .on('change', function() {
                                    var val = $.fn.dataTable.util.escapeRegex(
                                        $(this).val()
                                    );
                                    column
                                        .search(val ? '^' + val + '$' : '', true, false)
                                        .draw();
                                });
                            column.data().unique().sort().each(function(d, j) {
                                select.append('<option value="' + d + '">' + d +
                                    '</option>')
                            });
                        }
                        i++;
                    });
                }
            });
        });
    </script>
    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="deletePermisoModal" tabindex="-1" role="dialog" aria-labelledby="deletePermisoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="deletePermisoModalLabel">
                        <i class="fas fa-exclamation-triangle"></i> Confirmar eliminación
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">¿Está seguro que desea eliminar este permiso?</p>
                    <small class="text-muted">Esta acción no se puede deshacer.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeletePermiso">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var deletePermisoForm = null;
        // Usar un selector más específico para evitar conflicto con otros botones
        $(document).on('click', '.btn-eliminar-permiso', function(e) {
            e.preventDefault();
            deletePermisoForm = $(this).closest('form');
            $('#deletePermisoModal').modal('show');
        });
        $('#confirmDeletePermiso').click(function() {
            if (deletePermisoForm) {
                $('#deletePermisoModal').modal('hide');
                deletePermisoForm.submit();
            }
        });
    </script>
@stop
