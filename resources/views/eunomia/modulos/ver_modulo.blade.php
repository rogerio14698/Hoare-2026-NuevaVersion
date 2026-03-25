@extends('adminlte::page')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0"><i class="fas fa-cube text-primary mr-2"></i>{{ $modulo->nombre }}</h1>
        <a href="{{ route('modulos.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body py-4">
                    <div class="row align-items-center mb-3">
                        <div class="col-12 col-md-3 text-center mb-3 mb-md-0">
                            <div class="display-4">
                                @if($modulo->imagen == 1)
                                    <i class="fas fa-check-circle text-success"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger"></i>
                                @endif
                            </div>
                            <div class="small mt-2">
                                @if($modulo->imagen == 1)
                                    <span class="badge badge-success">Imagen activada</span>
                                @else
                                    <span class="badge badge-danger">Sin imagen</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12 col-md-9">
                            <h4 class="font-weight-bold mb-2">{{ $modulo->nombre }}</h4>
                            <p class="mb-1 text-muted"><strong>Slug:</strong> <span class="text-monospace">{{ $modulo->slug }}</span></p>
                            <p class="mb-0"><strong>Descripción:</strong> {{ $modulo->descripcion }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
