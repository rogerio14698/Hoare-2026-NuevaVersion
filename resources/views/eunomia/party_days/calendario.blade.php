<div class="row">
    <div class="col-md-12">
        <div class="box box">
            <div class="box-body no-padding">
                <!-- THE CALENDAR -->
                <div id="calendar"></div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /. box -->
    </div>

</div>
    <!-- calendario -->

    <script>
        $('document').ready(function () {
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

                    @foreach ($party_days as $party_day)
                    {

                        id: '{{$party_day->id}}',
                        title: '{{$party_day->name}}',
                        start: '{{$party_day->date}}',
                        end: '{{$party_day->date}}',
                        url: '',
                        @if ($party_day->date_type == 'Nacional')
                        backgroundColor: "#a39c12", //
                        borderColor: "#a39c12", //red
                        @elseif ($party_day->date_type == 'Autonómica')
                        backgroundColor: "#a05ca8 ", //red
                        borderColor: "#a05ca8 ", //red
                        @elseif ($party_day->date_type == 'Local')
                        backgroundColor: "#e0a65a ", //red
                        borderColor: "#e0a65a ", //red
                        @endif
                        allDay: false,
                    },
                    @endforeach
                ],

                editable: true,
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