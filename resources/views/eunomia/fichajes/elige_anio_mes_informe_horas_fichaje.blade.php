
<!-- Contenido simplificado para carga dinámica en modal -->
<div id="content">
    <div class="row">
        <div class="col-md-12">
            <h4 class="classic-title"><span>Elije Mes y Año</span></h4>
            <form action="#" method="POST" name="form_elige_mes_anio_fichaje" class="contact-form" id="contact-form">
                @csrf
                <div class="form-group">
                    <div class="controls">
                        <label for="mes">Mes</label>
                        <select name="mes" id="mes" class="form-control" required>
                            <option value="">Elija un mes</option>
                            @foreach($meses as $key => $value)
                                <option value="{{ $key }}" {{ old('mes', date('n')) == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="controls">
                        <label for="anio">Año</label>
                        <select name="anio" id="anio" class="form-control" required>
                            <option value="">Elija un año</option>
                            @foreach($anios as $key => $value)
                                <option value="{{ $key }}" {{ old('anio', date('Y')) == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="controls">
                        <input type="checkbox" name="informe_completo" id="informe_completo" value="1" checked> Informe completo
                    </div>
                </div>
                <input type="hidden" name="user_id" value="{{ $user_id }}">
                <div class="form-group">
                    <button type="button" id="boton_envia_mes_anio_fichaje" class="btn btn-info float">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('js')
<script>
    $('#boton_envia_mes_anio_fichaje').click(function(){
        // Validar que se haya seleccionado mes y año
        var mesSeleccionado = $('#mes').val();
        var anioSeleccionado = $('#anio').val();

        if (!mesSeleccionado || !anioSeleccionado) {
            alert('Por favor, selecciona tanto el mes como el año antes de continuar.');
            return;
        }

        // Construir la URL del informe y abrirlo en una nueva ventana
        var informeCompleto = $('#informe_completo').prop('checked') ? 1 : 0;
        var url = '/eunomia/fichajes/informeHorasEmpleadoMes/{{$user_id}}/' + mesSeleccionado + '/' + anioSeleccionado + '/' + informeCompleto;

        // Abrir en nueva ventana
        window.open(url, '_blank');
    });
</script>
@endsection