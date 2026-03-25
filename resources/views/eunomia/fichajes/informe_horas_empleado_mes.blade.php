<!-- Contenido del informe para carga en modal -->

    <div class="content" id="contentToPrint">
        <div class="col-lg-12" style="float: left;">
            <div class="col-lg-3" style="float: left;"><img src="{{asset('images/logo_mglab.png')}}" width="200"></div>
            <div class="col-lg-5" style="float: left;">
                <div class="col-lg-12">
                    <div class="form-group col-lg-12">
                        Empresa: <strong>{{$user->empresa->nombre ?? '-'}}</strong><br>

                        CIF: <strong>{{$user->empresa->cif ?? '-'}}</strong><br>

                        Domicilio: <strong>{{$user->empresa->domicilio ?? '-'}}</strong>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="form-group col-lg-12">
                        Trabajador/a: <strong>{{$user->nombre_completo}}</strong><br>

                        NIF/NIE: <strong>{{$user->dni}}</strong>
                    </div>
                </div>
            </div>

            <div class="col-lg-4" style="float: left;">
                <div class="col-lg-12">&nbsp;</div>
                <div class="col-lg-12">
                    Mes: <strong>{{$user->devuelveMesLetra($mes)}}</strong>
                </div>
                <div class="col-lg-12">
                    Año: <strong>{{$anio}}</strong>
                </div>
            </div>
        </div>

        <div class="col-lg-12" style="float: left;">
            <div class="col-lg-12 col-md-10">

                <div class="box">

                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="informe-wrap">
                            <table id="list2" class="table table-bordered" >
                                <colgroup>
                                    <col class="col-dia"><!-- Día -->
                                    @for($i = 0; $i < 11; $i++)
                                        <col class="col-hora"><!-- 11 horas (8..18) -->
                                    @endfor
                                    <col class="col-total"><!-- Total -->
                                </colgroup>

                                <thead>
                                    <tr>
                                        <th rowspan="2">Días</th>
                                        <th colspan="11">Tiempo trabajado</th>
                                        <th rowspan="2">Total</th>
                                    </tr>
                                    <tr>
                                        @for($i = 8; $i < 19; $i++)
                                            <th>{{ $i }}</th>
                                        @endfor
                                    </tr>
                                </thead>

                                <tbody id="tabla_horas_trabajadas_mes"></tbody>
                            </table>
                        </div>

                        <!--<p>* Hay que descontar 30 minutos cada día empleados en la pausa/café</p>   -->
                    </div>

                </div>
            </div>
        </div>
    </div>
    {{csrf_field()}}
</body>


<script src="{{asset('js/printThis.js')}}"></script>
<script>
    $(document).ready(function () {
        muestraTablaTiempoTrabajadoMes({{$anio}},{{$mes}});
    });

    function muestraTablaTiempoTrabajadoMes(anio, mes) {
        $('#tabla_horas_trabajadas_mes').html('<tr><td align="center" colspan="13"><img src="{{asset('images/carga.gif')}}"></td></tr>');

        $.ajax({
            method: "POST",
            url: "{{route('muestraTablaTiempoTrabajado')}}",
            data: {
                _token: "{{ csrf_token() }}",   // más robusto que buscarlo en un input
                intervalo: 'mes',
                anio: anio,
                mes: mes, // antes estaba como 'mesito: mes'
                user_id: {{$user->id}},
                informe_completo: {{ $informe_completo ? 'true' : 'false' }}
              }
        })
            .done(function (html) {
                $('#tabla_horas_trabajadas_mes').html(html);
            })
            .fail(function (xhr) {
                console.error(xhr.responseText);
                alert('Error al cargar el informe');
                $('#tabla_horas_trabajadas_mes').html(
                    '<tr><td class="text-danger" colspan="13">Error al cargar el informe.</td></tr>'
                );
            });
    }

    $(document)
        .on('shown.bs.modal', '.modal', function () {
            $(this).removeAttr('aria-hidden').attr('aria-modal', 'true');
        })
        .on('hidden.bs.modal', '.modal', function () {
            $(this).attr('aria-hidden', 'true').removeAttr('aria-modal');
        });

</script>