@extends('adminlte::page')

@section('content_header')
    <div class="alinearHeader">
        <h1 class="fuenteTitulo">Editar día festivo</h1>
        <a href="{{ route('party_days.index') }}" class="btn-modificar">Volver</a>
    </div>
@stop

@section('content')
    <form action="{{ route('party_days.update', $partyDay->id) }}" method="POST" id="form_edit_party_day">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Datos del día festivo</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="name" class="col-sm-3 col-form-label">Nombre</label>
                            <div class="col-sm-9">
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Nombre del festivo" value="{{ old('name', $partyDay->name) }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="date_type" class="col-sm-3 col-form-label">Tipo de fiesta</label>
                            <div class="col-sm-9">
                                <select name="date_type" id="date_type" class="form-control">
                                    <option value="">Selecciona el tipo</option>
                                    @foreach ($date_types as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ old('date_type', $partyDay->date_type) == $key ? 'selected' : '' }}>
                                            {{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="date" class="col-sm-3 col-form-label">Fecha</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    </div>
                                    <input type="text" name="date" id="date" class="form-control"
                                        placeholder="YYYY-MM-DD" value="{{ old('date', $partyDay->date) }}">
                                </div>
                            </div>
                            <button type="submit" class="btn-modificar m-2"> Guardar cambios</button>
                            <a href="{{ route('party_days.index') }}" class="btn-modificar m-2">Cancelar</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datepicker/datepicker3.css') }}">
@stop

@section('js')
    <script src="{{ asset('vendor/adminlte/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datepicker/locales/bootstrap-datepicker.es.js') }}"></script>
    <script>
        $('#date').datepicker({
            autoclose: true,
            todayHighlight: true,
            weekStart: 1,
            language: 'es',
            format: "yyyy-mm-dd",
        });
    </script>
@stop
