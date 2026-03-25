@extends('adminlte::page')

@section('content_header')
    <h1>
        Mensaje
        <small>Error</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('eunomia.home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Mensajes</li>
    </ol>
@stop

@section('content')
 <br/><div class='rechazado'><label style='color:#FA206A'><?php  echo $msj; ?></label>  </div> 

 @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> {{ trans('adminlte_lang::message.someproblems') }}<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
  @endif
@stop

@section('css')

    <!-- Mensajes -->
    <!-- <link rel="stylesheet" href="{{asset('css/mensajes.css')}}"> -->

@stop

@section('js')

@stop
