@extends('adminlte::page')

@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1>Editar Módulo</h1>
    <a href="{{route('modulos.index')}}" class="btn btn-primary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
  </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- general form elements -->
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

                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ route('modulos.update', $modulo) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                <div class="card-body">

                    <div class="form-group row">
                        <label for="nombre" class="col-sm-3 col-form-label">Nombre</label>
                        <div class="col-sm-9">
                            <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre del módulo" value="{{ old('nombre', $modulo->nombre) }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="descripcion" class="col-sm-3 col-form-label">Descripción</label>
                        <div class="col-sm-9">
                            <input type="text" name="descripcion" id="descripcion" class="form-control" placeholder="Descripción del módulo" value="{{ old('descripcion', $modulo->descripcion) }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="slug" class="col-sm-3 col-form-label">Slug</label>
                        <div class="col-sm-9">
                            <input type="text" name="slug" id="slug" class="form-control" placeholder="slug-del-modulo" value="{{ old('slug', $modulo->slug) }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="imagen" class="col-sm-3 col-form-label">Imagen</label>
                        <div class="col-sm-9">
                            <div class="form-check">
                                <input type="checkbox" name="imagen" id="imagen" class="form-check-input" value="1" {{ old('imagen', $modulo->imagen) ? 'checked' : '' }}>
                                <label class="form-check-label" for="imagen">
                                    Activar imágenes para este módulo
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
                    <a href="{{route('modulos.index')}}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
                </div>

                </form>

            </div>
            <!-- /.card -->
        </div>
    </div>


@endsection

@section('css')
@stop

@section('js')
@stop
