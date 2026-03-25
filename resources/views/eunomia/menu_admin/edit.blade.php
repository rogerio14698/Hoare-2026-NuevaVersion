@extends('adminlte::page')

@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Editar elemento del menú</h1>
  </div>
@stop

@section('content')
<form action="{{ url('eunomia/menu_admin/edit/' . $item->id) }}" method="POST">
    @csrf
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Editar elemento del menú</h3>
                <div class="card-tools">
                    <a href="{{ url('eunomia/menu_admin')}}" class="btn btn-sm btn-primary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php
$title = $item->title;
$label = $item->label;
$label_color = $item->label_color;
$url = $item->url;
$icono = $item->icon;
$tabla = $item->table;
$separator = $item->separator;
$visible = $item->visible;
                ?>
                
                <div class="form-group">
                    <label for="title">Título</label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $item->title) }}">
                </div>

                <div class="form-group">
                    <label for="label">Etiqueta</label>
                        <input type="text" name="label" id="label" class="form-control" value="{{ old('label', $item->label) }}">
                </div>

                <div class="form-group">
                        <label for="label_color">Color del label</label>
                    <select name="label_color" class="form-control" id="label_color">
                        <option value="">Seleccione un color</option>
                        <option value="default" {{$label_color == 'default' ? ' selected="selected"' : ''}}>Blanco</option>
                        <option value="primary" {{$label_color == 'primary' ? ' selected="selected"' : ''}}>Azul marino</option>
                        <option value="success" {{$label_color == 'success' ? ' selected="selected"' : ''}}>Verde</option>
                        <option value="info" {{$label_color == 'info' ? ' selected="selected"' : ''}}>Azul claro</option>
                        <option value="warning" {{$label_color == 'warning' ? ' selected="selected"' : ''}}>Naranja</option>
                        <option value="danger" {{$label_color == 'danger' ? ' selected="selected"' : ''}}>Rojo</option>
                    </select>
                </div>

                <div class="form-group">
                        <label for="icon">Icono</label>
                    @php
                        $iconsList = isset($icons['icons']) && is_array($icons['icons']) ? $icons['icons'] : (is_array($icons) ? $icons : []);
                        // Asegura la variable usada para el "selected"
                        $icono = isset($icono) ? $icono : ($item->icon ?? '');
                    @endphp
                        <select name="icon" class="form-control" id="icon" style="font-family: 'FontAwesome';">
                        <option value="">Seleccione un icono</option>
                        @foreach ($iconsList as $icon)
                            @php
                                $id = is_array($icon) ? ($icon['id'] ?? '') : (string) $icon;
                                $unicode = is_array($icon) ? ($icon['unicode'] ?? '') : '';
                            @endphp
                            @if($id !== '')
                                <option value="{{ $id }}" {{ $id === $icono ? 'selected' : '' }}>
                                    &#x{{ $unicode }}; fa-{{ $id }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="url">URL</label>
                    <input type="text" name="url" id="url" class="form-control" value="{{ old('url', $item->url) }}">
                </div>

                <div class="form-group">
                    <label for="modulo_id">Módulo</label>
                    <select name="modulo_id" id="modulo_id" class="form-control">
                        <option value="">selecciona un módulo</option>
                        @foreach($modulos as $key => $modulo)
                            <option value="{{ $key }}" {{ old('modulo_id', $item->modulo_id) == $key ? 'selected' : '' }}>{{ $modulo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="table">Contador (tabla)</label>
                    <select name="table" class="form-control" id="table">
                        <option value="">Seleccione un tabla</option>
                        @foreach($tables as $table)
                            @php
                                $tableValues = array_values((array)$table);
                                $tableName = $tableValues[0] ?? '';
                            @endphp
                            @if($tableName)
                                <option value="{{$tableName}}" {{ $tableName == $tabla ? 'selected' : '' }}>
                                    {{$tableName}}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="separator" id="separator" class="form-check-input" value="1" {{ $item->separator == 1 ? 'checked' : '' }}>
                        <label for="separator" class="form-check-label">Separador</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="visible" id="visible" class="form-check-input" value="1" {{ $item->visible == 1 ? 'checked' : '' }}>
                        <label for="visible" class="form-check-label">Visible</label>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar elemento
                </button>
            </div>
        </div>
    </div>
</div>
    </form>
@stop

@section('css')
@stop

@section('js')
@stop