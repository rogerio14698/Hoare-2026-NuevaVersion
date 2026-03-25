@extends('adminlte::page')

@section('title', 'Horae | Dashboard')

@section('content_header')
  <h1>
    Dashboard
    <small>Control panel</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
    <li class="active">Dashboard</li>
  </ol>
@stop

@section('content')

  <!-- Small boxes (Stat box) -->
  <div class="row">
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3>000</h3>

          <p>Usuarios</p>
        </div>
        <div class="icon">
          <i class="ion ion-person-stalker"></i>
        </div>
        <a href="{{ route('users.index') }}" class="small-box-footer"><i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->

  </div>
  <!-- /.row -->




  <div class="row">

      <div class="col-md-12">
        <div class="box box-solid">
          <div class="box-header with-border">
            <i class="fa fa-quote-left"></i>

            <h3 class="box-title">La Frase del dia!</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <blockquote>
              <p>"Según vamos adquiriendo conocimiento, las cosas no se hacen más comprensibles, sino más misteriosas"</p>
              <small>Autor: <cite title="Source Title">Albert Schweitzer</cite></small>
            </blockquote>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- ./col -->






  </div>
  <!-- /.row (main row) -->

@stop
