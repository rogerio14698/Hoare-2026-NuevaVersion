@extends('adminlte::page')

@section('content_header')
  <div class="alinearHeader">
    <h1 class="fuenteTitulo">Insertar Módulo</h1>
    <a href="{{route('modulos.index')}}" class="btn-nueva-tarea"> Volver</a>
  </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Datos del módulo</h3>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger m-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('modulos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                <div class="card-body">
                    <div class="form-group row">
                        <label for="nombre" class="col-sm-3 col-form-label">Nombre</label>
                        <div class="col-sm-9">
                            <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre del módulo" value="{{ old('nombre') }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="descripcion" class="col-sm-3 col-form-label">Descripción</label>
                        <div class="col-sm-9">
                            <input type="text" name="descripcion" id="descripcion" class="form-control" placeholder="Descripción del módulo" value="{{ old('descripcion') }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="slug" class="col-sm-3 col-form-label">Slug</label>
                        <div class="col-sm-9">
                            <input type="text" name="slug" id="slug" class="form-control" placeholder="slug-del-modulo" value="{{ old('slug') }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="imagen" class="col-sm-3 col-form-label">Imagen</label>
                        <div class="col-sm-9">
                            <div class="form-check">
                                <input type="checkbox" name="imagen" id="imagen" class="form-check-input" value="1" {{ old('imagen') ? 'checked' : '' }}>
                                <label class="form-check-label" for="imagen">
                                    Activar imágenes para este módulo
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    <button type="submit" class="btn-nueva-tarea"> Insertar</button>
                    <a href="{{route('modulos.index')}}" class="btn-cancelar"> Cancelar</a>
                </div>

                </form>

            </div>
        </div>
    </div>
@endsection

@section('css')
@stop

@section('js')
@stop
