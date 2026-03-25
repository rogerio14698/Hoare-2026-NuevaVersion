@extends('adminlte::page')

@section('content_header')
    <div class="alinearHeader">
        <h1 class="fuenteTitulo">Nuevo Proyecto</h1>
        <a href="{{ route('projects.index') }}" class="btn-modificar"> Volver</a>
    </div>
@stop

@section('content')

        <div class="card-body alinearCentro">
            @include('eunomia.projects.formulario_proyectos')
        </div>

@endsection



@section('js')
    <!-- bootstrap datepicker -->
    <script src="{{ asset('vendor/adminlte/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datepicker/locales/bootstrap-datepicker.es.js') }}"></script>

    <!-- TinyMCE -->
    <script src="{{ asset('vendor/adminlte/plugins/tinymce/tinymce.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Date picker
            $('#fechaentrega_proyecto').datepicker({
                autoclose: true,
                todayHighlight: true,
                weekStart: 1,
                language: 'es',
                format: 'yyyy-mm-dd'
            });

            // Client add button handler
            $('#aniade_cliente').click(function() {
                // Crear modal Bootstrap 5
                var modalHtml = `
          <div class="modal fade" id="modal_add_customer" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content">
                <div class="modal-header bg-primary">
                  <h5 class="modal-title">Añadir Cliente</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-2x"></i> Cargando...
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
              </div>
            </div>
          </div>
        `;

                $('body').append(modalHtml);
                var modal = $('#modal_add_customer');

                // Cargar contenido
                modal.find('.modal-body').load('/eunomia/customers/formularioClientes');

                // Mostrar modal
                modal.modal('show');

                // Limpiar al cerrar
                modal.on('hidden.bs.modal', function() {
                    $(this).remove();
                });
            });

            // TinyMCE initialization
            if (typeof tinymce !== 'undefined') {
                tinymce.init({
                    selector: 'textarea',
                    height: 200,
                    menubar: false,
                    plugins: ['link', 'lists'],
                    toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link',
                    setup: function(editor) {
                        editor.on('change', function() {
                            editor.save();
                        });
                    }
                });
            }

            // Form submit - Prevenir dobles envíos
            $('#formulario_proyectos').find('button[type="submit"]').one('click', function() {
                var $btn = $(this);
                $btn.prop('disabled', true);
                $btn.html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

                // Asegurarse de que TinyMCE guarde su contenido
                if (typeof tinymce !== 'undefined') {
                    tinymce.triggerSave();
                }

                setTimeout(function() {
                    $('#formulario_proyectos')[0].submit();
                }, 100);
            });
        });
    </script>

    <!-- Laravel Javascript Validation -->
    <script src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
    {!! isset($validator) ? $validator : '' !!}
@stop
