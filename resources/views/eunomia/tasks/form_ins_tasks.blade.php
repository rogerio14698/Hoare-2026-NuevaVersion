@extends('adminlte::page')

@section('content_header')
  <div class="alinearHeader">
    <h1 class="fuenteTitulo">Nueva Tarea</h1>
    <a href="{{ route('tasks.index') }}" class="btn-modificar"> Volver</a>
  </div>
@stop

@section('content')
<div class="alinearCentro">
          @if (count($errors) > 0)
            <div class="alert alert-danger alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true" id="close-alert">×</button>
                  <h4><i class="icon fas fa-ban"></i> Errores de validación</h4>
                  <ul class="mb-0">
                      @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
                </div>
          @endif

          <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="project-form-container">
              <div class="form-header">
                <h2 class="fuenteTituloBlanco">Crear Nueva Tarea</h2>
              </div>

              <div class="form-body">
                
                {{-- Cliente --}}
                <div class="form-group full-width">
                  <label for="customer_id" class="form-label">Cliente</label>
                  @if(!isset($customer))
                    <div class="form-field-with-button">
                      <select name="customer_id" class="form-control form-field-flexible" id="customer_id">
                        <option value="">Selecciona un cliente</option>
                        @foreach($customers as $id => $customerName)
                          <option value="{{ $id }}" {{ old('customer_id') == $id ? 'selected' : '' }}>{{ $customerName }}</option>
                        @endforeach
                      </select>
                      <button type="button" id="aniade_cliente" class="btn-accion form-field-button">Añadir</button>
                    </div>
                  @else
                    <input type="text" class="form-control" id="customer_display" value="{{ $customer->codigo_cliente . '_' . $customer->nombre_cliente }}" disabled>
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                  @endif
                </div>

                {{-- Proyecto --}}
                <div class="form-group full-width">
                  <label for="project_id" class="form-label">Proyecto</label>
                  @if(!isset($proyecto))
                    <div class="form-field-with-button">
                      <select name="project_id" class="form-control form-field-flexible" id="project_id" required>
                        <option value="">Selecciona un proyecto</option>
                        @foreach($projects as $id => $projectName)
                          <option value="{{ $id }}" {{ old('project_id') == $id ? 'selected' : '' }}>{{ $projectName }}</option>
                        @endforeach
                      </select>
                      <button type="button" id="aniade_proyecto" class="btn-accion form-field-button">Añadir</button>
                    </div>
                  @else
                    <input type="text" class="form-control" value="{{ $proyecto->customer->codigo_cliente . '_' . $proyecto->titulo_proyecto }}" disabled>
                    <input type="hidden" name="project_id" value="{{ $proyecto->id }}">
                  @endif
                </div>

                {{-- Responsables --}}
                <div class="form-group full-width">
                  <label for="user_id" class="form-label">Responsable en mg.lab</label>
                  <select name="user_id[]" class="form-control select2" id="user_id" multiple data-placeholder="Selecciona los responsables">
                    @foreach($users as $id => $user)
                      <option value="{{ $id }}" {{ (collect(old('user_id'))->contains($id)) ? 'selected' : '' }}>{{ $user }}</option>
                    @endforeach
                  </select>
                </div>

                {{-- Nombre de tarea --}}
                <div class="form-group full-width">
                  <label for="titulo_tarea" class="form-label">Nombre de tarea</label>
                  <input type="text" name="titulo_tarea" class="form-control" placeholder="Nombre de tarea" id="titulo_tarea" value="{{ old('titulo_tarea') }}">
                </div>

                {{-- Fecha de inicio --}}
                <div class="form-group">
                  <label for="fechainicio_tarea" class="form-label">Fecha de inicio</label>
                  <input type="text" name="fechainicio_tarea" class="form-control" id="fechainicio_tarea" placeholder="DD/MM/YYYY" value="{{ old('fechainicio_tarea') }}">
                </div>

                {{-- Hora de inicio --}}
                <div class="form-group">
                  <label for="horanicio_tarea" class="form-label">Hora de inicio</label>
                  <input type="text" name="horanicio_tarea" class="form-control" id="horanicio_tarea" placeholder="HH:MM" value="{{ old('horanicio_tarea') }}">
                </div>

                {{-- Fecha de entrega --}}
                <div class="form-group">
                  <label for="fechaentrega_tarea" class="form-label">Fecha de entrega</label>
                  <input type="text" name="fechaentrega_tarea" class="form-control" id="fechaentrega_tarea" required placeholder="YYYY-MM-DD" value="{{ old('fechaentrega_tarea') }}">
                </div>

                {{-- Hora de entrega --}}
                <div class="form-group">
                  <label for="horaentrega_tarea" class="form-label">Hora de entrega</label>
                  <input type="text" name="horaentrega_tarea" class="form-control" id="horaentrega_tarea" placeholder="HH:MM" value="{{ old('horaentrega_tarea') }}">
                </div>

                {{-- Estado de tarea --}}
                <div class="form-group">
                  <label for="estado_tarea" class="form-label">Estado de tarea</label>
                  <select name="estado_tarea" class="form-control" id="estado_tarea">
                    @foreach($task_states as $id => $state)
                      <option value="{{ $id }}" {{ (old('estado_tarea', 6) == $id) ? 'selected' : '' }}>{{ $state }}</option>
                    @endforeach
                  </select>
                </div>

                {{-- Comentarios --}}
                <div class="form-group full-width">
                  <label for="comentario_tarea" class="form-label">Comentarios</label>
                  <textarea name="comentario_tarea" class="form-control" placeholder="Comentarios sobre la tarea" id="comentario_tarea" rows="4">{{ old('comentario_tarea') }}</textarea>
                </div>

                <input type="hidden" name="role_id" id="role_id" value="{{ Auth::user()->role_id }}">
                <input type="hidden" name="previous" id="previous_url" value="{{ URL::previous() }}">

                {{-- Botones de acción --}}
                <div class="form-actions full-width">
                  <button type="submit" id="btn-submit" class="btn-accion">Guardar</button>
                  <a href="{{ route('tasks.index') }}" class="btn-accion">Cancelar</a>
                </div>

              </div>
            </div>

            </form>

      </div>


