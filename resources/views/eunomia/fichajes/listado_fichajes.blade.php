@extends('adminlte::page')

@section('content_header')
    <div class="alinearHeader">
        <h1 class="fuenteTitulo">Listado de Fichajes</h1>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-body bgUsuario">
                <div class="card-body">
                    
                    <!-- Filtros de Estado de Usuario (solo para usuarios con editar-fichaje) -->
                    @if(isset($puedeVerTodos) && $puedeVerTodos)
                        <div class="col-md-12">
                            <div class="btn-group" role="group" aria-label="Filtros de estado">
                                <strong style="margin-right: 10px; line-height: 34px;">Mostrar usuarios:</strong>
                                <a href="{{ url('eunomia/fichajes?estado=activos') }}"
                                    class="btn btn-sm {{ $filtro_estado == 'activos' ? 'btn-primary' : 'btn-secondary' }}">
                                    <i class="fas fa-check-circle"></i> Solo Activos
                                </a>
                                <a href="{{ url('eunomia/fichajes?estado=inactivos') }}"
                                    class="btn btn-sm {{ $filtro_estado == 'inactivos' ? 'btn-warning' : 'btn-secondary' }}">
                                    <i class="fas fa-ban"></i> Solo Inactivos
                                </a>
                                <a href="{{ url('eunomia/fichajes?estado=todos') }}"
                                    class="btn btn-sm {{ $filtro_estado == 'todos' ? 'btn-info' : 'btn-secondary' }}">
                                    <i class="fas fa-users"></i> Todos
                                </a>
                            </div>
                            <span class="help-block">
                                <small>
                                    <i class="fas fa-lightbulb"></i>
                                    <strong>Compliance legal:</strong> Los fichajes de usuarios inactivos se conservan para
                                    inspecciones laborales
                                </small>
                            </span>
                        </div>
                    @endif

                    <!--Tabla para mostrar los usuarios -->
                    <table id="list" class="table table-bordered table-striped">
                        <thead>
                            <tr class="fuente-negro">
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Departamento</th>
                                <th>Roles</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            setlocale(LC_ALL, 'es_ES');
                            ?>

                            @foreach ($users as $user)
                                <tr data-user-id="{{ $user->id }}"
                                    class=" {{ $user->baja == 1 ? 'table-warning' : '' }}">
                                    
                                    <td class="fuente-blancoBold">
                                        {{ $user->name }}
                                        @if ($user->baja == 1)
                                            <span class="badge badge-warning" style="margin-left: 5px;">
                                                <i class="fas fa-ban"></i> INACTIVO
                                            </span>
                                        @endif
                                    </td>
                                    <td class="fuenteNombresGris">{{ $user->email }}</td>
                                    <td class="fuente-negro">{{ $user->departamento->role_name }}</td>
                                    <td class="fuenteNombresBlanco">
                                        @if ($user->roles_usuario && $user->roles_usuario->count() > 0)
                                            {{ $user->roles_usuario->first()->roles->name ?? '' }}
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        @if (\Auth::user()->compruebaSeguridad('ver-todos-fichajes') || \Auth::user()->id == $user->id)
                                            <a href="{{ route('fichajes.modificar', $user->id) }}"
                                                class="btn-modificar"> Modificar Fichaje</a>
                                        @endif

                                        @if ($user->baja == 1 && \Auth::user()->compruebaSeguridad('crear-usuario') == true)
                                            <button class="btn btn-success btn-sm btn-reactivar-usuario"
                                                data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}">
                                                <i class="fas fa-check-circle"></i> Reactivar
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Template for the child rows -->
                    <div id="rowTemplate" style="display:none">
                        <table width="100%" cellpadding="10" cellspacing="0" class="child-table">
                            <tr><td>Cargando...</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar fichaje -->
    <div class="modal fade" id="modal-editar-fichaje" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">Editar Fichaje</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-editar-fichaje">
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <input type="hidden" id="edit_fichaje_id" name="fichaje_id">
                        
                        <div class="form-group">
                            <label for="edit_fecha">Fecha:</label>
                            <input type="date" class="form-control" id="edit_fecha" name="fecha" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_hora">Hora:</label>
                            <input type="time" class="form-control" id="edit_hora" name="hora" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_tipo">Tipo:</label>
                            <input type="text" class="form-control" id="edit_tipo" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_comentarios">Comentarios:</label>
                            <textarea class="form-control" id="edit_comentarios" name="comentarios" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <!-- DataTables -->

    <style>
        td.details-control {
            background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3QwJCxAZPPpN9gAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAAhklEQVQ4y+2UsQ2EMBBExxQ0QE1UQCUu8O9fABE1EFEFwg20QAGErs2+wNJ9YiRv5JmXbLzGiIj4QWTmFBEJhQbHASzADHRlFxkLsAF74aEFGM7AAQwNR3UAO/ACrg2nCNyAJ/CquUXEBEwRsQP9hbZJSuk+hfxXf/Wpm/oZvH6Bb6AvwKB5ZQ7tGggAAAAASUVORK5CYII=') no-repeat center center;
            cursor: pointer;
            width: 30px;
        }

        td.details-control i {
            display: none;
        }

        tr.shown td.details-control i.fa-minus {
            display: inline;
        }

        tr:not(.shown) td.details-control i.fa-plus {
            display: inline;
        }

        .child-table {
            padding: 10px;
        }

        /* Estilos para usuarios inactivos */
        tr.table-warning {
            background-color: #fcf8e3 !important;
        }

        tr.table-warning:hover {
            background-color: #faf2cc !important;
        }

        .badge-warning {
            font-size: 10px;
            vertical-align: top;
        }

        /* Estilos para los botones de filtro */
        .btn-group .btn {
            margin-right: 2px;
        }

        /* Espacio entre botones de acciones */
        td .btn+.btn {
            margin-left: 5px;
        }

        .help-block {
            margin-top: 8px;
            margin-bottom: 0px;
        }
    </style>

