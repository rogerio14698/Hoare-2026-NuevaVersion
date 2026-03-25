@extends('adminlte::page')

@section('content_header')
    <div class="alinearHeader">
        <h1 class="fuenteTitulo">Editar Proyecto: {{ $project->nombre_proyecto }}</h1>
    </div>
@stop

@section('content')



    <div class="editar-proyectoVista">
        <div class="project-top">
            @include('eunomia.includes.user_project_box')
        </div>

        <div class="two-columns">
            <div class="left-column">
                @if (\Auth::user()->compruebaSeguridad('crear-tarea') == true)
                    <div class="create-task-wrap">
                        <a href="{{ route('create_WhithProject', $project->id) }}" 
                          class="btn-nueva-tarea">Nueva tarea del proyecto</a>
                    </div>
                @endif

                <!-- Inicio del formulario de edición de proyecto (columna izquierda) -->
                @include('eunomia.includes.projectsEdicion.formulario_editar_proyecto')
            </div>

            <div class="right-column">
                <!-- Listado de tareas del proyecto (columna derecha) -->
                <div class="tasks-container">
                    @include('eunomia.includes.projectsEdicion.listado_tarea_del_proyecto')
                </div>

                <!-- Comentarios del proyecto (debajo de la tabla de tareas) -->
                <div class="comments-container">
                    @include('eunomia.comments.list_comments')
                    @include('eunomia.comments.form_ins_comments')
                </div>
            </div>
        </div>
    </div>
@endsection



@section('js')
    {{-- Plugins --}}
    <script src="{{ asset('vendor/adminlte/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datepicker/locales/bootstrap-datepicker.es.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/tinymce/tinymce_plugin.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/js/bootstrap-dialog.min.js"></script>

    <script>
        $(function() {
            // 1. Datepicker
            $('#fechaentrega_proyecto').datepicker({
                autoclose: true,
                todayHighlight: true,
                weekStart: 1,
                language: 'es',
                format: 'yyyy-mm-dd'
            });

            // Comentarios: los handlers de insert/delete/edit están centralizados
            // en form_ins_comments.blade.php con eventos delegados.
        });
    </script>
@endsection
