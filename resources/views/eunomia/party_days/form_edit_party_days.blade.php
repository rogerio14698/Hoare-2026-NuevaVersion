@extends('adminlte::page')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Calendario de festivos</h1>
    </div>
@stop

@section('content')
    <div class="row">
        @if( \Auth::user()->compruebaSeguridad('crear-dia-festivo') == true)
        <div class="col-md-4 col-12">
            <div class="card card-primary card-outline mb-3">
                <div class="card-header">
                    <h3 class="card-title">Nuevo día festivo</h3>
                </div>
                <form action="{{ route('party_days.store') }}" method="POST" enctype="multipart/form-data" id="form_party_days">
                    @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label for="name" class="col-sm-4 col-form-label">Nombre</label>
                        <div class="col-sm-8">
                            <input type="text" name="name" class="form-control" placeholder="Nombre del festivo" id="name" value="{{ old('name') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="date_type" class="col-sm-4 col-form-label">Tipo de fiesta</label>
                        <div class="col-sm-8">
                            <select name="date_type" class="form-control" id="date_type">
                                <option value="">Selecciona el tipo</option>
                                @foreach($date_types as $key => $value)
                                    <option value="{{ $key }}" {{ old('date_type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="date" class="col-sm-4 col-form-label">Fecha</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                </div>
                                <input type="text" name="date" class="form-control" id="date" placeholder="YYYY-MM-DD" value="{{ old('date') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" id="insertar" class="btn-guardar">
                         Guardar
                    </button>
                </div>
                </form>
            </div>
        </div>
        @endif

        @if( \Auth::user()->compruebaSeguridad('mostrar-dias-festivos') == true)
        <div class="col-md-8 col-12">
            <div class="card card-primary card-outline mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fas fa-list"></i> Listado de días festivos</h3>
                    <form method="GET" action="{{ route('party_days.index') }}" class="form-inline">
                        <label for="year" class="mr-2 mb-0">Año:</label>
                        <select name="year" id="year" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0" id="tabla-festivos">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($party_days as $party_day)
                                <tr>
                                    <td>
                                        {{ $party_day->name }}
                                    </td>
                                    <td>
                                        {{ $party_day->date_type }}
                                    </td>
                                    <td>
                                        {{ $party_day->date }}
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('party_days.edit', $party_day->id) }}" class="btn-modificar"> Editar</a>
                                        <form action="{{ route('party_days.destroy', $party_day->id) }}" method="POST" style="display:inline" class="form_eliminar">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-eliminar"> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-2">
                        {{ $party_days->appends(['year' => $year])->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Calendario</h3>
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection


@section('css')

    <!-- fullCalendar 2.2.5-->
    <link rel="stylesheet" href="{{asset("https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.css")}}">
    <link rel="stylesheet" href="{{asset("https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.print.css")}}" media="print">

    <style>
        /* Estilo del calendario similar al del dashboard */
        #calendar {
            padding: 25px;
        }
        /* Solo borde exterior en el contenedor, no en las tablas internas */
        .fc-view-container {
            border: 0.5px solid #ddd !important;
        }
        /* Corrige el tamaño de los iconos de paginación de Laravel y adapta a Bootstrap 4 */
        .pagination .page-link {
            padding: 0.25rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
        }
        .pagination .page-item .fa, .pagination .page-item .fas {
            font-size: 1em !important;
            vertical-align: middle;
        }
        .pagination {
            margin-bottom: 0;
        }
    </style>
@endsection


@section('js')
    <!-- bootstrap datepicker -->
    <script src="{{asset('vendor/adminlte/plugins/datepicker/bootstrap-datepicker.js')}}"></script>

    <!-- Languaje -->
    <script src="{{asset('vendor/adminlte/plugins/datepicker/locales/bootstrap-datepicker.es.js')}}"></script>

    <script type="text/javascript">
        //Date picker para alta
        $('#date').datepicker({
            autoclose: true,
            todayHighlight: true,
            weekStart: 1,
            language: 'es',
            format: "yyyy-mm-dd",
        });

        //Envio formulario ajax
        $('#insertar').click(function(){
            $.ajax({
                url: '{{route('insertaDiasFestivos')}}',
                type: 'POST',
                data: {
                    name: $('#name').val(),
                    date: $('#date').val(),
                    date_type: $('#date_type').val(),
                    _token: $("input[name='_token']").val()
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                error: function (jqXHR, textStatus) {
                    console.log('Error:', jqXHR.responseText);
                    alert('Error al guardar el día festivo');
                },
                success: function (data) {
                    console.log('Respuesta:', data);
                    if (data.success) {
                        alert('Día festivo guardado correctamente');
                        $('#form_party_days')[0].reset();
                        location.reload();
                    } else {
                        alert('Error al guardar el día festivo');
                    }
                }
            })
        });

        // Confirmación de borrado (como en módulos)
        $(function(){
            $('.form_eliminar .btn-danger').on('click', function(e){
                e.preventDefault();
                var form = $(this).closest('form');
                if (confirm('¿Está seguro que desea eliminar este día festivo?')) {
                    form.submit();
                }
            });
        });
    </script>


    <!-- fullCalendar 2.2.5 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.js"></script>
    <script src="{{asset("vendor/adminlte/plugins/fullcalendar/locale/es.js")}}"> </script>

    <!-- DataTables -->
    <script src="{{asset("vendor/adminlte/plugins/datatables/jquery.dataTables.min.js")}}"> </script>
    <script src="{{asset("vendor/adminlte/plugins/datatables/dataTables.bootstrap.min.js")}}"> </script>

    <!-- calendario -->

    <script>
        $(function () {
            /* initialize the calendar
             -----------------------------------------------------------------*/
            //Date for the calendar events (dummy data)
            var date = new Date();

            $('#calendar').fullCalendar({

                locale: "es",
                nextDayThreshold: '00:00:00',

                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month'
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'mes'
                },

                //events
                events: [
                    @foreach ($all_party_days as $party_day)
                    {
                        id: '{{$party_day->id}}',
                        title: '{{$party_day->name}}',
                        start: '{{$party_day->date}}',
                        end: '{{$party_day->date}}',
                        @if ($party_day->date_type == 'Nacional')
                            backgroundColor: "#a39c12",
                            borderColor: "#a39c12",
                        @elseif ($party_day->date_type == 'Autonómica')
                            backgroundColor: "#a05ca8",
                            borderColor: "#a05ca8",
                        @elseif ($party_day->date_type == 'Local')
                            backgroundColor: "#e0a65a",
                            borderColor: "#e0a65a",
                        @endif
                        allDay: true
                    }@if(!$loop->last),@endif
                    @endforeach
                ],

                editable: false,
                droppable: false
            });
        });
    </script>
    <!-- fin calendario-->
@endsection