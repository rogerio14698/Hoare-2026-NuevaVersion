@extends('adminlte::page')

@section('content_header')
  <div class="alinearHeader">
    <h1 class="fuenteTitulo">Calendario de Tareas</h1>
    <a href="{{ route('tasks.create') }}" class="btn-modificar"> Añadir Tarea</a>
  </div>
@stop

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card-body bgUsuario">
        <div class="card-body">
          <div class="mb-3" style="max-width: 350px;">
            <select name="usuarios" id="usuarios" class="form-control" onchange="const val = this.value; if (val) { window.location = '{{ route('calendar.user', ['user' => '__USER__']) }}'.replace('__USER__', val); } else { window.location = '{{ route('calendar.index') }}'; }">
              <option value="">Selecciona un usuario...</option>
              @foreach ($users as $user)
                <option {{($user->id == $usuario)?'selected':''}} value="{{$user->id}}">{{$user->name}}</option>
              @endforeach
            </select>
          </div>
          <!-- THE CALENDAR -->
          <div id="calendar"></div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('css')

  <!-- fullCalendar -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.print.css" media="print">



  <style>
    /* Reducir tamaño del calendario */
    #calendar {
      max-width: 1200px;
      margin: 0 auto;
      font-size: 0.9rem;
      padding: 25px;
    }
    
    /* Solo borde exterior en el contenedor, no en las tablas internas */
    .fc-view-container {
      border: 0.5px solid #ddd !important;
    }
    
    .fc-toolbar h2 {
      font-size: 1.5rem;
    }
    
    .fc-button {
      padding: 0.25rem 0.5rem;
      font-size: 0.875rem;
    }
    
    .fc-day-header {
      padding: 0.5rem 0;
      font-size: 0.875rem;
    }
    
    .fc-day-number {
      padding: 0.25rem;
      font-size: 0.875rem;
    }
    
    .fc-event {
      font-size: 0.85rem;
      padding: 2px 4px;
    }
    
    .fc-time {
      font-size: 0.8rem;
    }
  </style>

@stop

@section('js')

  <!-- fullCalendar -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.js"></script>
  <script src="{{asset("vendor/adminlte/plugins/fullcalendar/locale/es.js")}}"></script>

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

        @foreach ($tasks as $task)
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
          backgroundColor: "red",
          borderColor: "red",
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
      eventRender: function(event, element) {
          var title = element.find( '.fc-title' );
          title.html( title.text() );
      },
      eventDrop: function(event, delta, revertFunc) {
          var title = event.title;
          var start = event.start.format('Y-M-D HH:mm:ss');
          var end = (event.end == null) ? start : event.end.format('Y-M-D HH:mm:ss');
          var id = event.id;
          var solomios = 1;
          var _token = $("input[name='_token']").val() // Token generado en el campo de arriba para los formularios de Laravel (CSRF Protection)
          $.ajax({
              url: "{{route('edit_Calendar')}}",
              data: 'title='+title+'&start='+start+'&end='+end+'&eventid='+id+'&_token='+_token+'&solomios='+solomios,
              type: 'POST',
              success: function(response){
                  if(response != '')
                      revertFunc();
              },
              error: function( jqXHR, textStatus ) {
                  console.log(jqXHR.responseText);
                  revertFunc();
              }
          });
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




<!-- fin calendario-->
@stop
