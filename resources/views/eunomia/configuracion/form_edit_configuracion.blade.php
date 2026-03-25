@extends('adminlte::page')

@section('content_header')
    <h1>
        Editar
        <small>Configuración</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('eunomia.home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Configuración</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <!-- general form elements -->
            <div class="box box-default">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            <!-- /.box-header -->
                <!-- form start -->
                    <form action="{{ route('configuracion.update', $configuracion) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')



                <div class="box-body">
                    <div class="form-group">
                        <label for="nombre_empresa">Nombre empresa</label>
                        <input type="text" name="nombre_empresa" id="nombre_empresa" class="form-control" placeholder="Nombre empresa" value="{{ old('nombre_empresa', $configuracion->nombre_empresa) }}">
                    </div>
                    <div class="form-group">
                        <label for="nif_cif">NIF/CIF empresa</label>
                        <input type="text" name="nif_cif" id="nif_cif" class="form-control" placeholder="NIF/CIF empresa" value="{{ old('nif_cif', $configuracion->nif_cif) }}">
                    </div>
                    <div class="form-group">
                        <label for="direccion_empresa">Dirección empresa</label>
                        <input type="text" name="direccion_empresa" id="direccion_empresa" class="form-control" placeholder="Dirección empresa" value="{{ old('direccion_empresa', $configuracion->direccion_empresa) }}">
                    </div>
                    <div class="form-group">
                        <label for="telefono_empresa">Teléfono empresa</label>
                        <input type="text" name="telefono_empresa" id="telefono_empresa" class="form-control" placeholder="Teléfono empresa" value="{{ old('telefono_empresa', $configuracion->telefono_empresa) }}">
                    </div>
                    <div class="form-group">
                        <label for="movil_empresa">Móvil empresa</label>
                        <input type="text" name="movil_empresa" id="movil_empresa" class="form-control" placeholder="Móvil empresa" value="{{ old('movil_empresa', $configuracion->movil_empresa) }}">
                    </div>
                    <div class="form-group">
                        <label for="email">Email empresa</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Email empresa" value="{{ old('email', $configuracion->email) }}">
                    </div>
                    <div class="form-group">
                        <label for="url">URL web</label>
                        <input type="text" name="url" id="url" class="form-control" placeholder="URL web" value="{{ old('url', $configuracion->url) }}">
                    </div>
                    <div class="form-group">
                        <label for="g_analytics">Google Analytics</label>
                        <input type="text" name="g_analytics" id="g_analytics" class="form-control" placeholder="Google Analytics" value="{{ old('g_analytics', $configuracion->g_analytics) }}">
                    </div>
                    <div class="form-group">
                        <label for="facebook">Facebook</label>
                        <input type="text" name="facebook" id="facebook" class="form-control" placeholder="Facebook" value="{{ old('facebook', $configuracion->facebook) }}">
                    </div>
                    <div class="form-group">
                        <label for="twitter">Twitter</label>
                        <input type="text" name="twitter" id="twitter" class="form-control" placeholder="Twitter" value="{{ old('twitter', $configuracion->twitter) }}">
                    </div>
                    <div class="form-group">
                        <label for="instagram">Instagram</label>
                        <input type="text" name="instagram" id="instagram" class="form-control" placeholder="Instagram" value="{{ old('instagram', $configuracion->instagram) }}">
                    </div>
                    <div class="form-group">
                        <label for="google_plus">Google Plus</label>
                        <input type="text" name="google_plus" id="google_plus" class="form-control" placeholder="Google Plus" value="{{ old('google_plus', $configuracion->google_plus) }}">
                    </div>
                    <div class="form-group">
                        <label for="youtube">Youtube</label>
                        <input type="text" name="youtube" id="youtube" class="form-control" placeholder="Youtube" value="{{ old('youtube', $configuracion->youtube) }}">
                    </div>
                </div>
                <!-- /.box-body -->

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Editar</button>
                </div>
                    </form>

            </div>
            <!-- /.box -->
        </div>
    </div>


@endsection

@section('css')
  {{-- General --}}
  <link rel="stylesheet" href="{{ asset('vendor/adminlte/css/general.css') }}">

  {{-- iCheck --}}
  <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/iCheck/flat/green.css') }}">

  {{-- Bootstrap Datepicker (CDN) --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css" referrerpolicy="no-referrer" />

  {{-- jQuery Timepicker (CDN) --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css" referrerpolicy="no-referrer" />
@stop

@section('js')
  {{-- iCheck --}}
  <script src="{{ asset('vendor/adminlte/plugins/iCheck/icheck.min.js') }}"></script>
  <script>
    $(function(){
      $('input[type="checkbox"].flat-green').iCheck({ checkboxClass:'icheckbox_flat-green' });
    });
  </script>

  {{-- Bootstrap Datepicker (CDN) --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.es.min.js" referrerpolicy="no-referrer"></script>

  {{-- jQuery Timepicker (CDN) --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js" referrerpolicy="no-referrer"></script>

  {{-- Select2 --}}
  <script src="{{ asset('vendor/adminlte/plugins/select2/select2.full.min.js') }}"></script>

  {{-- Inicializaciones --}}
  <script>
    $(function () {
      // Datepicker (clase .datepicker en tus inputs)
      $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        language: 'es',
        autoclose: true,
        // si no necesitas acotar fechas, quita estas dos líneas:
        // startDate: '02/12/2017',
        // endDate: '04/12/2017',
        todayHighlight: true
      });

      // Timepicker (id #hora o la clase que uses)
      $('#hora').timepicker({
        timeFormat: 'H:i',
        step: 15,
        // minTime: '08:00',
        // maxTime: '23:30',
        scrollDefault: 'now'
      });

      // Select2
      $('.select2').select2();
    });
  </script>
@stop

