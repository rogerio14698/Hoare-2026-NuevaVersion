@extends('adminlte::page')

@section('content_header')
    <h1>
        Listado
        <small>Dominios</small>
    </h1>
    @if( \Auth::user()->compruebaSeguridad('crear-cliente') == true)
        <h2><a href="{{route('dominios.create')}}" class="btn btn-block btn-success btn-xs"><i class="fa fa-plus"></i> Añadir </a></h2>
    @endif

    <ol class="breadcrumb">
        <li><a href="{{ route('eunomia.home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dominios</li>
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
                            <th>Dominio</th>
                            <th>Cliente</th>
                            <th>Fecha contratación</th>
                            <th>Fecha renovación</th>
                            <th>Agente</th>
                            <th>Precio anual</th>
                            <th>Hosting</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>

                        <tfoot>
                        <tr>
                            <th>Dominio</th>
                            <th>Cliente</th>
                            <th>Fecha contratación</th>
                            <th>Fecha renovación</th>
                            <th>Agente</th>
                            <th>Precio anual</th>
                            <th>Hosting</th>
                            <th>Acciones</th>
                        </tr>
                        </tfoot>
                        <tbody>

                        @foreach ($dominios as $dominio)

                            <tr>
                                <td>{{$dominio->dominio}}</td>
                                <td>{{$dominio->customer->nombre_cliente}}</td>
                                <td>{{$dominio->fecha_contratacion!=''?\Carbon\Carbon::createFromFormat('Y-m-d', $dominio->fecha_contratacion)->format('d/m/Y'):''}}</td>
                                <td>{{$dominio->fecha_renovacion!=''?\Carbon\Carbon::createFromFormat('Y-m-d', $dominio->fecha_renovacion)->format('d/m/Y'):''}}</td>
                                <td>{{$dominio->agente_dominio->nombre}}</td>
                                <td>{{$dominio->precio_anual}}&euro;</td>
                                <td>{!! $dominio->hosting?('SI / ' . $dominio->precio_hosting . '&euro;'):'NO' !!}</td>
                                <td>@if( \Auth::user()->compruebaSeguridad('editar-dominio') == true)
                                        <a href="{{ route('dominios.edit', $dominio) }}" class="btn btn btn-warning btn-xs">Editar</a>
                                    @endif
                                    @if( \Auth::user()->compruebaSeguridad('eliminar-cliente') == true)
                                        <form action="{{ route('dominios.destroy', $dominio->id) }}" method="POST" style="display:inline" class="form_eliminar">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn btn-danger btn-xs">Eliminar</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>

                        @endforeach
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


    <!-- Bootstrap Dialog -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css" />


@stop

@section('js')

    <!-- page script -->

    <!-- DataTables -->

    <script src="{{asset("vendor/adminlte/plugins/datatables/jquery.dataTables.min.js")}}"> </script>
    <script src="{{asset("vendor/adminlte/plugins/datatables/dataTables.bootstrap.min.js")}}"> </script>

    <script>
        $(function () {
            table = $('#list').DataTable({
                paging: true,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                stateSave: true,
                responsive: true,
                language: {
                    url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                },
                initComplete: function () {
                    var i = 1;
                    this.api().columns().every( function () {
                        if (i==2 || i==5) {
                            var column = this;
                            var select = $('<select><option value=""></option></select>')
                                .appendTo($(column.footer()).empty())
                                .on('change', function () {
                                    var val = $.fn.dataTable.util.escapeRegex(
                                        $(this).val()
                                    );

                                    column
                                        .search(val ? '^' + val + '$' : '', true, false)
                                        .draw();
                                });

                            column.data().unique().sort().each(function (d, j) {
                                select.append('<option value="' + d + '">' + d + '</option>')
                            });
                        }
                        i++;
                    } );
                }
            });
        });
    </script>

    <!-- Bootstrap Dialog -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/js/bootstrap-dialog.min.js"></script>

    <script language="JavaScript">
        $('.btn-danger').click(function(e){
            e.preventDefault();
            boton = this;

            BootstrapDialog.confirm(
                '¿Está seguro que desea eliminar el registro?', function(result) {

                    if (result) {
                        $(boton).parent().submit();
                    }

                });
        });
    </script>
@stop
