@extends('adminlte::page')

@section('content_header')
    <div class="alinearHeader">
        <h1>Editar Tarea: {{ $task->titulo_tarea }}</h1>
        <form action="{{ route('tasks.destroy', $task) }}" method="POST" style="display:inline" id="elimina_task">
            @csrf
            @method('DELETE')
            <input type="hidden" name="previous" value="{{ URL::previous() }}">
            <button type="submit" id="btn-elimina-task" class="btn-modificar">Eliminar tarea: {{ $task->titulo_tarea }}</button>
        </form>
    </div>

@stop

@section('content')

    <div class="editar-proyectoVista">
        <div class="project-top">
            @include('eunomia.includes.user_task_box')
        </div>

        <div class="two-columns">
            <div class="left-column">
                <!-- Formulario de edición de tarea (omitido aquí a petición). -->
                <!-- Si quieres recuperar el formulario, descomenta la siguiente línea: -->
                @include('eunomia.includes.projectsEdicion.formulario_editar_tarea')
            </div>

            <div class="right-column">
                <!-- Listado de tareas del proyecto (columna derecha) -->
                <div class="tasks-container">
                    @include('eunomia.includes.projectsEdicion.listado_tarea_del_proyecto', [
                        'project' => $task->project,
                    ])
                </div>

                <!-- Comentarios del tarea (debajo de la tabla de tareas) -->
                <div class="comments-container">
                    @include('eunomia.comments.list_comments')
                    @include('eunomia.comments.form_ins_comments')
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')

    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/select2/select2.min.css') }}">


    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datepicker/datepicker3.css') }}">

    <!-- Bootstrap time Picker -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/timepicker/bootstrap-timepicker.min.css') }}">

    <style>
        /* Hacer que Select2 múltiple sea idéntico a un select normal de Bootstrap 5 */
        .select2-container--bootstrap5 .select2-selection--multiple {
            display: block;
            width: 100%;
            height: calc(1.5em + 0.75rem + 2px);
            padding: 0.375rem 2.25rem 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            min-height: calc(1.5em + 0.75rem + 2px);
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            appearance: none;
        }

        .select2-container--bootstrap5.select2-container--focus .select2-selection--multiple {
            color: #495057;
            background-color: #fff;
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .select2-container--bootstrap5 .select2-selection--multiple .select2-selection__rendered {
            display: block;
            padding: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .select2-container--bootstrap5 .select2-selection--multiple .select2-search--inline {
            display: block;
            width: 100%;
        }

        .select2-container--bootstrap5 .select2-selection--multiple .select2-search--inline .select2-search__field {
            width: 100% !important;
            padding: 0;
            margin: 0;
            border: none;
            background: transparent;
        }

        .select2-container--bootstrap5 .select2-selection--multiple .select2-search--inline .select2-search__field::placeholder {
            color: #6c757d;
        }

        .select2-container--bootstrap5 .select2-selection--multiple .select2-selection__choice {
            background-color: #0d6efd;
            border: 1px solid #0d6efd;
            border-radius: 0.25rem;
            color: #fff;
            cursor: default;
            float: left;
            margin-right: 5px;
            margin-top: 0;
            padding: 0 5px;
            display: inline-block;
            font-size: 0.875rem;
        }

        .select2-container--bootstrap5 .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff;
            cursor: pointer;
            display: inline-block;
            font-weight: bold;
            margin-right: 2px;
        }

        .select2-container--bootstrap5 .select2-results__option--highlighted {
            background-color: #0d6efd;
            color: white;
        }

        .select2-container--bootstrap5 .select2-results>.select2-results__options {
            max-height: 300px;
            overflow-y: auto;
        }
    </style>

    <style>
        /* Scroll para la lista de comentarios dentro del panel de comentarios */
        .comments-container {
            display: flex;
            flex-direction: column;
        }

        .comments-container #comentarios {
            max-height: 360px;
            /* ajustar según necesidad */
            overflow-y: auto;
            padding-right: 8px;
            /* espacio para la barra de scroll */
            -webkit-overflow-scrolling: touch;
        }

        /* Opcional: mantener el formulario de inserción fijo en la parte inferior */
        .comments-container .form-ins-comments {
            margin-top: 12px;
            flex: 0 0 auto;
        }
    </style>

