@extends('adminlte::page')

@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Gestión de Menú</h1>
    @include('eunomia.includes.configuracionAdmin.configNav')
    @if(\Auth::user()->compruebaSeguridad('crear-elemento-menu-admin') == true)
      <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#newModal">
        <i class="fas fa-plus"></i> Nuevo
      </button>
    @endif
  </div>
@stop

@section('content')
  <div class="row">
    <div class="col-md-8">
      <div class="card card-primary card-outline">
        <div class="card-header">
          <h3 class="card-title">Elementos del menú</h3>
        </div>
        <div class="card-body">
          <div class="dd" id="nestable">
            <?php echo $menu ?>
            {{ csrf_field() }}
          </div>

          <p id="success-indicator" style="display:none; margin-top: 10px;">
            <i class="fas fa-check text-success"></i> El orden del menú ha sido actualizado
          </p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-info card-outline">
        <div class="card-header">
          <h3 class="card-title">Instrucciones</h3>
        </div>
        <div class="card-body">
          <p>Arrastre elementos para moverlos en un orden diferente</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Create new item Modal -->
  <div class="modal fade" id="newModal" tabindex="-1" role="dialog" aria-labelledby="newModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form action="{{ url('eunomia/menu_admin/new') }}" method="POST" role="form">
          @csrf
        <div class="modal-header bg-primary">
          <h5 class="modal-title text-white" id="newModalLabel">Nuevo elemento de menú</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group row">
            <label for="title" class="col-sm-3 col-form-label">Título</label>
            <div class="col-sm-9">
              <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}">
            </div>
          </div>

          <div class="form-group row">
            <label for="label" class="col-sm-3 col-form-label">Etiqueta</label>
            <div class="col-sm-9">
              <input type="text" name="label" id="label" class="form-control" value="{{ old('label') }}">
            </div>
          </div>

          <div class="form-group row">
            <label for="label_color" class="col-sm-3 col-form-label">Color del label</label>
            <div class="col-sm-9">
              <select name="label_color" class="form-control" id="label_color">
                <option value="">Seleccione un color</option>
                <option value="default">Blanco</option>
                <option value="primary">Azul marino</option>
                <option value="success">Verde</option>
                <option value="info">Azul claro</option>
                <option value="warning">Naranja</option>
                <option value="danger">Rojo</option>
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label for="icon" class="col-sm-3 col-form-label">Icono</label>
            <div class="col-sm-9">
              @php
                $iconsList = isset($icons['icons']) && is_array($icons['icons']) ? $icons['icons'] : (is_array($icons) ? $icons : []);
              @endphp
              <select name="icon" class="form-control" id="icon" style="font-family: 'FontAwesome';">
                <option value="">Seleccione un icono</option>
                @foreach ($iconsList as $icon)
                  @php
                    $id = is_array($icon) ? ($icon['id'] ?? '') : (string) $icon;
                    $unicode = is_array($icon) ? ($icon['unicode'] ?? '') : '';
                  @endphp
                  @if($id !== '')
                    <option value="{{ $id }}">&#x{{ $unicode }}; fa-{{ $id }}</option>
                  @endif
                @endforeach
              </select>
            </div>
          </div>


          <div class="form-group row">
            <label for="url" class="col-sm-3 col-form-label">URL</label>
            <div class="col-sm-9">
              <input type="text" name="url" id="url" class="form-control" value="{{ old('url') }}">
            </div>
          </div>

          <div class="form-group row">
            <label for="modulo_id" class="col-sm-3 col-form-label">Módulo</label>
            <div class="col-sm-9">
              <select name="modulo_id" id="modulo_id" class="form-control">
                <option value="">selecciona un módulo</option>
                @foreach($modulos as $key => $modulo)
                  <option value="{{ $key }}" {{ old('modulo_id') == $key ? 'selected' : '' }}>{{ $modulo }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label for="table" class="col-sm-3 col-form-label">Contador (tabla)</label>
            <div class="col-sm-9">
              <select name="table" class="form-control" id="table">
                <option value="">Seleccione un tabla</option>
                @foreach($tables as $table)
                  @php
                    $tableValues = array_values((array)$table);
                    $tableName = $tableValues[0] ?? '';
                  @endphp
                  @if($tableName)
                    <option value="{{$tableName}}">{{$tableName}}</option>
                  @endif
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">Separador</label>
            <div class="col-sm-9">
              <div class="form-check">
                <input type="checkbox" name="separator" id="separator" class="form-check-input" value="1">
                <label class="form-check-label" for="separator">Mostrar como separador</label>
              </div>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">Visible/Oculto</label>
            <div class="col-sm-9">
              <div class="form-check">
                <input type="checkbox" name="visible" id="visible" class="form-check-input" value="1" checked>
                <label class="form-check-label" for="visible">Visible en el menú</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Crear
          </button>
        </div>
        </form>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

  <!-- Delete item Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="{{ url('eunomia/menu_admin/delete') }}" method="POST">
          @csrf
        <div class="modal-header bg-danger">
          <h5 class="modal-title text-white" id="deleteModalLabel">Eliminar elemento</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>¿Está seguro de que desea eliminar este elemento del menú?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <input type="hidden" name="delete_id" id="postvalue" value="" />
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash"></i> Eliminar elemento
          </button>
        </div>
        </form>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
@endsection

@section('css')
<!-- Nestable -->
<link rel="stylesheet" href="{{asset("vendor/nestable/nestable.css")}}">

<!-- iCheck -->
<link rel="stylesheet" href="{{asset('vendor/adminlte/plugins/iCheck/flat/green.css')}}">

@stop

@section('js')
@if(\Auth::user()->compruebaSeguridad('editar-elemento-menu-admin') == true)
  <!-- Nestable -->
  <script src="{{asset("vendor/nestable/jquery.nestable.js")}}"> </script>
  <script type="text/javascript">
    $(function () {
      $('.dd').nestable({
        dropCallback: function (details) {

          var order = new Array();
          $("li[data-id='" + details.destId + "']").find('ol:first').children().each(function (index, elem) {
            order[index] = $(elem).attr('data-id');
          });

          if (order.length === 0) {
            var rootOrder = new Array();
            $("#nestable > ol > li").each(function (index, elem) {
              rootOrder[index] = $(elem).attr('data-id');
            });
          }

          $.post('{{url("eunomia/menu_admin/")}}',
            {
              source: details.sourceId,
              destination: details.destId,
              order: JSON.stringify(order),
              rootOrder: JSON.stringify(rootOrder),
              _token: $("input[name='_token']").val() // Token generado en el campo de arriba para los formularios de Laravel (CSRF Protection)
            }, function (data) {
              console.log('data ' + data);
            }).done(function () {
              $("#success-indicator").fadeIn(100).delay(1000).fadeOut();
            }).fail(function (data) { console.log('data ' + data.responseText); }).always(function () { });
        }
      });

      $('.delete_toggle').each(function (index, elem) {
        $(elem).click(function (e) {
          e.preventDefault();
          $('#postvalue').attr('value', $(elem).attr('rel'));
          $('#deleteModal').modal('toggle');
        });
      });
    });
  </script>
@endif

<!-- iCheck -->
<script src="{{asset('vendor/adminlte/plugins/iCheck/icheck.min.js')}}"></script>
<script type="text/javascript">
  //Green color scheme for iCheck
  $('input[type="checkbox"].flat-green').iCheck({
    checkboxClass: 'icheckbox_flat-green'
  });
</script>

@stop