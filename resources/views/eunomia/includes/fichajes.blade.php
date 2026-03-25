
<div class="card-fichajes">
    <header>
        <h3>Fichajes <span>jornada laboral</span></h3>
    </header>
    <!-- /.card-header -->
    <div class="card-body">
      <button class="btn-checkIn " id="btn_fichaje"></button> 
      <button id="informe_horas_empleado_mes" class="btn-informeMes">Informe mes</button>
      <!--Aqui vamos a poner un boton que nos permita modificar el fichaje sin la necesisdad de entrar la base de datos
        La idea es poder modificar el fichaje de cada dia de la semana.  -->

        <p><span style="font-size: 16px;">Llevas trabajado en el día de hoy: <span id="tiempo_trabajado"></span></span></p>
  
        <div class="form-group col-lg-12">
            <table width="100%" border="1" style="border-color: #ddd; border-collapse: collapse !important; table-layout: fixed;">
                <thead>
                <tr>
                    <td align="center" colspan="13">
                        <span style="font-size: 14px;">Semana del <strong>{{$semana_actual['fechaInicio']}}</strong> al <strong>{{$semana_actual['fechaFin']}}</strong></span>
                    </td>
                </tr>
                <tr>
                    <th width="20%" rowspan="2">Día</th>
                    <th width="48%" colspan="11" style="color:#000;">Tiempo trabajado</th>
                    <th width="32%" rowspan="2" >Total</th>
                </tr>
                <tr>
                    @for($i=8;$i<19;$i++)
                        <th width="4.4%" style=" text-align: center; padding: 1px; font-size: 8px;">{{$i}}</th>
                    @endfor
                </tr>
                </thead>
                <tbody id="tabla_horas_trabajadas">

                </tbody>
            </table>
        </div>
    </div>
    <p><span id="tiempo_trabajado_semana"></span></p>
  
</div>
@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/informe_horas_trabajadas_mes.css') }}">
@endpush