@endsection

@section('css')

  <!-- Select2 -->
  <link rel="stylesheet" href="{{asset('vendor/adminlte/plugins/select2/select2.min.css')}}">

  

  <!-- bootstrap datepicker -->
  <link rel="stylesheet" href="{{asset('vendor/adminlte/plugins/datepicker/datepicker3.css')}}">

  <!-- Bootstrap time Picker -->
  <link rel="stylesheet" href="{{asset('vendor/adminlte/plugins/timepicker/bootstrap-timepicker.min.css')}}">

  <style>
    /* Hacer que Select2 múltiple sea idéntico a un select normal de Bootstrap */
    .select2-container--bootstrap4 .select2-selection--multiple {
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
    
    .select2-container--bootstrap4.select2-container--focus .select2-selection--multiple {
      color: #495057;
      background-color: #fff;
      border-color: #80bdff;
      outline: 0;
      box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__rendered {
      display: block;
      padding: 0;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    
    .select2-container--bootstrap4 .select2-selection--multiple .select2-search--inline {
      display: block;
      width: 100%;
    }
    
    .select2-container--bootstrap4 .select2-selection--multiple .select2-search--inline .select2-search__field {
      width: 100% !important;
      padding: 0;
      margin: 0;
      border: none;
      background: transparent;
    }
    
    .select2-container--bootstrap4 .select2-selection--multiple .select2-search--inline .select2-search__field::placeholder {
      color: #6c757d;
    }
    
    .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
      background-color: #007bff;
      border: 1px solid #007bff;
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
    
    .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
      color: #fff;
      cursor: pointer;
      display: inline-block;
      font-weight: bold;
      margin-right: 2px;
    }
    
    .select2-container--bootstrap4 .select2-results__option--highlighted {
      background-color: #007bff;
      color: white;
    }
    
    .select2-container--bootstrap4 .select2-results > .select2-results__options {
      max-height: 300px;
      overflow-y: auto;
    }
  </style>

@stop

@section('js')
    {{-- Plugins específicos de esta vista --}}
    <script src="{{ asset('vendor/jsvalidation/js/jsvalidation.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datepicker/locales/bootstrap-datepicker.es.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/tinymce/tinymce_plugin.js') }}"></script>

    <script type="text/javascript">
    $(function() {
        // Inicializar Select2 con tema Bootstrap 4
        $(".select2").select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Selecciona los responsables',
            allowClear: false,
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                }
            }
        });
        
        // Establecer placeholder manualmente en el input de búsqueda
        $('.select2-search__field').attr('placeholder', 'Selecciona los responsables');

        // Inicializar Datepickers
        $('#fechainicio_tarea, #fechaentrega_tarea').datepicker({
            autoclose: true,
            todayHighlight: true,
            weekStart: 1,
            language: 'es',
            format: "yyyy-mm-dd"
        });

        // Inicializar Timepickers
        $('#horanicio_tarea, #horaentrega_tarea').timepicker({
            showMeridian: false,
            showSeconds: false
        });

        // TinyMCE initialization
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: 'textarea',
                height: 200,
                theme: 'modern'
            });
        }

        // Inicializar estado de proyecto
        @if(!isset($proyecto))
        // Dejar el proyecto vacío pero habilitado
        $('#project_id').val('');
        @endif
        
        // Deshabilitar botón de añadir proyecto inicialmente
        $('#aniade_proyecto').prop('disabled', true);

        // Gestionar evento de selección de cliente
        $('#customer_id').on('change', function(){
            if ($(this).val() > 0){
                $('#aniade_proyecto').prop('disabled', false);
                // Cargar proyectos del cliente seleccionado
                $.get('/eunomia/add_projects/' + $('#customer_id').val(), function(res) {
                    $('#project_id').empty();
                    $('#project_id').append('<option value="">Selecciona un proyecto</option>');
                    res.forEach(function(element) {
                        $('#project_id').append('<option value="' + element.id + '">' + element.codigo_proyecto + '</option>');
                    });
                });
            } else {
                $('#project_id').empty();
                $('#project_id').append('<option value="">Selecciona un proyecto</option>');
                $('#aniade_proyecto').prop('disabled', true);
            }
        });

        // Modal para añadir cliente
        $('#aniade_cliente').click(function(){
            const modal = $('<div class="modal fade" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header bg-primary"><h5 class="modal-title text-white">Nuevo Cliente</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button></div></div></div></div>');
            modal.find('.modal-body').load('/eunomia/customers/formularioClientes/');
            modal.modal('show');
        });

        // Modal para añadir proyecto
        $('#aniade_proyecto').click(function(){
            if (!$('#customer_id').val() || $('#customer_id').val() == '') {
                alert('Por favor, selecciona un cliente primero');
                return;
            }
            const modal = $('<div class="modal fade" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header bg-primary"><h5 class="modal-title text-white">Nuevo Proyecto</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button></div></div></div></div>');
            modal.find('.modal-body').load('/eunomia/projects/formularioProyectos/' + $('#customer_id').val(), function() {
                // Interceptar el submit del formulario para hacerlo vía AJAX
                modal.find('#formulario_proyectos').on('submit', function(e) {
                    e.preventDefault();
                    var formData = $(this).serialize();
                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST',
                        data: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        success: function(response) {
                            if (response.success) {
                                // Añadir el nuevo proyecto al select y seleccionarlo
                                $('#project_id').append(
                                    $('<option>', { value: response.id, text: response.codigo_proyecto }).prop('selected', true)
                                );
                                modal.modal('hide');
                                modal.remove();
                            }
                        },
                        error: function(xhr) {
                            var msg = xhr.responseJSON && xhr.responseJSON.message
                                ? xhr.responseJSON.message
                                : 'Error al guardar el proyecto';
                            alert(msg);
                        }
                    });
                });
            });
            modal.on('hidden.bs.modal', function() { modal.remove(); });
            modal.modal('show');
        });
    });
    </script>
@stop
