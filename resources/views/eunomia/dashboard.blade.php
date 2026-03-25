 @extends('adminlte::page')

 @section('title', 'Horae | Dashboard')
 @section('css')


     <!-- fullCalendar 2.2.5-->
     <link rel="stylesheet"
         href="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.css') }}">
     <link rel="stylesheet"
         href="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.print.css') }}"
         media="print">

     <!-- DataTables -->
     <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables/dataTables.bootstrap.css') }}">

     <!-- Nestable -->
     <link rel="stylesheet" href="{{ asset('vendor/nestable/nestable.css') }}">

     <!-- Timepicker -->
     <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/timepicker/bootstrap-timepicker.css') }}">

     <!-- Bootstrap Dialog -->
     <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/css/bootstrap-dialog.min.css"
         rel="stylesheet" type="text/css" />


     <style>
         /* Solo borde exterior en el contenedor del calendario del dashboard */
         #calendar .fc-view-container {
             border: 0.5px solid #ddd !important;
         }
     </style>

    <!-- NewsFeed styles -->
    <link rel="stylesheet" href="{{ asset('css/nuevoCSS/newFeed.css') }}">

 @stop
 @section('content_header')

 @stop



 @section('content')
     <!--Aqui validamos el rol del usuario -->
     @if (!\Auth::user()->isRole('cliente'))
         

         <!--Main dashboard -->
         <div class="main-dashboard">
             <div class="tarea-dashboard movil">
                 <!--Aqui se incluyen las tareas -->
                @include('eunomia.includes.tareasNew')
             </div>

             <div class="calendario-dashboard">
                <!-- Aqui se incluye el fichaje -->
                @include('eunomia.includes.fichajes')
                 <!--Aqui se incluye el calendario -->
                 @include('eunomia.includes.calendario')

                 <!--Aqui se incluyen las vacaciones -->
                 @include('eunomia.includes.holidays')
             </div>

             <div class="toDo-dashboard movil">
                 <div class="toDo-lista movil">
                     @include('eunomia.includes.todo')
                 </div>

                 <div class="newfeed-dashboard">
                    @include('eunomia.includes.newFeed')
                     
                 </div>
             </div>
         </div>
     @endif

     {{-- CARD DE EMPLEADOS - NO REQUERIDO NI PARA MOVIL NI WEB 
                        <div class="card card-danger card-outline movil">
                            <div class="card-header">
                                <h3 class="card-title">Emplead@s</h3>

                                <div class="card-tools">
                                    <span class="badge badge-danger">{{$users->count()}} Emplead@s</span>
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- /.card-header 
                            <div class="card-body p-0">
                                <ul class="users-list clearfix">
                                    @foreach ($usuarios as $usuario)
                                    <li>
                                        <img src="{{ $usuario->avatar_url }}" width="112" alt="{{ $usuario->nombre_completo }}">
                                        <a class="users-list-name" href="#">{{$usuario->nombre_completo}}</a>
                                        <span class="users-list-date">{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $usuario->created_at)->format('d/m/Y')}}</span>
                                    </li>
                                    @endforeach
                                </ul>
                                <!-- /.users-list -->
                            </div>
                            <!-- /.card-body -->
                        </div>
                        --}}


 @stop
 <!--Aqui incluimos todo el script del dashboard que se encuentra en la carpeta de includes -->
 @include('eunomia.includes.scriptDashboard')
