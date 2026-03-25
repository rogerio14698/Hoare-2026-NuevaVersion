@extends('adminlte::page')

@section('content_header')
    <div class="alinearHeader">
        <h1 class="fuenteTitulo">Calendario de vacaciones</h1>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <div class="form-group">
                        <h5>Por favor marca los días que vas a estar de <span style="text-decoration: underline;">VACACIONES</span>:</h5>
                    </div>
                    
                    <div id="cal_disp"></div>

                    {{ csrf_field() }}
                </div>
                
                <div class="card-footer">
                    <button type="button" id="btn_editar" class="btn-guardar">Guardar</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('css')
    <!-- Calendario -->
    <link rel="stylesheet" href="{{asset("css/jquery-ui.multidatespicker.css")}}">
    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/pepper-grinder/jquery-ui.css">


    <style>
        #cal_disp {
            max-width: 100%;
            overflow-x: auto;
            margin: 0 auto;
        }
        
        #cal_disp .ui-datepicker-inline {
            width: 100% !important;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        #cal_disp .ui-datepicker-group {
            width: 24% !important;
            margin: 0.5% !important;
            float: left !important;
        }
        
        /* Responsive adjustments */
        @media (max-width: 1400px) {
            #cal_disp .ui-datepicker-inline {
                font-size: 0.85rem;
            }
        }
        
        @media (max-width: 992px) {
            #cal_disp .ui-datepicker-inline {
                font-size: 0.75rem;
            }
            #cal_disp .ui-datepicker-group {
                width: 32.5% !important;
                margin: 0.4% !important;
            }
        }
        
        @media (max-width: 768px) {
            #cal_disp .ui-datepicker-inline {
                font-size: 0.7rem;
            }
            #cal_disp .ui-datepicker-group {
                width: 49% !important;
                margin: 0.5% !important;
            }
            #cal_disp .ui-datepicker-calendar {
                font-size: 0.65rem;
            }
        }
        
        @media (max-width: 576px) {
            #cal_disp .ui-datepicker-inline {
                font-size: 0.65rem;
            }
        }
    </style>
@endsection


@section('js')
    <!-- Calendario -->
    <script language="JavaScript" src="{{asset('vendor/adminlte/plugins/jQueryUI/jquery-ui.min.js')}}"></script>
    <script language="JavaScript" src="{{asset('js/jquery-ui.multidatespicker.js')}}"></script>
    <script language="JavaScript" src="{{asset('js/datepicker-es.js')}}"></script>
    <script language="JavaScript">
        var today = new Date();
        var y = today.getFullYear();
        var calendario = $('#cal_disp').multiDatesPicker({
            @if (count($holidaydays)>0)
            addDates: {!! str_replace('"',"'",$holidaydays) !!},
            @endif
            numberOfMonths: [3,4],
            defaultDate: '1/1/'+y
        });
    </script>

    <script language="JavaScript">
        $('#btn_editar').click(function(){
            console.log('Botón editar clickeado');
            
            var fechas = calendario.multiDatesPicker('getDates');
            console.log('Fechas seleccionadas:', fechas);
            
            var token = $('input[name="_token"]').val();
            console.log('Token encontrado:', token);
            
            if (fechas.length === 0) {
                alert('Por favor selecciona al menos una fecha antes de guardar.');
                return;
            }
            
            $.ajax({
                url: '{{route('insertaDiasNoDisponibles')}}',
                type: 'POST',
                data: {
                    dates: fechas,
                    user_id: '{{\Auth::user()->id}}',
                    _token: token
                },
                error: function (jqXHR, textStatus) {
                    console.error('Error AJAX:', jqXHR.responseText);
                    alert('Error al guardar las vacaciones: ' + (jqXHR.responseJSON ? jqXHR.responseJSON.message : jqXHR.responseText));
                },
                success: function (data) {
                    console.log('Éxito:', data);
                    alert('Vacaciones guardadas correctamente');
                    window.location.href = "{{ route('eunomia.home') }}";
                }
            });
        });
    </script>
@endsection