@stop

@section('js')

    <!-- Select2 -->
    <script src="{{ asset('vendor/adminlte/plugins/select2/select2.full.min.js') }}"></script>

    <!-- bootstrap datepicker -->
    <script src="{{ asset('vendor/adminlte/plugins/datepicker/bootstrap-datepicker.js') }}"></script>

    <!-- bootstrap time picker -->
    <script src="{{ asset('vendor/adminlte/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>

    <!-- Languaje -->
    <script src="{{ asset('vendor/adminlte/plugins/datepicker/locales/bootstrap-datepicker.es.js') }}"></script>

    <!-- TinyMCE -->
    <script src="{{ asset('vendor/adminlte/plugins/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/tinymce/tinymce_plugin.js') }}"></script>

    <script type="text/javascript">
        $(function() {
            // Initialize Select2 con tema Bootstrap 5
            $(".select2").select2({
                theme: 'bootstrap5',
                width: '100%',
                placeholder: 'selecciona los responsables',
                allowClear: false,
                language: {
                    noResults: function() {
                        return "No se encontraron resultados";
                    }
                }
            });

            $('.select2-search__field').attr('placeholder', 'selecciona los responsables');

            // Date picker
            $('#fechaentrega_tarea, #fechainicio_tarea').datepicker({
                autoclose: true,
                todayHighlight: true,
                weekStart: 1,
                language: 'es',
                format: "yyyy-mm-dd"
            });

            // Timepicker
            $('#horanicio_tarea, #horaentrega_tarea').timepicker({
                showMeridian: false,
                showSeconds: false
            });

            // Comentarios: los handlers de insert/delete/edit están centralizados
            // en form_ins_comments.blade.php con eventos delegados.
        });
    </script>

    <!-- Bootstrap Dialog -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/js/bootstrap-dialog.min.js"></script>

    <script language="JavaScript">
        $(function() {
            // Manejar la confirmación sobre el envío del formulario de eliminación
            $(document).on('submit', '#elimina_task', function(e) {
                e.preventDefault();
                var $form = $(this);

                var doSubmit = function() {
                    if ($form.length) {
                        try {
                            $form.off('submit');
                            $form.submit();
                            return;
                        } catch (err) {
                            console.error('Form submit failed:', err);
                        }
                    }

                    // Fallback AJAX DELETE
                    var url = $form.attr('action');
                    var token = $form.find('input[name="_token"]').val();
                    if (url && token) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: token
                            },
                            success: function(resp) {
                                window.location = $form.find('input[name="previous"]')
                                    .first().val() || window.location.href.replace(
                                        /\/edit$/, '');
                            },
                            error: function(jqXHR) {
                                console.error('AJAX delete failed', jqXHR.responseText);
                                alert('Error al eliminar (ver consola)');
                            }
                        });
                    } else {
                        console.warn('No action URL or CSRF token found for delete form');
                    }
                };

                var confirmedViaDialog = false;
                if (typeof BootstrapDialog !== 'undefined' && BootstrapDialog && typeof BootstrapDialog
                    .confirm === 'function') {
                    try {
                        BootstrapDialog.confirm('¿Está seguro que desea eliminar el registro?', function(
                            result) {
                            if (result) {
                                doSubmit();
                            }
                        });
                        confirmedViaDialog = true;
                    } catch (e) {
                        console.warn('BootstrapDialog.confirm failed, falling back to native confirm', e);
                    }
                }

                if (!confirmedViaDialog) {
                    if (confirm('¿Está seguro que desea eliminar el registro?')) {
                        doSubmit();
                    }
                }
            });
        });
    </script>
@stop
