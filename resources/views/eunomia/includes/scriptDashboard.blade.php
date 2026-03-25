@section('js')

    <!-- page script -->

    <!-- fullCalendar 2.2.5 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.js"></script>

    <script src="{{asset("vendor/adminlte/plugins/fullcalendar/locale/es.js")}}"> </script>

    <!-- DataTables -->
    <script src="{{asset("vendor/adminlte/plugins/datatables/jquery.dataTables.min.js")}}"> </script>
    <script src="{{asset("vendor/adminlte/plugins/datatables/dataTables.bootstrap.min.js")}}"> </script>

<!-- calendario -->

<script>
    $(document).ready(function(){
        
        // === ABRIR MODAL Y CARGAR DATOS ===
        $(document).on('click', '.edit-btn', function (e) {
            e.preventDefault();
            var taskId = $(this).data('id');
            var $form = $('#form_edit');

            $.post("{{ route('todo.edit') }}", {
                id: taskId,
                _token: "{{ csrf_token() }}"
            })
                .done(function (msg) {
                    // Asignar el ID al campo oculto
                    $('#edit_task_id').val(taskId);
                    $('#titulo_tarea_edit').val(msg.titulo_tarea || '');
                    $('#fechaentrega_tarea_edit').val(msg.fecha || '');
                    $('#horaentrega_tarea_edit').val(msg.hora || '');
                    $('#comentario_tarea_edit').val(msg.comentario_tarea || '');
                    $('#editModal').modal('show');
                })
                .fail(function (xhr) {
                    alert('Error cargando la tarea para edición.');
                });
        });

        // === BOTÓN CANCELAR ===
        $(document).on('click', '#btn-cancelar-edit', function (e) {
            e.preventDefault();
            $('#editModal').modal('hide');
        });

        $(document).on('click', '#btn-cancelar-new', function (e) {
            e.preventDefault();
            $('#newModal').modal('hide');
        });

        $(document).on('click', '#btn-cancelar-delete', function (e) {
            e.preventDefault();
            $('#deleteModal').modal('hide');
        });

        // === ENVIAR FORMULARIO (AJAX) ===
        $('#form_edit').on('submit', function (e) {
            e.preventDefault();
            var $form = $(this);

            $.post("{{ route('todo.update') }}", $form.serialize())
                .done(function (r) {
                    if (!r || !r.ok) {
                        alert('No se pudo guardar la tarea.');
                        return;
                    }
                    var d = r.data || {};
                    var id = d.id;

                    // 1) Actualizar título en la lista
                    var $li = $('li[data-id="' + id + '"]');
                    if ($li.length > 0) {
                        $li.find('.text').text(d.titulo_tarea || '');

                        // 2) Actualizar badge (texto y color)
                        var $label = $li.find('small.label');
                        $label.removeClass(function (i, c) { return (c.match(/(^|\s)bg-\S+/g) || []).join(' '); });
                        if (d.badge_class) $label.addClass(d.badge_class);
                        $label.html('<i class="fa fa-clock-o"></i> ' + (d.badge_text || ''));
                    }

                    $('#editModal').modal('hide');
                })
                .fail(function (xhr) {
                    alert('No se pudo guardar la tarea.');
                });
        });

        // === ELIMINAR: pasar id al modal de borrado ===
        $(document).on('click', '.delete_toggle', function () {
            $('#postvalue').val($(this).attr('rel'));
            $('#deleteModal').modal('show');
        });

        
        // Desde el modal de edición: pasar el id oculto al modal de borrado
        $(document).on('click', '#btn-delete-edit', function (e) {
            e.preventDefault();
            var id = $('#edit_task_id').val();
            $('#postvalue').val(id);
            $('#editModal').modal('hide');
            $('#deleteModal').modal('show');
        });
        
        // === RESTO DEL CÓDIGO ORIGINAL ===
        var target = document.getElementById('chat_messages');
        var spinner = new Spinner(opts).spin(target);
        //actualizaChat();
        {{-- @if(\Auth::user()->isRole('trabajador')) --}}
        $("#chat_messages").animate({ scrollTop: $("#chat_messages").prop('scrollHeight')}, 1000); // Scrollea hasta abajo del div, el id debe ser del div que tiene la barra de scrolleo
        cargaComentarios();
        recargaTiempoTrabajado();
        muestraTablaTiempoTrabajado();
        muestraTiempoTrabajadoSemana();
        {{-- @endif --}}
    });

    <!-- Spin ajax -->

    $(function () {


    /* initialize the calendar
     -----------------------------------------------------------------*/
    //Date for the calendar events (dummy data)
    var date = new Date();

    $('#calendar').fullCalendar({

      locale: "es",
      nextDayThreshold: '00:00:00',
      weekends: false,

      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
      },
      buttonText: {
        today: 'Hoy',
        month: 'mes',
        week: 'semana',
        day: 'dia'
      },

      //events
      events: [

        @foreach ($tareascalendario as $task)
        {
          id: '{{$task->id}}',
          title: '{{optional($task->project->customer)->codigo_cliente ?? 'TASK'}}_{{$task->titulo_tarea}}',
          start: '{{$task->fechainicio_tarea->format('Y-m-d H:i:s')}}',
          end: '{{$task->fechaentrega_tarea->format('Y-m-d H:i:s')}}',
          url: '/eunomia/tasks/{{$task->id}}/edit',
          @if ($task->estado_tarea == 1)
            backgroundColor: "#f39c12",
            borderColor: "#f39c12",
          @elseif ($task->estado_tarea == 2)
            backgroundColor: "#605ca8",
            borderColor: "#605ca8",
          @elseif ($task->estado_tarea == 3)
            backgroundColor: "#00a65a",
            borderColor: "#00a65a",
          @elseif ($task->estado_tarea == 4)
            backgroundColor: "#b2d2d1",
            borderColor: "#b2d2d1",
          @elseif ($task->estado_tarea == 5)
            backgroundColor: "#0073b7",
            borderColor: "#0073b7",
          @elseif ($task->estado_tarea == 6)
            backgroundColor: "#dd4b39",
            borderColor: "#dd4b39",
          @elseif ($task->estado_tarea == 7)
            backgroundColor: "#000000",
            borderColor: "#000000",
          @endif
          allDay: false
        },
        @endforeach

        @foreach($party_days as $party_day)
        {
          id: 'party_{{$party_day->id}}',
          title: '{{$party_day->name}}',
          start: '{{$party_day->date}}',
          end: '{{$party_day->date}}',
          backgroundColor: "#626568",
          borderColor: "#626568",
          allDay: true,
          editable: false
        },
        @endforeach

        @foreach($holiday_days as $holiday_day)
        {
          id: 'holiday_{{$holiday_day->id}}',
          title: 'Vacaciones {{$holiday_day->user->name}}',
          start: '{{$holiday_day->date}}',
          end: '{{$holiday_day->date}}',
          backgroundColor: "#BABEC1",
          borderColor: "#BABEC1",
          allDay: true,
          editable: false
        }@if(!$loop->last),@endif
        @endforeach

          // {
        //   title: 'Long Event',
        //   start: new Date(y, m, d - 5),
        //   end: new Date(y, m, d - 2),
        //   backgroundColor: "#f39c12", //yellow
        //   borderColor: "#f39c12" //yellow
        // },
        // {
        //   title: 'Meeting',
        //   start: new Date(y, m, d, 10, 30),
        //   allDay: false,
        //   backgroundColor: "#0073b7", //Blue
        //   borderColor: "#0073b7" //Blue
        // },
        // {
        //   title: 'Lunch',
        //   start: new Date(y, m, d, 12, 0),
        //   end: new Date(y, m, d, 14, 0),
        //   allDay: false,
        //   backgroundColor: "#00c0ef", //Info (aqua)
        //   borderColor: "#00c0ef" //Info (aqua)
        // },
        // {
        //   title: 'Birthday Party',
        //   start: new Date(y, m, d + 1, 19, 0),
        //   end: new Date(y, m, d + 1, 22, 30),
        //   allDay: false,
        //   backgroundColor: "#00a65a", //Success (green)
        //   borderColor: "#00a65a" //Success (green)
        // },
        // {
        //   title: 'Click for Google',
        //   start: new Date(y, m, 28),
        //   end: new Date(y, m, 29),
        //   url: 'http://google.com/',
        //   backgroundColor: "#3c8dbc", //Primary (light-blue)
        //   borderColor: "#3c8dbc" //Primary (light-blue)
        // }
      ],

      editable: true,
      eventDrop: function(event, delta, revertFunc) {
          var title = event.title;
          var start = event.start.format('Y-M-D HH:mm:ss');
          var end = (event.end == null) ? start : event.end.format('Y-M-D HH:mm:ss');
          var id = event.id;
          var _token = $("input[name='_token']").val() // Token generado en el campo de arriba para los formularios de Laravel (CSRF Protection)
          var solomios = 0;
          $.ajax({
              url: "{{route('edit_Calendar')}}",
              data: 'title='+title+'&start='+start+'&end='+end+'&eventid='+id+'&_token='+_token+'&solomios='+solomios,
              type: 'POST',
              success: function(response){
                  if(response != '')
                      revertFunc();
              },
              error: function( jqXHR, textStatus ) {
                  console.log(textStatus);
                  revertFunc();
              }
          });
      },
      eventRender: function(event, element) {
          var title = element.find( '.fc-title' );
          title.html( title.text() );
      },
      droppable: false, // this allows things to be dropped onto the calendar !!!
      drop: function (date, allDay) { // this function is called when something is dropped

        // retrieve the dropped element's stored Event Object
        var originalEventObject = $(this).data('eventObject');

        // we need to copy it, so that multiple events don't have a reference to the same object
        var copiedEventObject = $.extend({}, originalEventObject);

        // assign it the date that was reported
        copiedEventObject.start = date;
        copiedEventObject.allDay = date;
        copiedEventObject.backgroundColor = $(this).css("background-color");
        copiedEventObject.borderColor = $(this).css("border-color");

        // render the event on the calendar
        // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
        $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);

        // is the "remove after drop" checkbox checked?
        if ($('#drop-remove').is(':checked')) {
          // if so, remove the element from the "Draggable Events" list
          $(this).remove();
        }

      }
    });

    /* ADDING EVENTS */
    var currColor = "#3c8dbc"; //Red by default
    //Color chooser button
    var colorChooser = $("#color-chooser-btn");
    $("#color-chooser > li > a").click(function (e) {
      e.preventDefault();
      //Save color
      currColor = $(this).css("color");
      //Add color effect to button
      $('#add-new-event').css({"background-color": currColor, "border-color": currColor});
    });
    $("#add-new-event").click(function (e) {
      e.preventDefault();
      //Get value and make sure it is not null
      var val = $("#new-event").val();
      if (val.length == 0) {
        return;
      }

      //Create events
      var event = $("<div />");
      event.css({"background-color": currColor, "border-color": currColor, "color": "#fff"}).addClass("external-event");
      event.html(val);
      $('#external-events').prepend(event);

      //Add draggable funtionality
      ini_events(event);

      //Remove event from text input
      $("#new-event").val("");
    });
  });
