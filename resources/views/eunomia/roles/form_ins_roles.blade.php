@extends('adminlte::page')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="mb-0">Nuevo Rol</h1>
    </div>
@stop


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Datos del rol</h3>
                    <a href="{{route('roles.index')}}" class="btn btn-primary btn-sm float-right"><i class="fas fa-arrow-left"></i> Volver</a>
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
                <form action="{{ route('roles.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label for="name" class="col-sm-3 col-form-label">Nombre</label>
                        <div class="col-sm-9">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Nombre" value="{{ old('name') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="slug" class="col-sm-3 col-form-label">Slug</label>
                        <div class="col-sm-9">
                            <input type="text" name="slug" id="slug" class="form-control" placeholder="Slug" value="{{ old('slug') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="description" class="col-sm-3 col-form-label">Descripción</label>
                        <div class="col-sm-9">
                            <input type="text" name="description" id="description" class="form-control" placeholder="Descripción" value="{{ old('description') }}">
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                    <a href="{{route('roles.index')}}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
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
