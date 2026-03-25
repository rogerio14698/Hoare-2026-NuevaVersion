@extends('adminlte::page')

@section('content_header')
  <h1>
    Listado
    <small>Clientes</small>
  </h1>
    <h2><button type="button" class="btn btn-block btn-success btn-xs"><i class="fa fa-plus"></i> Añadir</button></h2>
  <ol class="breadcrumb">
    <li><a href="{{ route('eunomia.home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">clientes</li>
  </ol>
@stop

@section('content')
  <div class="row">
    <div class="col-xs-12">

      <div class="box">

        <!-- /.box-header -->
        <div class="box-body">

          <table id="list" class="table table-bordered table-striped">

            <thead>
            <tr>
              <th>Codigo</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Teléfono</th>
              <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <tr>
              <td>SER</td>
              <td>Seresco</td>
              <td>info@seresco.es</td>
              <td>984050409</td>
              <td><button type="button" class="btn btn btn-warning btn-xs">Editar</button> <button type="button" class="btn btn btn-danger btn-xs">Eliminar</button></td>
            </tr>
        </tbody>

          </table>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->

@endsection

@section('css')
  <!-- DataTables -->


@stop

@section('js')

  <!-- page script -->

  <!-- DataTables -->

  <script src="{{asset("vendor/adminlte/plugins/datatables/jquery.dataTables.min.js")}}"> </script>
  <script src="{{asset("vendor/adminlte/plugins/datatables/dataTables.bootstrap.min.js")}}"> </script>

  <script>
    $(function () {
      $('#list').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "stateSave": true,
        "responsive": true,



      });
    });
  </script>
@stop