</script>


    <script src="{{asset('js/spin.js')}}"></script>
    <script language="JavaScript">
        var opts = {
            lines: 12, // The number of lines to draw
            length: 41, // The length of each line
            width: 16, // The line thickness
            radius: 47, // The radius of the inner circle
            scale: 0.45, // Scales overall size of the spinner
            corners: 1, // Corner roundness (0..1)
            color: '#3C8DBC', // CSS color or array of colors
            fadeColor: 'transparent', // CSS color or array of colors
            opacity: 0.15, // Opacity of the lines
            rotate: 27, // The rotation offset
            direction: 1, // 1: clockwise, -1: counterclockwise
            speed: 0.9, // Rounds per second
            trail: 60, // Afterglow percentage
            fps: 20, // Frames per second when using setTimeout() as a fallback in IE 9
            zIndex: 2e9, // The z-index (defaults to 2000000000)
            className: 'spinner', // The CSS class to assign to the spinner
            top: '50%', // Top position relative to parent
            left: '50%', // Left position relative to parent
            position: 'absolute' // Element positioning
        };
        var target = document.getElementById('empresas');
        var spinner = new Spinner(opts).spin(target);

    </script>


<!-- fin calendario-->



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
        "pageLength": 50,
        "displayLength": 50,




      });
    });
  </script>


    <!-- Select2 -->
    <script src="{{asset('vendor/adminlte/plugins/select2/select2.full.min.js')}}"></script>


    <!-- bootstrap datepicker -->
    <script src="{{asset('vendor/adminlte/plugins/datepicker/bootstrap-datepicker.js')}}"></script>

    <!-- bootstrap time picker -->
    <script src="{{asset('vendor/adminlte/plugins/timepicker/bootstrap-timepicker.js')}}"></script>


    <!-- Languaje -->
    <script src="{{asset('vendor/adminlte/plugins/datepicker/locales/bootstrap-datepicker.es.js')}}"></script>

    <!-- Nestable -->
    <script src="{{asset("vendor/nestable/jquery.nestable.js")}}"> </script>

    <script type="text/javascript">
        //Date picker
        $('#fechaentrega_tarea').datepicker({
            autoclose: true,
            todayHighlight :true,
            weekStart : 1,
            language: 'es',
            format: "yyyy-mm-dd"
        });


        $('#fechaentrega_tarea2').datepicker({
            autoclose: true,
            todayHighlight :true,
            weekStart : 1,
            language: 'es',
            format: "yyyy-mm-dd"
        });

        // Datepicker para el modal del TodoTask
        $('#fechaentrega_tarea_new, #fechaentrega_tarea_edit, .datepicker').datepicker({
            format: 'dd/mm/yyyy',
            language: 'es',
            autoclose: true,
            todayHighlight: true,
            weekStart: 1
        });
    </script>

    <script type="text/javascript">
        //timepicker
        $('#horaentrega_tarea').timepicker(
            {
                showMeridian: false,
                showSeconds: false
            }
        );

        //timepicker
        $('#horaentrega_tarea2').timepicker(
            {
                showMeridian: false,
                showSeconds: false
            }
        );

        //timepicker para el modal del TodoTask
        $('#horaentrega_tarea_new, #horaentrega_tarea_edit').timepicker(
            {
                showMeridian: false,
                showSeconds: false
            }
        );


        $('.dd').nestable({
            dropCallback: function(details) {

                var order = new Array();
                $("li[data-id='"+details.destId +"']").find('ol:first').children().each(function(index,elem) {
                    order[index] = $(elem).attr('data-id');
                });

                if (order.length === 0){
                    var rootOrder = new Array();
                    $("#nestable > ol > li").each(function(index,elem) {
                        rootOrder[index] = $(elem).attr('data-id');
                    });
                }

                $.post('{{url("eunomia/todo/updateOrden")}}',
                    { source : details.sourceId,
                        destination: details.destId,
                        order:JSON.stringify(order),
                        rootOrder:JSON.stringify(rootOrder),
                        _token:$("input[name='_token']").val() // Token generado en el campo de arriba para los formularios de Laravel (CSRF Protection)
                    }, function(data) {
                        console.log('data '+data);
                    }).done(function() {
                    $( "#success-indicator" ).fadeIn(100).delay(1000).fadeOut();
                }).fail(function(data) { console.log('data '+data.responseText); }).always(function() {  });
            }
        });

        $('.delete_toggle').each(function(index,elem) {
            $(elem).click(function(e){
                e.preventDefault();
                $('#postvalue').attr('value',$(elem).attr('rel'));
                $('#deleteModal').modal('toggle');
            });
        });

        $('[data-toggle="tooltip"]').tooltip()
    </script>

    <script src="{{asset('js/DetectarVisibilidad.js')}}"></script>

    <script>
        //Funciones del chat

        /**
         * Comprueba si un elemento esta dentro de la pantalla
         *
         * @param elemento
         */
        function estaEnPantalla(elemento) {
            var estaEnPantalla = false;

            var posicionElemento = $(elemento).get(0).getBoundingClientRect();

            if (posicionElemento.top >= 0 && posicionElemento.left >= 0
                && posicionElemento.bottom <= (window.innerHeight || document.documentElement.clientHeight)
                && posicionElemento.right <= (window.innerWidth || document.documentElement.clientWidth)) {
                estaEnPantalla = true;
            }

            return estaEnPantalla;
        }

        /**
         * Comprueba si un elemento esta visible o no en la pantalla
         *
         * @param elemento
         */
        function esVisibleEnPantalla(elemento) {
            var esVisible = false;
            if ($(elemento).is(':visible') && $(elemento).css("visibility") != "hidden"
                && $(elemento).css("opacity") > 0 && estaEnPantalla(elemento)) {
                esVisible = true;
            }

            return esVisible;
        }

        // Chat removido - funcionalidad eliminada en Laravel 7
        setInterval('cargaComentarios()',300000);

        function cargaComentarios() {
            var target = document.getElementById('chat-card');
            var spinner = new Spinner(opts).spin(target);
            $.ajax({
                method: "POST",
                url: "{{route('cargaComentarios')}}",
                data: {
                    _token: $("input[name='_token']").val()
                },
                success: function (data) {
                    $('#chat-card').html(data);
                    $("#chat-card").animate({ scrollTop: $("#chat-card").prop('scrollHeight')}, 1000); // Scrollea hasta abajo del div, el id debe ser del div que tiene la barra de scrolleo
                },
                error: function (jqXHR, textStatus) {
                    console.log(jqXHR.responseText);
                }
            });
        }
    </script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <!-- Bootstrap Dialog -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/js/bootstrap-dialog.min.js"></script>

    <script language="JavaScript">
        @foreach($tareassemana as $task)
        @if($task->comments->count() > 0)
        $('#comm_{{$task->id}}').click(function(){
            // Crear modal usando Bootstrap 5 nativo en lugar de BootstrapDialog
            var modalHtml = '<div class="modal fade" id="modalComentarios-{{$task->id}}" tabindex="-1" role="dialog">' +
                '<div class="modal-dialog modal-lg" role="document">' +
                '<div class="modal-content">' +
                '<div class="modal-header" style="background-color: #3C8DBC; color: #FFF;">' +
                '<h5 class="modal-title">Comentarios tarea "{{$task->titulo_tarea}}"</h5>' +
                '<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '</div>' +
                '<div class="modal-body">' +
                '<div id="comments-content-{{$task->id}}">Cargando...</div>' +
                '</div>' +
                '<div class="modal-footer">' +
                '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';

            // Eliminar modal anterior si existe
            $('#modalComentarios-{{$task->id}}').remove();

            // Agregar modal al body
            $('body').append(modalHtml);

            // Cargar contenido
            $('#comments-content-{{$task->id}}').load('/eunomia/tasks/muestraComentarios/{{$task->id}}');

            // Mostrar modal
            $('#modalComentarios-{{$task->id}}').modal('show');

            // Limpiar cuando se cierre
            $('#modalComentarios-{{$task->id}}').on('hidden.bs.modal', function () {
                $(this).remove();
            });
        });
        @endif
        @endforeach

        @foreach($tareasmes as $task)
        @if($task->comments->count() > 0)
        $('#comm_{{$task->id}}').click(function(){
            // Crear modal usando Bootstrap 5 nativo en lugar de BootstrapDialog
            var modalHtml = '<div class="modal fade" id="modalComentarios-mes-{{$task->id}}" tabindex="-1" role="dialog">' +
                '<div class="modal-dialog modal-lg" role="document">' +
                '<div class="modal-content">' +
                '<div class="modal-header" style="background-color: #3C8DBC; color: #FFF;">' +
                '<h5 class="modal-title">Comentarios tarea "{{$task->titulo_tarea}}"</h5>' +
                '<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '</div>' +
                '<div class="modal-body">' +
                '<div id="comments-content-mes-{{$task->id}}">Cargando...</div>' +
                '</div>' +
                '<div class="modal-footer">' +
                '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';

            // Eliminar modal anterior si existe
            $('#modalComentarios-mes-{{$task->id}}').remove();

            // Agregar modal al body
            $('body').append(modalHtml);

            // Cargar contenido
            $('#comments-content-mes-{{$task->id}}').load('/eunomia/tasks/muestraComentarios/{{$task->id}}');

            // Mostrar modal
            $('#modalComentarios-mes-{{$task->id}}').modal('show');

            // Limpiar cuando se cierre
            $('#modalComentarios-mes-{{$task->id}}').on('hidden.bs.modal', function () {
                $(this).remove();
            });
        });
        @endif
        @endforeach

        @foreach($tareasparamastarde as $task)
        @if($task->comments->count() > 0)
        $('#comm_{{$task->id}}').click(function(){
            // Crear modal usando Bootstrap 5 nativo en lugar de BootstrapDialog
            var modalHtml = '<div class="modal fade" id="modalComentarios-tarde-{{$task->id}}" tabindex="-1" role="dialog">' +
                '<div class="modal-dialog modal-lg" role="document">' +
                '<div class="modal-content">' +
                '<div class="modal-header" style="background-color: #3C8DBC; color: #FFF;">' +
                '<h5 class="modal-title">Comentarios tarea "{{$task->titulo_tarea}}"</h5>' +
                '<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '</div>' +
                '<div class="modal-body">' +
                '<div id="comments-content-tarde-{{$task->id}}">Cargando...</div>' +
                '</div>' +
                '<div class="modal-footer">' +
                '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';

            // Eliminar modal anterior si existe
            $('#modalComentarios-tarde-{{$task->id}}').remove();

            // Agregar modal al body
            $('body').append(modalHtml);

            // Cargar contenido
            $('#comments-content-tarde-{{$task->id}}').load('/eunomia/tasks/muestraComentarios/{{$task->id}}');

            // Mostrar modal
            $('#modalComentarios-tarde-{{$task->id}}').modal('show');

            // Limpiar cuando se cierre
            $('#modalComentarios-tarde-{{$task->id}}').on('hidden.bs.modal', function () {
                $(this).remove();
            });
        });
        @endif
        @endforeach
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>

    <script language="JavaScript">
        var ctxtarea = document.getElementById('pieChartTareas').getContext('2d');
        var charttarea = new Chart(ctxtarea, {
            // The type of chart we want to create
            type: 'doughnut',

            // The data for our dataset
            data: {
                labels: {!! $labels !!},
                datasets: [{
                    label: "My First dataset",
                    backgroundColor: {!! $backgroundColors !!},
                    borderColor: '#FFF',
                    data: {!! $values !!},
                }]
            },

            // Configuration options go here
            options: {}
        });
        var ctxproyecto = document.getElementById('pieChartProyectos').getContext('2d');
        var chartproyecto = new Chart(ctxproyecto, {
            // The type of chart we want to create
            type: 'doughnut',

            // The data for our dataset
            data: {
                labels: {!! $labels !!},
                datasets: [{
                    label: "My First dataset",
                    backgroundColor: {!! $backgroundColors !!},
                    borderColor: '#FFF',
                    data: {!! $valuesP !!},
                }]
            },

            // Configuration options go here
            options: {}
        });

    </script>

    {{-- @if(\Auth::user()->isRole('trabajador')) --}}
    <script language="JavaScript">
        @if($ultimo_estado_fichaje == 'entrada')
            $('#btn_fichaje').addClass('btn-danger');
            $('#btn_fichaje').html('Check OUT');
            $('#btn_fichaje').prepend('<i class="fa fa-clock-o"></i> ');

        @else
            $('#btn_fichaje').addClass('btn-success');
            $('#btn_fichaje').html('Check IN');
            $('#btn_fichaje').prepend('<i class="fa fa-clock-o"></i> ');
        @endif

        $('#btn_fichaje').click(function(){
            var boton = $(this);
            if (boton.hasClass('btn-danger')) {
                $.ajax({
                    method: "POST",
                    url: "{{route('fichajes.store')}}",
                    data: {
                        _token: $("input[name='_token']").val()
                    },
                    success: function (data) {
                        document.location.reload();
                    },
                    error: function (jqXHR, textStatus) {
                        console.log(jqXHR.responseText);
                        // Manejar error de validación
                        if (jqXHR.status === 422) {
                            var response = JSON.parse(jqXHR.responseText);
                            alert(response.error);
                        } else {
                            alert('Error al registrar fichaje');
                        }
                    }
                });
            } else {
                // Crear modal usando Bootstrap 5 nativo en lugar de BootstrapDialog
                var modalHtml = '<div class="modal fade" id="modalFichaje" tabindex="-1" role="dialog">' +
                    '<div class="modal-dialog modal-dialog-centered" role="document">' +
                    '<div class="modal-content">' +
                    '<div class="modal-header" style="background-color: #3C8DBC; color: #FFF;">' +
                    '<h5 class="modal-title">Establece hora fichaje</h5>' +
                    '<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                    '</div>' +
                    '<div class="modal-body">' +
                    '<div id="fichaje-content">Cargando...</div>' +
                    '</div>' +
                    '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';

                // Eliminar modal anterior si existe
                $('#modalFichaje').remove();

                // Agregar modal al body
                $('body').append(modalHtml);

                // Cargar contenido
                $('#fichaje-content').load('{{route('estableceHoraFichaje')}}');

                // Mostrar modal
                $('#modalFichaje').modal('show');

                // Limpiar cuando se cierre
                $('#modalFichaje').on('hidden.bs.modal', function () {
                    $(this).remove();
                });
            }
        });

        $('#informe_horas_empleado_mes').click(function(){
            var boton = $(this);
            // Crear modal usando Bootstrap 5 nativo con contenido inline
            var meses = {
                1: 'Enero', 2: 'Febrero', 3: 'Marzo', 4: 'Abril', 5: 'Mayo', 6: 'Junio',
                7: 'Julio', 8: 'Agosto', 9: 'Septiembre', 10: 'Octubre', 11: 'Noviembre', 12: 'Diciembre'
            };
            var anios = [];
            for (var i = 2019; i <= new Date().getFullYear(); i++) {
                anios.push(i);
            }

            var mesOptions = '<option value="">Elija un mes</option>';
            for (var mes in meses) {
                var selected = (mes == new Date().getMonth() + 1) ? 'selected' : '';
                mesOptions += '<option value="' + mes + '" ' + selected + '>' + meses[mes] + '</option>';
            }

            var anioOptions = '<option value="">Elija un año</option>';
            for (var j = 0; j < anios.length; j++) {
                var selected = (anios[j] == new Date().getFullYear()) ? 'selected' : '';
                anioOptions += '<option value="' + anios[j] + '" ' + selected + '>' + anios[j] + '</option>';
            }

            var modalHtml = '<div class="modal fade" id="modalInforme" tabindex="-1" role="dialog">' +
                '<div class="modal-dialog modal-dialog-centered" role="document">' +
                '<div class="modal-content">' +
                '<div class="modal-header" style="background-color: #3C8DBC; color: #FFF;">' +
                '<h5 class="modal-title">Elije Año y Mes del fichaje</h5>' +
                '<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '</div>' +
                '<div class="modal-body">' +
                '<div id="informe-content">' +
                '<form id="form-informe">' +
                '<div class="form-group">' +
                '<label for="mes">Mes</label>' +
                '<select name="mes" id="mes" class="form-control" required>' + mesOptions + '</select>' +
                '</div>' +
                '<div class="form-group">' +
                '<label for="anio">Año</label>' +
                '<select name="anio" id="anio" class="form-control" required>' + anioOptions + '</select>' +
                '</div>' +
                '<div class="form-group">' +
                '<div class="form-check">' +
                '<input type="checkbox" name="informe_completo" id="informe_completo" class="form-check-input" value="1" checked>' +
                '<label class="form-check-label" for="informe_completo"> Informe completo</label>' +
                '</div>' +
                '</div>' +
                '<button type="button" id="boton_envia_mes_anio_fichaje" class="btn btn-info">Enviar</button>' +
                '</form>' +
                '</div>' +
                '</div>' +
                '<div class="modal-footer">' +
                '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';

            // Eliminar modal anterior si existe
            $('#modalInforme').remove();

            // Agregar modal al body
            $('body').append(modalHtml);

            // Mostrar modal
            $('#modalInforme').modal('show');

            // Configurar el evento del botón después de que el modal esté en el DOM
            $('#modalInforme').on('shown.bs.modal', function() {
                $('#boton_envia_mes_anio_fichaje').click(function(){
                    var mesSeleccionado = $('#mes').val();
                    var anioSeleccionado = $('#anio').val();

                    if (!mesSeleccionado || !anioSeleccionado) {
                        alert('Por favor, selecciona tanto el mes como el año antes de continuar.');
                        return;
                    }

                    // Cerrar el modal de selección y abrir el informe en un modal grande
                    $('#modalInforme').modal('hide');

                    var informeCompleto = $('#informe_completo').prop('checked') ? 1 : 0;
                    var url = '/eunomia/fichajes/informeHorasEmpleadoMes/{{\Auth::user()->id}}/' + mesSeleccionado + '/' + anioSeleccionado + '/' + informeCompleto;

                    // Abrir el informe en un modal grande
                    var modalInformeHtml = '<div class="modal fade" id="modalInformeCompleto" tabindex="-1" role="dialog">' +
                        '<div class="modal-dialog modal-xl" role="document">' +
                        '<div class="modal-content">' +
                        '<div class="modal-header" style="background-color: #3C8DBC; color: #FFF;">' +
                        '<h5 class="modal-title">Informe de Horas</h5>' +
                        '<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div>' +
                        '<div class="modal-body" id="informe-content-completo" style="max-height: 70vh; overflow-y: auto;">' +
                        '<div style="text-align: center;"><img src="{{asset('images/carga.gif')}}"></div>' +
                        '</div>' +
                        '<div class="modal-footer">' +
                        '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>' +
                        '<button type="button" class="btn btn-primary" id="btn-imprimir-informe">Imprimir</button>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>';

                    // Eliminar modal anterior si existe
                    $('#modalInformeCompleto').remove();

                    // Agregar modal al body
                    $('body').append(modalInformeHtml);

                    // Mostrar modal
                    $('#modalInformeCompleto').modal('show');

                    // Cargar contenido del informe con timeout y manejo de errores
                    $.ajax({
                        url: url,
                        method: 'GET',
                        timeout: 15000, // 15s
                        success: function(html) {
                            // Insertar HTML y ejecutar scripts incluidos (como los que llaman a muestraTablaTiempoTrabajadoMes)
                            try {
                                var parsed = $.parseHTML(html, document, true); // keep scripts
                                var $container = $('<div></div>').append(parsed);

                                // Extraer y ejecutar scripts (src y inline)
                                $container.find('script').each(function() {
                                    var src = $(this).attr('src');
                                    if (src) {
                                        // Cargar script externo y esperar (no bloqueante)
                                        $.getScript(src).fail(function() {
                                            console.error('Failed to load script:', src);
                                        });
                                    } else {
                                        // Ejecutar script inline
                                        try { $.globalEval($(this).text()); } catch (e) { console.error('Script eval error', e); }
                                    }
                                });

                                // Insertar el contenido sin los scripts en el DOM
                                $container.find('script').remove();
                                $('#informe-content-completo').html($container.html());
                            } catch (e) {
                                console.error('Error parsing informe HTML', e);
                                $('#informe-content-completo').html('<div class="alert alert-danger">Error al procesar el informe.</div>');
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('Error loading informe:', textStatus, errorThrown, jqXHR && jqXHR.responseText);
                            var msg = 'Error al cargar el informe. Revise la consola o los logs del servidor. Muy Posible que el informe esté vacio';
                            if (textStatus === 'timeout') msg = 'Timeout al cargar el informe (tarda demasiado).';
                            $('#informe-content-completo').html('<div class="alert alert-danger">' + msg + '</div>');
                        }
                    });

                    // Limpiar cuando se cierre
                    $('#modalInformeCompleto').on('hidden.bs.modal', function () {
                        $(this).remove();
                    });

                    // Configurar el botón de imprimir después de que el modal esté listo
                    $('#modalInformeCompleto').on('shown.bs.modal', function() {
                        $('#btn-imprimir-informe').click(function() {
                            // Crear una nueva ventana con solo el contenido del informe
                            var printWindow = window.open('', '_blank', 'width=1000,height=800');
                            var content = $('#informe-content-completo').html();

                            printWindow.document.write(`
                                <!DOCTYPE html>
                                <html>
                                <head>
                                    <title>Informe de Horas</title>
                                    <style>
                                        /* Reset básico */
                                        * { box-sizing: border-box; }
                                        body { font-family: Arial, sans-serif; margin: 0; padding: 15px; background: white; font-size: 12px; line-height: 1.4; }

                                        /* Utilidades */
                                        .text-center { text-align: center; }

                                        /* Layout del informe */
                                        .content { padding: 10px; font-size: 11px; }
                                        [class*="col-lg-"] { float: left; padding: 0 8px; }

                                        /* Sistema de columnas Bootstrap-like */
                                        .col-lg-12 { width: 100%; }
                                        .col-lg-3 { width: 25%; }
                                        .col-lg-4 { width: 33.333%; }
                                        .col-lg-5 { width: 41.666%; }
                                        .col-md-10 { width: 83.333%; }

                                        /* Box styling */
                                        .box { border: 1px solid #ddd; border-radius: 4px; margin-bottom: 15px; }
                                        .box-body { padding: 10px; }

                                        /* Logo */
                                        img { max-width: 100%; height: auto; }

                                        /* Texto del informe */
                                        .form-group { margin-bottom: 8px; font-size: 11px; }
                                        strong { font-weight: bold; }
                                        p { font-size: 10px; margin: 8px 0; }

                                        /* Contenedor con scroll horizontal */
                                        .informe-wrap { max-width: 100%; overflow-x: auto; }

                                        /* Tabla compacta y columnas fijas */
                                        #list2 { width: 100%; table-layout: fixed; border-collapse: collapse; margin-bottom: 15px; font-size: 9px; }
                                        #list2 th, #list2 td {
                                          padding: 2px 1px !important;
                                          font-size: 8px;
                                          box-sizing: border-box;
                                          vertical-align: middle;
                                          text-align: center;
                                          white-space: nowrap;
                                          border: 1px solid #ddd !important;
                                          line-height: 1.2;
                                        }

                                        /* Encabezado gris */
                                        #list2 thead th { background: #f2f2f2 !important; font-size: 9px; }

                                        /* Anchos fijos por colgroup */
                                        col.col-dia { width: 12.5%; }
                                        col.col-hora { width: 7%; }
                                        col.col-total { width: 10.5%; }

                                        /* Día / Total: que no se rompan y fuente más pequeña */
                                        .col-dia, .col-total { white-space: normal; font-size: 7px; }

                                        /* Celdas de horas */
                                        #list2 td.celda-hora { padding: 0 !important; background: #fff !important; }

                                        /* Estilos específicos para la tabla de fichajes */
                                        table[border="1"] {
                                            border-collapse: collapse !important;
                                            border: 1px solid #ddd !important;
                                            font-size: 8px;
                                        }

                                        table[border="1"] th,
                                        table[border="1"] td {
                                            border: 1px solid #ddd !important;
                                            font-size: 8px;
                                            padding: 3px !important;
                                        }

                                        /* Asegurar que los encabezados tengan el estilo correcto */
                                        table[border="1"] thead th {
                                            background: #F2F2F2 !important;
                                            border: 1px solid #ddd !important;
                                            text-align: center !important;
                                            vertical-align: middle !important;
                                            padding: 4px !important;
                                            font-size: 9px !important;
                                        }

                                        /* Estilos de barras de tiempo */
                                        .barra-tiempo {
                                            width: 100%;
                                            height: 16px;
                                            min-height: 16px;
                                            border-radius: 2px;
                                            display: block;
                                        }

                                        .barra-tiempo.is-vacio {
                                            background: #FFFFFF;
                                            border: 1px solid #ddd;
                                        }

                                        .barra-tiempo.is-pasado {
                                            background: #008D4C;
                                        }

                                        .barra-tiempo.is-ahora {
                                            background: #00C0EF;
                                        }

                                        /* Media queries para impresión */
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
                                    <div class="content">
                                        ${content}
                                    </div>
                                </body>
                                </html>
                            `);

                            printWindow.document.close();
                            printWindow.focus();

                            // Esperar un poco para que se cargue el contenido y luego imprimir
                            setTimeout(function() {
                                printWindow.print();
                                printWindow.close();
                            }, 500);
                        });
                    });
                });
            });

            // Limpiar cuando se cierre
            $('#modalInforme').on('hidden.bs.modal', function () {
                $(this).remove();
            });
        });

        function recargaTiempoTrabajado(){
            $.ajax({
                method: "POST",
                url: "{{route('recargaTiempoTrabajado')}}",
                data: {
                    estado: '{{$ultimo_estado_fichaje}}',
                    _token: $("input[name='_token']").val()
                },
                success: function (data) {
                    $('#tiempo_trabajado').html(data);
                },
                error: function (jqXHR, textStatus) {
                    console.log(jqXHR.responseText);
                }
            });
        }

        function muestraTablaTiempoTrabajado(){
            $.ajax({
                method: "POST",
                url: "{{route('muestraTablaTiempoTrabajado')}}",
                data: {
                    _token: $("input[name='_token']").val(),
                    intervalo: 'semana'
                },
                success: function (data) {
                    $('#tabla_horas_trabajadas').html(data);
                    
                    $('.btneditarfichaje').on('click', function(e){
                        e.preventDefault();
                        e.stopPropagation();
                        var boton = $(this);
                        var fichajeId = boton.attr('id').split('_')[1];
                        
                        // Crear modal usando Bootstrap 5 nativo
                        var modalHtml = '<div class="modal fade" id="modalEditarFichaje" tabindex="-1" role="dialog">' +
                            '<div class="modal-dialog modal-dialog-centered" role="document">' +
                            '<div class="modal-content">' +
                            '<div class="modal-header bg-primary">' +
                            '<h5 class="modal-title text-white">Modifica hora fichaje</h5>' +
                            '<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span>' +
                            '</button>' +
                            '</div>' +
                            '<div class="modal-body">' +
                            '<div id="contenidoModalFichaje">Cargando...</div>' +
                            '</div>' +
                            '<div class="modal-footer">' +
                            '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>';
                        
                        // Eliminar modal anterior si existe
                        $('#modalEditarFichaje').remove();
                        
                        // Agregar modal al body
                        $('body').append(modalHtml);
                        
                        // Cargar contenido
                        $('#contenidoModalFichaje').load('{{url("eunomia/fichajes/modificaHoraFichaje")}}/' + fichajeId);
                        
                        // Mostrar modal
                        $('#modalEditarFichaje').modal('show');
                        
                        // Limpiar cuando se cierre
                        $('#modalEditarFichaje').on('hidden.bs.modal', function () {
                            $(this).remove();
                        });
                    });
                    @for($i=0;$i<5;$i++)
                        $(".btn{{$i}}").collapse();
                        $("#demo{{$i}}").on("hide.bs.collapse", function(){
                            $(".btn{{$i}}").html('<i class="fa fa-plus"></i>');
                        });
                        $("#demo{{$i}}").on("show.bs.collapse", function(){
                            $(".btn{{$i}}").html('<i class="fa fa-minus"></i>');
                        });
                    @endfor
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Error loading table: ' + textStatus);
                }
            });
        }

        function muestraTiempoTrabajadoSemana(){
            $.ajax({
                method: "POST",
                url: "{{route('muestraTiempoTrabajadoSemana')}}",
                data: {
                    _token: $("input[name='_token']").val()
                },
                success: function (data) {
                    $('#tiempo_trabajado_semana').html(data);
                },
                error: function (jqXHR, textStatus) {
                    console.log(jqXHR.responseText);
                }
            });
        }


        setInterval('recargaTiempoTrabajado()',60000);
        // Comentado: setInterval('muestraTablaTiempoTrabajado()',60000); // Demasiado pesado para ejecutar cada minuto
        setInterval('muestraTiempoTrabajadoSemana()',60000);
    </script>
    {{-- @endif --}}

@stop