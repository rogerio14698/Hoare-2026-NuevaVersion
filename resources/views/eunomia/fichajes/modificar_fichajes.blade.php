@extends('adminlte::page')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Modificar Fichajes: {{ $user->name }}</h1>
        <a href="{{ url('eunomia/fichajes') }}" class="btn btn-default">Volver</a>
    </div>
@stop

@section('content')

    <h3 class="fuente-negro">Listado de fichajes de : {{ $user->name }}</h3>
    <!--Aqui vamos a crear un form para ingresar un fichaje en una fecha determinada,
                        Forzamos un fichaje en una fecha si ya existe un fichaje en esa fecha, se sobreescribe-->


    <div class="header-modificarFichaje">
        <div class="anadir-fichaje">
            <h4 class="fuente-negro">Añadir nuevo fichaje no existente</h4>
            <button class="btn-modificar" id="btnAbrirModal">Añadir fichaje</button>
            <p class="fuente-negro">Si el fichaje ya existe, se actualizará con los nuevos datos.</p>
        </div>

        <div class="generar-informe">
            <h4 class="fuente-negro">Generar Informe</h4>
            <button class="btn-modificar" id="btnGenerarInforme">Generar Informe</button>
            <p class="fuente-negro">Genera un informe de los fichajes del usuario.</p>
        </div>
    </div>


    <table class="tabla-gestion-fichajes">
        <thead>
            <tr>
                <th class="col-fecha">Fecha</th>
                <th class="col-hora">Hora</th>
                <th class="col-tipo">Tipo</th>
                <th class="col-comentarios">Comentarios</th>
                <th class="col-acciones">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <!-- Aqui se mostrarán los fichajes del usuario -->
            @foreach ($fichajes as $fichaje)
                <tr>
                    <td class="fichaje-fecha">
                        {{ $fichaje->fecha ? \Carbon\Carbon::parse($fichaje->fecha)->format('d/m/Y') : '' }}
                    </td>
                    <td class="fichaje-hora">
                        {{ $fichaje->fecha ? \Carbon\Carbon::parse($fichaje->fecha)->format('H:i') : '' }}
                    </td>
                    <td class="fichaje-tipo">{{ $fichaje->tipo }}</td>
                    <td class="fichaje-comentario">{{ $fichaje->comentarios }}</td>
                    <td class="fichaje-acciones">
                        @if (\Auth::user()->compruebaSeguridad('ver-todos-fichajes') || \Auth::user()->id == $user->id)
                            <button type="button" class="btn-modificar btn-abrir-modificar" data-id="{{ $fichaje->id }}"
                                data-fecha="{{ $fichaje->fecha ? \Carbon\Carbon::parse($fichaje->fecha)->format('Y-m-d') : '' }}"
                                data-hora="{{ $fichaje->fecha ? \Carbon\Carbon::parse($fichaje->fecha)->format('H:i') : '' }}"
                                data-tipo="{{ $fichaje->tipo }}" data-comentarios="{{ $fichaje->comentarios }}">
                                Modificar Fichaje
                            </button>
                            <!--Boton para eliminar el fichaje de ese dia. -->
                            <form action="{{ route('fichajes.destroy', $fichaje->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-eliminar">Eliminar Fichaje</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Enlaces de paginación --}}
    <div class="pagination-custom-wrapper">
        {{ $fichajes->links('vendor.pagination.simple-custom') }}
    </div>

    <!--Modal para añadir fichaje y reemplazar si existe uno con la misma fecha y hora -->
    <div class="modal-overlay" id="modal-anadirFichaje">
        <div class="modal-container">
            <div class="modal-header-simple">
                <h5>Añadir Fichaje</h5>
                <button type="button" class="btn-close-modal" id="closeModal">&times;</button>
            </div>

            <form id="form-anadirFichaje" method="POST" action="{{ route('fichajes.store') }}" class="modal-form">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">

                <div class="modal-body-content">
                    <div class="form-group">
                        <label for="fecha">Fecha</label>
                        <input type="date" id="fecha" name="fecha" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="hora">Hora</label>
                            <input type="time" id="hora" name="hora" required>
                        </div>

                        <div class="form-group">
                            <label for="tipo">Tipo de Registro</label>
                            <select name="tipo" id="tipo" class="tipo" required>
                                <option value="entrada">Check-In (Entrada)</option>
                                <option value="salida">Check-Out (Salida)</option>
                            </select>
                        </div>

                    </div>

                    <div class="form-group">
                        <label for="comentarios">Comentarios</label>
                        <textarea id="comentarios" name="comentarios" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer-simple">
                    <button type="button" class="btn-cancelar" id="btnCancel">Cerrar</button>
                    <button type="submit" class="btn-guardar">Guardar Fichaje</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Fin del Modal -->

    <!-- Modal de modificar fichaje -->
    <div class="modal-overlay" id="modal-modificarFichaje">
        <div class="modal-container">
            <div class="modal-header-simple">
                <h5>Modificar Fichaje</h5>
                <button type="button" class="btn-close-modal" id="closeModalModificar">&times;</button>
            </div>

            <form id="form-modificarFichaje" method="POST" action="" class="modal-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="fichaje_id" id="modificar-fichaje-id" value="">
                <input type="hidden" name="user_id" value="{{ $user->id }}">

                <div class="modal-body-content">
                    <div class="form-group">
                        <label for="modificar-fecha">Fecha</label>
                        <input type="date" id="modificar-fecha" name="fecha" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="modificar-hora">Hora</label>
                            <input type="time" id="modificar-hora" name="hora" required>
                        </div>

                        <div class="form-group">
                            <label for="modificar-tipo">Tipo de Registro</label>
                            <select name="tipo" id="modificar-tipo" class="tipo" required>
                                <option value="entrada">Check-In (Entrada)</option>
                                <option value="salida">Check-Out (Salida)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="modificar-comentarios">Comentarios</label>
                        <textarea id="modificar-comentarios" name="comentarios" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer-simple">
                    <button type="button" class="btn-cancelar" id="btnCancelModificar">Cerrar</button>
                    <button type="submit" class="btn-guardar">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
    <!--Fin del modal de modificar el fichaje -->

    <!-- Modal para seleccionar mes/año del informe -->
    <div class="modal-overlay" id="modal-seleccionInforme">
        <div class="modal-container">
            <div class="modal-header-simple">
                <h5>Elije Año y Mes del fichaje</h5>
                <button type="button" class="btn-close-modal" id="closeModalInforme">&times;</button>
            </div>

            <div class="modal-body-content">
                <div class="form-group">
                    <label for="informe-mes">Mes</label>
                    <select id="informe-mes" class="tipo" required></select>
                </div>

                <div class="form-group">
                    <label for="informe-anio">Año</label>
                    <select id="informe-anio" class="tipo" required></select>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" id="informe-completo" value="1" checked>
                        Informe completo
                    </label>
                </div>
            </div>

            <div class="modal-footer-simple">
                <button type="button" class="btn-cancelar" id="btnCancelInforme">Cerrar</button>
                <button type="button" class="btn-guardar" id="btnEnviarInforme">Enviar</button>
            </div>
        </div>
    </div>
    <!-- Fin modal selección informe -->

    <!-- Modal grande para mostrar el informe -->
    <div class="modal-overlay" id="modal-informeCompleto">
        <div class="modal-container modal-container-xl">
            <div class="modal-header-simple">
                <h5>Informe de Horas</h5>
                <button type="button" class="btn-close-modal" id="closeModalInformeCompleto">&times;</button>
            </div>

            <div class="modal-body-content" id="informe-content-completo" style="max-height: 70vh; overflow-y: auto;">
                <div style="text-align: center;"><p>Cargando informe...</p></div>
            </div>

            <div class="modal-footer-simple">
                <button type="button" class="btn-cancelar" id="btnCerrarInformeCompleto">Cerrar</button>
                <button type="button" class="btn-guardar" id="btnImprimirInforme">Imprimir</button>
            </div>
        </div>
    </div>
    <!-- Fin modal informe completo -->

