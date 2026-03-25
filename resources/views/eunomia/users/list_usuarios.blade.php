@extends('adminlte::page')


@section('content_header')
    <div class="alinearHeader">
        <h1 class="fuenteTitulo">Listado de Usuarios</h1>
        @if (\Auth::user()->compruebaSeguridad('crear-usuario') == true)
            <a href="{{ route('users.create') }}" class="btn-guardar"> Añadir Usuario</a>
        @endif
    </div>
@stop
@section('content')

    
        

            <div class="card card-primary card-outline w-100">

                <!-- /.card-header -->
                <div class="card-body bgUsuario">
                    <div class="table-responsive ">
                        <table id="list" class="table table-bordered table-striped w-100">

                            <thead>
                                <tr class="fuente-negro">
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Departamento</th>
                                    <th>Roles</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr class="fuente-negro">
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Departamento</th>
                                    <th>Roles</th>
                                    <th>Acciones</th>
                                </tr>
                            </tfoot>
                            <tbody>

                                @foreach ($users as $user)
                                    <tr class="fuente-negro">
                                        <td class="fuenteNombresBlanco">{{ $user->name }}</td>
                                        
                                        <td class="fuenteNombresGris">{{ $user->email }}</td>
                                        <td class="fuente-negro">
                                            {{ optional($user->departamento)->role_name ?? 'Sin departamento' }}</td>
                                        <td>
                                            @if ($user->roles_usuario && $user->roles_usuario->count() > 0)
                                                @foreach ($user->roles_usuario as $roleUsuario)
                                                    <span class="bg-Rol p-1">{{ $roleUsuario->roles->name ?? '' }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">Sin rol</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            @if (\Auth::user()->compruebaSeguridad('editar-usuario') == true)
                                                <a href="{{ route('users.edit', $user) }}" class="btn-accion"> Editar</a>
                                            @endif
                                            @if (\Auth::user()->compruebaSeguridad('eliminar-usuario') == true)
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                    style="display:inline" class="form_eliminar">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-eliminar">Dar de Baja</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

@endsection

@section('css')
    <!-- DataTables Bootstrap 4 -->

    <style>
        /* Espaciado entre botones en celdas de acciones */
        td .btn+.btn,
        td .btn+form,
        td form+.btn,
        td form+form {
            margin-left: 5px;
        }

        /* Eliminar doble borde/sombra en filas */
        table.dataTable.table-striped>tbody>tr.odd>* {
            box-shadow: none !important;
            border-bottom: 1px solid #dee2e6 !important;
        }

        table.dataTable.table-striped>tbody>tr.even>* {
            box-shadow: none !important;
            border-bottom: 1px solid #dee2e6 !important;
        }
    </style>
@stop

@section('js')

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('js/printThis.js') }}"></script>

    <!-- DataTables initialization -->
    <script>
        $(function() {
            var $t = $('#list');

            // si ya estaba inicializada por algún JS global, destrúyela
            if ($.fn.dataTable.isDataTable($t)) {
                $t.DataTable().destroy();
            }

            // re-inicializa SIN ajax; usa los datos del HTML
            $t.DataTable({
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
                        if (i == 3 || i == 4) {
                            var column = this;
                            var select = $('<select><option value=""></option></select>')
                                .appendTo($(column.footer()).empty())
                                .on('change', function() {
                                    var val = $.fn.dataTable.util.escapeRegex($(this)
                                .val());
                                    column.search(val ? '^' + val + '$' : '', true, false)
                                        .draw();
                                });

                            column.data().unique().sort().each(function(d) {
                                select.append('<option value="' + d + '">' + d +
                                    '</option>');
                            });
                        }
                        i++;
                    });
                }
            });
        });
    </script>

    <!-- Bootstrap 5 Modal Scripts -->
    <script>
        $(document).ready(function() {
            // confirmación de borrado con Bootstrap 5 modal
            $('.btn-danger').on('click', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');

                // Crear modal de confirmación
                var modalHtml = `
          <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header bg-danger">
                  <h5 class="modal-title text-white" id="confirmDeleteLabel">Confirmar baja de usuario</h5>
                  <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  ¿Está seguro que desea dar de baja este usuario? (Se mantendrán todos sus registros por requisitos legales)
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                  <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Dar de Baja</button>
                </div>
              </div>
            </div>
          </div>
        `;

                // Eliminar modal anterior si existe
                $('#confirmDeleteModal').remove();

                // Añadir modal al body
                $('body').append(modalHtml);

                // Mostrar modal
                $('#confirmDeleteModal').modal('show');

                // Manejar confirmación
                $('#confirmDeleteBtn').on('click', function() {
                    $('#confirmDeleteModal').modal('hide');
                    form.submit();
                });
            });
        });
    </script>
@endsection
