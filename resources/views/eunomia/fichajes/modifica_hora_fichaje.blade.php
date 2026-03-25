<!-- Start Content -->
<div id="content">
    <div class="row">

        <div class="col-md-12">

            <h4 class="classic-title"><strong>{{$fecha_fichaje}}</strong></h4>

            <!-- Classic Heading -->
            <h4 class="classic-title"><span>Establece hora</span></h4>

            <!-- Start Contact Form -->

            <form action="{{ route('fichajes.update', $fichaje_id) }}" method="POST" name="form_modifica_hora_fichaje" class="contact-form" id="contact-form">
                @csrf
                @method('PATCH')
            <div class="form-group">
                <div class="controls">
                    <label for="fecha">Fecha</label>
                    <input type="date" name="fecha" id="fecha" class="form-control" required value="{{ old('fecha', $fecha_fichaje_campo) }}">
                </div>
            </div>

            <div class="form-group">
                <div class="controls">
                    <label for="hora">Hora</label>
                    <input type="time" name="hora" id="hora" class="form-control" required value="{{ old('hora', $hora_fichaje) }}">
                </div>
            </div>

            <div class="form-group">
                <div class="controls">
                    <label for="comentarios">Comentarios</label>
                    <textarea name="comentarios" id="comentarios" class="form-control">{{ old('comentarios', $comentarios) }}</textarea>
                </div>
            </div>
            <input type="hidden" name="fichaje_id" value="{{ $fichaje_id }}">

            <div class="form-group">
                <button id="boton_establece_hora_fichaje" class="btn btn-info float">Enviar</button>
                <button type="button" class="btn btn-secondary float" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger float" id="btn-eliminar-fichaje">Eliminar</button>
            </div>
            </form>
        <!-- End Contact Form -->

        </div>
    </div>
</div>

<script>
$('#boton_establece_hora_fichaje').click(function(e){
    e.preventDefault();
    
    $.ajax({
        method: "PATCH",
        url: "{{ route('fichajes.update', $fichaje_id) }}",
        data: {
            _token: $("input[name='_token']").val(),
            fecha: $('#fecha').val(),
            hora: $('#hora').val(),
            comentarios: $('#comentarios').val(),
            fichaje_id: {{$fichaje_id}}
        },
        success: function (data) {
            if (data.success) {
                // Cerrar el modal de Bootstrap 5
                $('#modalEditarFichaje').modal('hide');
                // Recargar la tabla de fichajes
                if (typeof muestraTablaTiempoTrabajado === 'function') {
                    muestraTablaTiempoTrabajado();
                } else {
                    // Si no está disponible, recargar la página completa
                    window.location.reload();
                }
            } else {
                alert('Error: ' + (data.message || 'Error desconocido'));
            }
        },
        error: function (jqXHR, textStatus) {
            if (jqXHR.status === 422) {
                try {
                    var response = JSON.parse(jqXHR.responseText);
                    alert('Error de validación:\n\n' + response.error);
                } catch (e) {
                    alert('Error de validación en formato incorrecto');
                }
            } else {
                alert('Error al actualizar el fichaje');
            }
        }
    });
});
</script>
