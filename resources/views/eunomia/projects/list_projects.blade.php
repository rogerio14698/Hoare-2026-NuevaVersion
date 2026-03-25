@extends('adminlte::page')

@section('content_header')
    <div class="alinearHeader">
        <h1 class="fuenteTitulo">Listado de Proyectos</h1>
        <div class="d-flex gap-2 espaciado-fila">
            @if (\Auth::user()->compruebaSeguridad('crear-proyecto') == true)
                <a href="{{ route('projects.create') }}" class="btn-modificar"> Añadir Proyecto</a>
            @endif
            <a href="{{ route('proyhist.index') }}" class="btn-modificar"> Proyectos cerrados</a>
        </div>
    </div>

@stop

@section('content')
    <div class="row ">
        <div class="col-12 ">

            <div class="card card-primary card-outline ">
                <!--        <div class="card-header">
                  <h3 class="card-title">Listado de proyectos</h3>
                </div> -->
                <div class="card-body bg-proyecto">
                    <div class="table-responsive">
                        <table id="list" class="table table-bordered table-striped w-100">
                            <thead>
                                <tr class="fuente-negro">
                                    <th>Codigo</th>
                                    <th>Fecha entrega</th>
                                    <th>Responsable</th>
                                    <th>Estado</th>
                                    <th>Info</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="fuente-negro">
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
                                    <tr class="fuente-negro">
                                        <td><a class="fuente-blancoBold"
                                                href="{{ route(!Auth::user()->isRole('cliente') ? 'projects.edit' : 'projects.show', $project) }}">{{ $project->codigo_proyecto }}</a>
                                        </td>
                                        <td class="fuenteFechaNegro">{{ $project->fechaentrega_proyecto }}</td>
                                        <td class="fuenteNombresGris">
                                            {{ $project->user ? $project->user->name : 'Sin asignar' }}</td>

                                        @php
                                            // Mapeo de colores a clases de label de AdminLTE/Bootstrap
                                            $labelMap = [
                                                'success' => 'success',
                                                'warning' => 'warning',
                                                'danger' => 'danger',
                                                'info' => 'info',
                                                'primary' => 'primary',
                                                'default' => 'default',
                                                'verde' => 'success',
                                                'amarillo' => 'warning',
                                                'naranja' => 'warning',
                                                'rojo' => 'danger',
                                                'azul' => 'primary',
                                                'gris' => 'default',
                                                'celeste' => 'info',
                                                'green' => 'success',
                                                'yellow' => 'warning',
                                                'red' => 'danger',
                                                'blue' => 'primary',
                                                'gray' => 'default',
                                                'grey' => 'default',
                                                'orange' => 'warning',
                                                'aqua' => 'info',
                                                'purple' => 'primary',
                                                'black' => 'default', // Agregados los colores faltantes
                                            ];
                                            $newLabelMap = [
                                                'en proceso' => 'ColorEnProceso',
                                                'en espera' => 'ColorEnEspera',
                                                'en produccion' => 'ColorEnProduccion',
                                                'cerrado' => 'ColorCerrado',
                                                'cliente' => 'ColorCliente',
                                                'por hacer' => 'ColorPorHacer',
                                                'facturar' => 'ColorFacturar',
                                            ];
                                            $estadoProyecto = strtolower(trim($project->projectstate->state ?? ''));
                                            //Primero buscamos los nuevos colores y si no se encuentra el antiguo
                                            if (isset($newLabelMap[$estadoProyecto])) {
                                                $bgColor = 'bg' . $newLabelMap[$estadoProyecto];
                                            } else {
                                                $projectColor = strtolower(
                                                    trim($project->projectstate->color ?? 'gray'),
                                                );
                                                $labelColor = $labelMap[$projectColor] ?? 'default';
                                            }

                                            //Aqui es donde puede fallar. Entonces, en projectstate tiene que tener la posibilidad de fallar
                                            //Por lo que se usa Optinal de laravel
                                            $projectColor = strtolower(trim($project->projectstate->color ?? 'gray'));
                                            $labelColor = $labelMap[$projectColor] ?? 'default';
                                        @endphp
                                        <td class="fuente-negro">
                                            <span
                                                class=" {{ $bgColor }} px-2 py-1">{{ $project->projectstate->state ?? 'Sin estado' }}</span>
                                        </td>
                                        <td class="text-center align-middle">
                                            @if ($project->tasks->count() > 0)
                                                <img class="tareas mx-1" id="task_{{ $project->id }}"
                                                    alt="{{ $project->tasks->count() }} tarea(s)"
                                                    title="{{ $project->tasks->count() }} tarea(s)"
                                                    src="{{ asset('/images/tasks.png') }}" width="20">
                                            @endif
                                            @if ($project->comments->count() > 0)
                                                <img class="comentarios mx-1" id="comm_{{ $project->id }}"
                                                    alt="{{ $project->comments->count() }} comentario(s)"
                                                    title="{{ $project->comments->count() }} comentario(s)"
                                                    src="{{ asset('/images/comments.png') }}" width="20">
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            @if (\Auth::user()->compruebaSeguridad('editar-proyecto') == true)
                                                <a href="{{ route(!\Auth::user()->isRole('cliente') ? 'projects.edit' : 'projects.show', $project) }}"
                                                    class="btn-modificar">
                                                    {{ !\Auth::user()->isRole('cliente') ? 'Editar' : 'Ver' }}
                                                </a>
                                            @endif
                                            @if (\Auth::user()->compruebaSeguridad('eliminar-proyecto') == true)
                                                <form action="{{ route('projects.destroy', $project->id) }}" method="POST"
                                                    style="display:inline" class="form_eliminar">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-modificar">
                                                        Eliminar
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

        <!-- Modal de confirmación de eliminación moderno -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white" id="deleteModalLabel">
                            <i class="fas fa-exclamation-triangle"></i> Confirmar eliminación de proyecto
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">¿Está seguro que desea eliminar este proyecto?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @section('css')
        <!-- DataTables -->



        <style>
            /* Estilos para badges de estado */
            .badge {
                display: inline-block !important;
                padding: .35em .65em !important;
                font-size: 75% !important;
                font-weight: 700 !important;
                line-height: 1 !important;
                color: #fff !important;
                text-align: center !important;
                white-space: nowrap !important;
                vertical-align: baseline !important;
                border-radius: .25rem !important;
            }

            .badge.bg-red {
                background-color: #dc3545 !important;
            }

            .badge.bg-green {
                background-color: #28a745 !important;
            }

            .badge.bg-yellow {
                background-color: #ffc107 !important;
                color: #212529 !important;
            }

            .badge.bg-blue {
                background-color: #007bff !important;
            }

            .badge.bg-purple {
                background-color: #6f42c1 !important;
            }

            .badge.bg-aqua {
                background-color: #17a2b8 !important;
            }

            .badge.bg-orange {
                background-color: #fd7e14 !important;
            }

            .badge.bg-gray {
                background-color: #6c757d !important;
            }

            .badge.bg-black {
                background-color: #343a40 !important;
            }

            /* Espacio entre botones de acciones igual que en días festivos */
            .acciones-btns>*:not(:last-child) {
                margin-right: 0.5rem !important;
            }

            .acciones-btns {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 0.25rem;
            }

            @media (max-width: 575.98px) {
                .acciones-btns {
                    flex-direction: column;
                    align-items: stretch;
                    gap: 0.5rem;
                }

                .acciones-btns>* {
                    width: 100%;
                    margin-right: 0 !important;
                }
            }

            .dt-buttons .btn {
                margin-right: 0.25rem !important;
            }
        </style>

    @stop

    @section('js')

        <!-- DataTables -->
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.bootstrap4.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>

        <script>
            $(function() {
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
                                columns: [0, 1, 2, 3]
                            }
                        },
                        {
                            extend: 'csvHtml5',
                            title: 'LISTADO DE PROYECTOS',
                            footer: true,
                            exportOptions: {
                                columns: [0, 1, 2, 3]
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            title: 'LISTADO DE PROYECTOS',
                            orientation: 'landscape',
                            pageSize: 'A4',
                            footer: true,
                            exportOptions: {
                                columns: [0, 1, 2, 3]
                            }
                        }
                    ],
                    initComplete: function() {
                        let i = 1;
                        this.api().columns().every(function() {
                            if (i == 3) {
                                const column = this;
                                const select = $('<select><option value=""></option></select>')
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
                                    select.append('<option value="' + d.replace(/<[^>]*>?/g,
                                            '') + '">' + d.replace(/<[^>]*>?/g, '') +
                                        '</option>')
                                });
                            }
                            i++;
                        });
                    }
                });
            });
        </script>

        <script language="JavaScript">
            var deleteForm = null;

            $('table .btn_eliminar').click(function(e) {
                e.preventDefault();
                deleteForm = $(this).parent();
                $('#deleteModal').modal('show');
            });

            $('#confirmDelete').click(function() {
                if (deleteForm) {
                    $('#deleteModal').modal('hide');
                    deleteForm.submit();
                }
            });
        </script>

        <script language="JavaScript">
            @foreach ($projects as $project)
                @if ($project->comments->count() > 0)
                    $('#comm_{{ $project->id }}').click(function() {
                        var modal = $(
                            '<div class="modal fade" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header bg-primary"><h5 class="modal-title text-white">Comentarios proyecto "{{ $project->titulo_proyecto }}"</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button></div></div></div></div>'
                        );
                        modal.find('.modal-body').load('/eunomia/projects/muestraComentarios/{{ $project->id }}');
                        modal.modal('show');
                        modal.on('hidden.bs.modal', function() {
                            modal.remove();
                        });
                    });
                @endif

                @if ($project->tasks->count() > 0)
                    $('#task_{{ $project->id }}').click(function() {
                        var modal = $(
                            '<div class="modal fade" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header bg-primary"><h5 class="modal-title text-white">Tareas proyecto "{{ $project->titulo_proyecto }}"</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button></div></div></div></div>'
                        );
                        modal.find('.modal-body').load('/eunomia/projects/muestraTareasProyecto/{{ $project->id }}');
                        modal.modal('show');
                        modal.on('hidden.bs.modal', function() {
                            modal.remove();
                        });
                    });
                @endif
            @endforeach
        </script>
    @stop
