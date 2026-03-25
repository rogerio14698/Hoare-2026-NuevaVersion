@extends('adminlte::page')

@section('title', 'Horae | Dashboard Nuevo')

@section('content_header')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="m-0">Dashboard - {{ \Auth::user()->name }}</h1>
    </div>
    <!--Aquí va a ir el navegador superior o su include -->
@stop


@section('content')



    <div class="contenedor-dashboard">


        <div class="row mb-3">
            <div class="col-12">
                @include('eunomia.includes.navegador')
            </div>
        </div>

        {{-- Tres columnas principales: Tareas | Calendario | Todo List --}}
        <div class="row">
            <div class="col-md-4">
               <h1>Aqui va tareas</h1>
            </div>

            <div class="col-md-4">
                <h1>Aqui va calendario</h1>
            </div>

            <div class="col-md-4">
                <h1>Aqui va todo list</h1>
            </div>
        </div>

    </div>

@stop



@section('js')
    {{-- JS específicos del nuevo dashboard pueden añadirse aquí --}}
@stop