@stop


@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ========================
            // MODAL AÑADIR FICHAJE
            // ========================
            const modalAnadir = document.getElementById('modal-anadirFichaje');
            const btnAbrirAnadir = document.getElementById('btnAbrirModal');
            const btnCerrarAnadirX = document.getElementById('closeModal');
            const btnCancelarAnadir = document.getElementById('btnCancel');

            const abrirModalAnadir = () => modalAnadir.style.display = 'flex';
            const cerrarModalAnadir = () => modalAnadir.style.display = 'none';

            btnAbrirAnadir.addEventListener('click', abrirModalAnadir);
            btnCerrarAnadirX.addEventListener('click', cerrarModalAnadir);
            btnCancelarAnadir.addEventListener('click', cerrarModalAnadir);

            // Envío AJAX del formulario de añadir
            document.getElementById('form-anadirFichaje').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector(
                                '#form-anadirFichaje input[name="_token"]').value
                        }
                    })
                    .then(response => response.json().then(data => ({
                        status: response.status,
                        body: data
                    })))
                    .then(res => {
                        if (res.status === 200) {
                            alert("Guardado con éxito");
                            location.reload();
                        } else {
                            alert("Error: " + res.body.error);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });

            // ========================
            // MODAL MODIFICAR FICHAJE
            // ========================
            const modalModificar = document.getElementById('modal-modificarFichaje');
            const btnCerrarModificarX = document.getElementById('closeModalModificar');
            const btnCancelarModificar = document.getElementById('btnCancelModificar');

            const cerrarModalModificar = () => modalModificar.style.display = 'none';

            btnCerrarModificarX.addEventListener('click', cerrarModalModificar);
            btnCancelarModificar.addEventListener('click', cerrarModalModificar);

            // Abrir modal de modificar al pulsar cualquier botón "Modificar Fichaje"
            document.querySelectorAll('.btn-abrir-modificar').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const fichajeId = this.dataset.id;
                    const fecha = this.dataset.fecha;
                    const hora = this.dataset.hora;
                    const tipo = this.dataset.tipo;
                    const comentarios = this.dataset.comentarios;

                    // Rellenar campos del modal
                    document.getElementById('modificar-fichaje-id').value = fichajeId;
                    document.getElementById('modificar-fecha').value = fecha;
                    document.getElementById('modificar-hora').value = hora;
                    document.getElementById('modificar-tipo').value = tipo;
                    document.getElementById('modificar-comentarios').value = comentarios || '';

                    // Actualizar action del form con el id del fichaje
                    const form = document.getElementById('form-modificarFichaje');
                    form.action = '/eunomia/fichajes/' + fichajeId;

                    modalModificar.style.display = 'flex';
                });
            });

            // Envío AJAX del formulario de modificar
            document.getElementById('form-modificarFichaje').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector(
                                '#form-modificarFichaje input[name="_token"]').value
                        }
                    })
                    .then(response => response.json().then(data => ({
                        status: response.status,
                        body: data
                    })))
                    .then(res => {
                        if (res.status === 200) {
                            alert("Fichaje actualizado con éxito");
                            location.reload();
                        } else {
                            alert("Error: " + (res.body.error || res.body.message));
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });

            // Cerrar cualquier modal al clic fuera de la caja
            window.addEventListener('click', function(event) {
                if (event.target === modalAnadir) cerrarModalAnadir();
                if (event.target === modalModificar) cerrarModalModificar();
                if (event.target === modalSeleccionInforme) cerrarModalSeleccionInforme();
                if (event.target === modalInformeCompleto) cerrarModalInformeCompleto();
            });

            // ========================
            // MODAL GENERAR INFORME
            // ========================
            const modalSeleccionInforme = document.getElementById('modal-seleccionInforme');
            const modalInformeCompleto = document.getElementById('modal-informeCompleto');
            const userId = {{ $user->id }};

            const cerrarModalSeleccionInforme = () => modalSeleccionInforme.style.display = 'none';
            const cerrarModalInformeCompleto = () => modalInformeCompleto.style.display = 'none';

            document.getElementById('closeModalInforme').addEventListener('click', cerrarModalSeleccionInforme);
            document.getElementById('btnCancelInforme').addEventListener('click', cerrarModalSeleccionInforme);
            document.getElementById('closeModalInformeCompleto').addEventListener('click', cerrarModalInformeCompleto);
            document.getElementById('btnCerrarInformeCompleto').addEventListener('click', cerrarModalInformeCompleto);

            // Abrir modal de selección al pulsar "Generar Informe"
            document.getElementById('btnGenerarInforme').addEventListener('click', function() {
                // Poblar selects de mes y año
                const meses = {
                    1: 'Enero', 2: 'Febrero', 3: 'Marzo', 4: 'Abril', 5: 'Mayo', 6: 'Junio',
                    7: 'Julio', 8: 'Agosto', 9: 'Septiembre', 10: 'Octubre', 11: 'Noviembre', 12: 'Diciembre'
                };
                const mesActual = new Date().getMonth() + 1;
                const anioActual = new Date().getFullYear();

                let mesOptions = '<option value="">Elija un mes</option>';
                for (let m in meses) {
                    const sel = (m == mesActual) ? 'selected' : '';
                    mesOptions += '<option value="' + m + '" ' + sel + '>' + meses[m] + '</option>';
                }
                document.getElementById('informe-mes').innerHTML = mesOptions;

                let anioOptions = '<option value="">Elija un año</option>';
                for (let a = 2019; a <= anioActual; a++) {
                    const sel = (a == anioActual) ? 'selected' : '';
                    anioOptions += '<option value="' + a + '" ' + sel + '>' + a + '</option>';
                }
                document.getElementById('informe-anio').innerHTML = anioOptions;

                modalSeleccionInforme.style.display = 'flex';
            });

            // Enviar selección y cargar informe
            document.getElementById('btnEnviarInforme').addEventListener('click', function() {
                const mes = document.getElementById('informe-mes').value;
                const anio = document.getElementById('informe-anio').value;

                if (!mes || !anio) {
                    alert('Por favor, selecciona tanto el mes como el año antes de continuar.');
                    return;
                }

                const informeCompleto = document.getElementById('informe-completo').checked ? 1 : 0;
                const url = '/eunomia/fichajes/informeHorasEmpleadoMes/' + userId + '/' + mes + '/' + anio + '/' + informeCompleto;

                // Cerrar modal de selección y abrir modal de informe con loading
                cerrarModalSeleccionInforme();
                document.getElementById('informe-content-completo').innerHTML = '<div style="text-align:center;"><p>Cargando informe...</p></div>';
                modalInformeCompleto.style.display = 'flex';

                // Cargar contenido via AJAX
                fetch(url, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function(response) {
                    if (!response.ok) throw new Error('Error ' + response.status);
                    return response.text();
                })
                .then(function(html) {
                    document.getElementById('informe-content-completo').innerHTML = html;

                    // Los <script> del HTML inyectado no se ejecutan con innerHTML,
                    // así que lanzamos aquí la carga de la tabla de horas.
                    var token = document.querySelector('meta[name="csrf-token"]');
                    if (!token) token = document.querySelector('input[name="_token"]');
                    var csrfValue = token ? (token.content || token.value) : '';

                    var formData = new FormData();
                    formData.append('_token', csrfValue);
                    formData.append('intervalo', 'mes');
                    formData.append('anio', anio);
                    formData.append('mes', mes);
                    formData.append('user_id', userId);
                    formData.append('informe_completo', informeCompleto ? 'true' : 'false');

                    fetch('/eunomia/fichajes/muestraTablaTiempoTrabajado', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(function(r) {
                        if (!r.ok) throw new Error('Error ' + r.status);
                        return r.text();
                    })
                    .then(function(tablaHtml) {
                        var tbody = document.getElementById('tabla_horas_trabajadas_mes');
                        if (tbody) tbody.innerHTML = tablaHtml;
                    })
                    .catch(function(err) {
                        console.error('Error cargando tabla de horas:', err);
                        var tbody = document.getElementById('tabla_horas_trabajadas_mes');
                        if (tbody) tbody.innerHTML = '<tr><td class="text-danger" colspan="13">Error al cargar datos de fichajes.</td></tr>';
                    });
                })
                .catch(function(error) {
                    console.error('Error cargando informe:', error);
                    document.getElementById('informe-content-completo').innerHTML =
                        '<div style="color:red; padding:15px;">Error al cargar el informe. Revise la consola o los logs del servidor.</div>';
                });
            });

            // Botón imprimir informe
            document.getElementById('btnImprimirInforme').addEventListener('click', function() {
                const content = document.getElementById('informe-content-completo').innerHTML;
                const printWindow = window.open('', '_blank', 'width=1000,height=800');
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Informe de Horas</title>
                        <style>
                            * { box-sizing: border-box; }
                            body { font-family: Arial, sans-serif; margin: 0; padding: 15px; background: white; font-size: 12px; line-height: 1.4; }
                            .text-center { text-align: center; }
                            .content { padding: 10px; font-size: 11px; }
                            [class*="col-lg-"] { float: left; padding: 0 8px; }
                            .col-lg-12 { width: 100%; }
                            .col-lg-3 { width: 25%; }
                            .col-lg-4 { width: 33.333%; }
                            .col-lg-5 { width: 41.666%; }
                            .col-md-10 { width: 83.333%; }
                            .box { border: 1px solid #ddd; border-radius: 4px; margin-bottom: 15px; }
                            .box-body { padding: 10px; }
                            img { max-width: 100%; height: auto; }
                            .form-group { margin-bottom: 8px; font-size: 11px; }
                            strong { font-weight: bold; }
                            p { font-size: 10px; margin: 8px 0; }
                            .informe-wrap { max-width: 100%; overflow-x: auto; }
                            #list2 { width: 100%; table-layout: fixed; border-collapse: collapse; margin-bottom: 15px; font-size: 9px; }
                            #list2 th, #list2 td { padding: 2px 1px !important; font-size: 8px; box-sizing: border-box; vertical-align: middle; text-align: center; white-space: nowrap; border: 1px solid #ddd !important; line-height: 1.2; }
                            #list2 thead th { background: #f2f2f2 !important; font-size: 9px; }
                            col.col-dia { width: 12.5%; }
                            col.col-hora { width: 7%; }
                            col.col-total { width: 10.5%; }
                            .col-dia, .col-total { white-space: normal; font-size: 7px; }
                            #list2 td.celda-hora { padding: 0 !important; background: #fff !important; }
                            table[border="1"] { border-collapse: collapse !important; border: 1px solid #ddd !important; font-size: 8px; }
                            table[border="1"] th, table[border="1"] td { border: 1px solid #ddd !important; font-size: 8px; padding: 3px !important; }
                            table[border="1"] thead th { background: #F2F2F2 !important; border: 1px solid #ddd !important; text-align: center !important; vertical-align: middle !important; padding: 4px !important; font-size: 9px !important; }
                            .barra-tiempo { width: 100%; height: 16px; min-height: 16px; border-radius: 2px; display: block; }
                            .barra-tiempo.is-vacio { background: #FFFFFF; border: 1px solid #ddd; }
                            .barra-tiempo.is-pasado { background: #008D4C; }
                            .barra-tiempo.is-ahora { background: #00C0EF; }
                            @media print {
                                body { margin: 0; padding: 8px; font-size: 10px; }
                                .content { padding: 5px; }
                                [class*="col-lg-"] { padding: 0 3px; }
                                #list2 th, #list2 td { font-size: 7px; padding: 1px !important; }
                                .col-dia, .col-total { font-size: 6px !important; }
                                .form-group { font-size: 10px; }
                                p { font-size: 9px; }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="content">${content}</div>
                    </body>
                    </html>
                `);
                printWindow.document.close();
                printWindow.focus();
                setTimeout(function() {
                    printWindow.print();
                    printWindow.close();
                }, 500);
            });
        });
    </script>
@stop