@stop

@section('js')

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>

    <script>
        function format(data) {
            var template = $('#rowTemplate').html();
            return template;
        }

        // Función para cargar fichajes con filtros - DEFINIDA GLOBALMENTE
        function cargarFichajes(userId, periodo, page, targetRow) {
            // Validar parámetros
            if (!userId) {
                console.error('cargarFichajes: userId es obligatorio');
                return;
            }
            
            periodo = periodo || 'mes_actual';
            page = page || 1;
            
            console.log('cargarFichajes llamada con:', {userId: userId, periodo: periodo, page: page});
            
            var params = '?periodo=' + periodo + '&page=' + page;
            var url = '/eunomia/fichajes/get/' + userId + params;
            
            console.log('URL a llamar:', url);
            
            // Si no se pasa targetRow, buscar la tabla visible
            var target;
            if (targetRow) {
                target = targetRow.child().find('.child-table');
                console.log('Usando targetRow específico');
            } else {
                target = $('.child-table:visible');
                console.log('Usando child-table visible, encontrados:', target.length);
            }
            
            $.get(url, function(data) {
                console.log('Datos recibidos:', data.substring(0, 100) + '...');
                target.html(data);
            }).fail(function(xhr, status, error) {
                console.log('Error cargando fichajes:', {xhr: xhr, status: status, error: error});
                target.html('<p class="text-danger">Error al cargar los fichajes: ' + error + '</p>');
            });
        }

        $(function () {
            var table = $('#list').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "stateSave": true,
                "responsive": true,
                "order": [[0, 'asc']]
            });

            // Add event listener for opening and closing details
            $('#list tbody').on('click', 'td.details-control', function () {
                console.log('Click en details-control detectado');
                
                var tr = $(this).closest('tr');
                var row = table.row(tr);
                var row_data = table.row(tr).data();
                var userId = $(tr).data('user-id'); // Get user ID from data attribute

                console.log('userId encontrado:', userId);

                if (row.child.isShown()) {
                    console.log('Cerrando fila');
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                    $(this).find('i').removeClass('fa-minus').addClass('fa-plus');
                }
                else {
                    console.log('Abriendo fila y cargando fichajes');
                    // Open this row
                    row.child(format()).show();
                    tr.addClass('shown');
                    $(this).find('i').removeClass('fa-plus').addClass('fa-minus');

                    // Load fichajes data via AJAX
                    cargarFichajes(userId, 'mes_actual', 1, row);
                }
            });
            
            // Manejador para filtros de período
            $(document).on('click', '.filtros-periodo .btn', function(e){
                e.preventDefault();
                var periodo = $(this).data('periodo');
                var userId = $(this).closest('.fichajes-container').data('user-id');
                
                // Actualizar estado de botones
                $(this).siblings().removeClass('btn-primary').addClass('btn-default');
                $(this).removeClass('btn-default').addClass('btn-primary');
                
                // Cargar fichajes con nuevo filtro
                cargarFichajes(userId, periodo, 1);
            });
            
            // Manejador para paginación
            $(document).on('click', '.paginacion-fichajes .btn:not(.disabled)', function(e){
                e.preventDefault();
                console.log('Click en paginación detectado');
                
                var page = $(this).data('page');
                var userId = $(this).data('user-id');
                var periodo = $(this).data('periodo');
                
                console.log('Datos de paginación:', {
                    page: page,
                    userId: userId,
                    periodo: periodo
                });
                
                if (page && userId) {
                    cargarFichajes(userId, periodo, page);
                } else {
                    console.error('Faltan datos para paginación:', {page: page, userId: userId});
                }
            });
        });
    </script>

    <script language="JavaScript">
        // Confirmación para eliminar usuarios (ya no se usa en esta vista, pero lo mantengo por compatibilidad)
        $('.btn-danger').click(function(e){
            e.preventDefault();
            boton = this;

            if (confirm('¿Está seguro que desea eliminar el registro?')) {
                $(boton).parent().submit();
            }
        });

        // Manejador delegado para eliminar fichajes específicos
        $(document).on('click', '.form_eliminar_fichaje button', function(e){
            e.preventDefault();
            var form = $(this).closest('form');
            
            if (confirm('¿Está seguro que desea eliminar este fichaje?')) {
                // Hacer petición AJAX para eliminar
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            // Recargar la tabla de fichajes del usuario
                            var container = form.closest('.fichajes-container');
                            console.log('Container encontrado:', container.length);
                            console.log('Container data-user-id:', container.data('user-id'));
                            
                            var userId = container.data('user-id');
                            var periodo = container.find('.filtros-periodo .btn-primary').data('periodo') || 'mes_actual';
                            var page = container.find('.paginacion-fichajes .btn-primary').data('page') || 1;
                            
                            console.log('Datos para cargarFichajes:', {userId: userId, periodo: periodo, page: page});
                            
                            if (userId) {
                                cargarFichajes(userId, periodo, page);
                            } else {
                                console.error('No se pudo obtener userId del container');
                                // Alternativa: buscar en toda la tabla visible
                                var visibleContainer = $('.fichajes-container:visible');
                                console.log('Container visible alternativo:', visibleContainer.length);
                                if (visibleContainer.length > 0) {
                                    var altUserId = visibleContainer.data('user-id');
                                    console.log('UserId alternativo:', altUserId);
                                    if (altUserId) {
                                        cargarFichajes(altUserId, periodo, page);
                                    }
                                }
                            }
                            
                            alert('Fichaje eliminado correctamente');
                        } else {
                            alert('Error al eliminar el fichaje');
                        }
                    },
                    error: function() {
                        alert('Error al eliminar el fichaje');
                    }
                });
            }
        });

        // Manejador para editar fichajes
        $(document).on('click', '.btn-editar-fichaje', function(e){
            e.preventDefault();
            
            var fichajeId = $(this).data('fichaje-id');
            var fecha = $(this).data('fecha');
            var hora = $(this).data('hora');
            var tipo = $(this).data('tipo');
            var comentarios = $(this).data('comentarios');
            
            // Llenar el modal con los datos
            $('#edit_fichaje_id').val(fichajeId);
            $('#edit_fecha').val(fecha);
            $('#edit_hora').val(hora);
            $('#edit_tipo').val(tipo == 'entrada' ? 'Entrada' : 'Salida');
            $('#edit_comentarios').val(comentarios);
            
            // Mostrar el modal
            $('#modal-editar-fichaje').modal('show');
        });

        // Manejador para enviar el formulario de edición
        $('#form-editar-fichaje').submit(function(e){
            e.preventDefault();
            
            var formData = $(this).serialize();
            
            $.ajax({
                url: '/eunomia/fichajes/' + $('#edit_fichaje_id').val(),
                type: 'POST',
                data: formData + '&_method=PUT',
                success: function(response) {
                    if (response.success) {
                        $('#modal-editar-fichaje').modal('hide');
                        
                        // Encontrar el container para recargar con filtros actuales
                        var container = $('.fichajes-container:visible');
                        var userId = container.data('user-id');
                        var periodo = container.find('.filtros-periodo .btn-primary').data('periodo') || 'mes_actual';
                        var page = container.find('.paginacion-fichajes .btn-primary').data('page') || 1;
                        
                        cargarFichajes(userId, periodo, page);
                        
                        alert('Fichaje actualizado correctamente');
                    } else {
                        alert('Error: ' + (response.error || 'No se pudo actualizar el fichaje'));
                    }
                },
                error: function(xhr) {
                    var errorMsg = 'Error al actualizar el fichaje';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                    alert(errorMsg);
                }
            });
        });

        // Manejador para reactivar usuarios
        $(document).on('click', '.btn-reactivar-usuario', function(e){
            e.preventDefault();
            
            var userId = $(this).data('user-id');
            var userName = $(this).data('user-name');
            var button = $(this);
            
            if (confirm('¿Está seguro que desea reactivar al usuario ' + userName + '?\n\nEl usuario volverá a aparecer en la lista de usuarios activos y podrá acceder al sistema.')) {
                // Hacer petición AJAX para reactivar
                $.ajax({
                    url: '/eunomia/users/' + userId + '/reactivar',
                    type: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        button.prop('disabled', true);
                        button.html('<i class="fas fa-spinner fa-spin"></i> Reactivando...');
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('El usuario ' + userName + ' ha sido reactivado correctamente.');
                            window.location.reload();
                        } else {
                            alert('Error al reactivar el usuario: ' + (response.error || 'Error desconocido'));
                            button.prop('disabled', false);
                            button.html('<i class="fas fa-check-circle"></i> Reactivar');
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = 'Error al reactivar el usuario';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        }
                        alert(errorMsg);
                        button.prop('disabled', false);
                        button.html('<i class="fas fa-check-circle"></i> Reactivar');
                    }
                });
            }
        });
    </script>@stop
