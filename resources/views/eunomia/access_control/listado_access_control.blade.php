@extends('adminlte::page')

@section('content_header')
    <div class="alinearHeader">
        <h1 class="fuenteTitulo">Listado de Control de Accesos</h1>
        <img src="{{ asset('images/help.png') }}" class="help" id="help_list" width="16" height="16" alt="help">
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Listado de control de accesos</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        <strong>Instrucciones:</strong> Para ver el historial de accesos de cada usuario, haga click en el botón <i class="fa fa-plus"></i> verde.
                    </div>
                    <div class="mb-3 d-flex gap-2 align-items-center">
                        <strong class="mr-2" style="line-height: 34px;">Mostrar usuarios:</strong>
                        <div class="btn-group" role="group" aria-label="Filtros de estado">
                            <a href="{{ url('eunomia/control_accesos?estado=activos') }}" class="btn btn-sm {{ (isset($filtro_estado) ? $filtro_estado : 'activos') == 'activos' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                <i class="fa fa-check-circle"></i> Solo Activos
                            </a>
                            <a href="{{ url('eunomia/control_accesos?estado=inactivos') }}" class="btn btn-sm {{ isset($filtro_estado) && $filtro_estado == 'inactivos' ? 'btn-warning' : 'btn-outline-secondary' }}">
                                <i class="fa fa-ban"></i> Solo Inactivos
                            </a>
                            <a href="{{ url('eunomia/control_accesos?estado=todos') }}" class="btn btn-sm {{ isset($filtro_estado) && $filtro_estado == 'todos' ? 'btn-info' : 'btn-outline-secondary' }}">
                                <i class="fa fa-users"></i> Todos
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="list" class="table table-bordered table-striped w-100">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Departamento</th>
                                    <th>Roles</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Departamento</th>
                                    <th>Roles</th>
                                    <th>Acciones</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach ($usuarios as $user)
                                    <tr data-user-id="{{$user->id}}" class="{{ $user->baja == 1 ? 'warning' : '' }}">
                                        <td class="details-control"><i class="fa fa-plus"></i></td>
                                        <td>
                                            {{$user->name}}
                                            @if($user->baja == 1)
                                                <span class="label label-warning" style="margin-left: 5px;"><i class="fa fa-ban"></i> INACTIVO</span>
                                            @endif
                                        </td>
                                        <td>{{$user->email}}</td>
                                        <td>{{ is_object($user->departamento) ? $user->departamento->role_name : '' }}</td>
                                        <td>
                                            @if($user->roles_usuario && $user->roles_usuario->count())
                                                {{ $user->roles_usuario->map(function($ru){ return $ru->roles->name; })->implode(', ') }}
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            <div class="acciones-btns">
                                                @if( \Auth::user()->compruebaSeguridad('editar-usuario') == true)
                                                    <a href="{{ route('users.edit', $user) }}" class="btn-modificar">Editar</a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
    <div class="modal fade" id="myMapModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Mapa de localización</h4>
                </div>
                <div class="modal-body">
                    <div id="map-canvas" style="width: 100%; height:450px;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!--Ayuda en línea-->
    <div id="pop-up"></div>

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
        tr.warning {
            background-color: #fcf8e3 !important;
        }
        tr.warning:hover {
            background-color: #faf2cc !important;
        }
        .label-warning { font-size: 10px; vertical-align: top; }
        #list button[id^="btnaccesos_"]{ display: none !important; }
        .dt-buttons .btn { margin-right: 0.25rem !important; }
        .acciones-btns {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.25rem;
        }
        .acciones-btns > *:not(:last-child) {
            margin-right: 0.5rem !important;
        }
        @media (max-width: 575.98px) {
            .acciones-btns {
                flex-direction: column;
                align-items: stretch;
                gap: 0.5rem;
            }
            .acciones-btns > * {
                width: 100%;
                margin-right: 0 !important;
            }
        }
    </style>
@stop
@section('adminlte_js')
    <!-- DataTables y plugins (CDN, igual que list_customers) -->
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
    <script src="https://cdn.datatables.net/rowreorder/1.2.0/js/dataTables.rowReorder.min.js"></script>
    <script>
        function format(data) {
            var template = $('#rowTemplate').html();
            return template;
        }
        function cargarAccesos(userId, periodo, page, targetRow) {
            if (!userId) return;
            periodo = periodo || 'mes_actual';
            page = page || 1;
            var params = '?periodo=' + periodo + '&page=' + page;
            var url = '/eunomia/control_accesos/historial/' + userId + params;
            var target;
            if (targetRow) {
                target = targetRow.child().find('.child-table');
            } else {
                target = $('.child-table:visible');
            }
            $.get(url, function(data) {
                target.html(data);
            }).fail(function(xhr, status, error) {
                target.html('<p class="text-danger">Error al cargar los accesos: ' + error + '</p>');
            });
        }
        $(function () {
            var table = $('#list').DataTable({
                paging: true,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                stateSave: true,
                responsive: { details: false },
                pageLength: 50,
                displayLength: 50,
                dom: 'Blfrtip',
                buttons: [
                    'copyHtml5',
                    {
                        extend: 'excelHtml5',
                        title: 'LISTADO DE ACCESOS',
                        exportOptions: { columns: [1,2,3,4] }
                    },
                    {
                        extend: 'csvHtml5',
                        title: 'LISTADO DE ACCESOS',
                        footer: true,
                        exportOptions: { columns: [1,2,3,4] }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'LISTADO DE ACCESOS',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        footer: true,
                        exportOptions: { columns: [1,2,3,4] }
                    }
                ],
                columnDefs: [
                    { targets: 0, orderable: false, className: 'details-control' }
                ],
                order: [[1, 'asc']]
            });
            // Abrir / cerrar child rows con contenido cargado por AJAX
            $('#list tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = table.row(tr);
                var userId = $(tr).data('user-id');
                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                    $(this).find('i').removeClass('fa-minus').addClass('fa-plus');
                } else {
                    row.child(format()).show();
                    tr.addClass('shown');
                    $(this).find('i').removeClass('fa-plus').addClass('fa-minus');
                    cargarAccesos(userId, 'mes_actual', 1, row);
                }
            });
            // Manejador para filtros de período dentro del child (delegado)
            $(document).on('click', '.filtros-periodo .btn', function(e){
                e.preventDefault();
                var periodo = $(this).data('periodo');
                var userId = $(this).closest('.accesos-container').data('user-id');
                $(this).siblings().removeClass('btn-primary').addClass('btn-default');
                $(this).removeClass('btn-default').addClass('btn-primary');
                cargarAccesos(userId, periodo, 1);
            });
            // Manejador para paginación dentro del child
            $(document).on('click', '.paginacion-accesos .btn:not(.disabled)', function(e){
                e.preventDefault();
                var page = $(this).data('page');
                var userId = $(this).data('user-id');
                var periodo = $(this).data('periodo');
                if (page && userId) {
                    cargarAccesos(userId, periodo, page);
                }
            });
        });
    </script>
@endsection

