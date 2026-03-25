@extends('adminlte::page')

@section('title', 'Horae')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')

    @if (Session::has('status'))
        <hr />
        <div class='alert alert-success'>
            {{Session::get('status')}}
        </div>
        <hr />
    @endif

@stop