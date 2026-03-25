@extends('adminlte::page')

@section('content_header')
    <h1>
        Insertar
        <small>Dominio</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="/admin"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dominios</li>
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
                <form action="{{ route('dominios.store') }}" method="POST" enctype="multipart/form-data" name="form_dominios">
                    @csrf

                <div class="box-body">
                    <div class="form-group">
                        <label for="customer_id">Cliente</label>
                        <select name="customer_id" id="customer_id" class="form-control">
                            <option value="">Elija un cliente...</option>
                            @foreach($customers as $key => $value)
                                <option value="{{ $key }}" {{ old('customer_id') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="dominio">Dominio</label>
                        <input type="text" name="dominio" id="dominio" class="form-control" placeholder="Dominio" value="{{ old('dominio') }}">
                    </div>

                    <div class="form-group">
                        <label for="fecha_contratacion">Fecha contratación</label>
                        <input type="date" name="fecha_contratacion" id="fecha_contratacion" class="form-control" value="{{ old('fecha_contratacion') }}">
                    </div>

                    <div class="form-group">
                        <label for="fecha_renovacion">Fecha renovación</label>
                        <input type="date" name="fecha_renovacion" id="fecha_renovacion" class="form-control" value="{{ old('fecha_renovacion') }}">
                    </div>

                    <div class="form-group">
                        <label for="agente_dominio_id">Agente</label>
                        <select name="agente_dominio_id" id="agente_dominio_id" class="form-control">
                            <option value="">Elija un agente...</option>
                            @foreach($agentes_dominios as $key => $value)
                                <option value="{{ $key }}" {{ old('agente_dominio_id') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="precio_anual">Precio anual</label>
                        <input type="text" name="precio_anual" id="precio_anual" class="form-control" placeholder="Precio anual" value="{{ old('precio_anual') }}">
                    </div>

                    <div class="form-group">
                        <label for="hosting">Hosting</label>
                        <input type="checkbox" name="hosting" id="hosting" value="1" class="flat-green" {{ old('hosting') ? 'checked' : '' }}>
                    </div>

                    <div class="form-group">
                        <label for="precio_hosting">Precio hosting</label>
                        <input type="text" name="precio_hosting" id="precio_hosting" class="form-control" placeholder="Precio hosting" value="{{ old('precio_hosting') }}">
                    </div>
                </div>
                <!-- /.box-body -->

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Insertar</button>
                </div>

                <input type="hidden" name="url" value="{{ $_SERVER['HTTP_REFERER'] ?? '' }}">

                </form>

            </div>
            <!-- /.box -->
        </div>
    </div>


@endsection

@section('css')

    <!-- General -->
    <link rel="stylesheet" href="{{asset('vendor/adminlte/css/general.css')}}">

    <!-- iCheck -->
    <link rel="stylesheet" href="{{asset('vendor/adminlte/plugins/iCheck/flat/green.css')}}">

@stop

@section('js')
    <!-- iCheck -->
    <script src="{{asset('vendor/adminlte/plugins/iCheck/icheck.min.js')}}"></script>
    <script>
        //Green color scheme for iCheck
        $('input[type="checkbox"].flat-green').iCheck({
            checkboxClass: 'icheckbox_flat-green'
        });
    </script>
@stop
