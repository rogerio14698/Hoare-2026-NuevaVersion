@php
    $user = Auth::user();
    $userName = $user ? $user->name : 'Invitado';
    $userPhoto = ($user && $user->avatar) ? '/images/avatar/' . $user->avatar : 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=0F1C26&color=fff&size=128';
    
    // Contadores para el menú (corregido para usar la relación correcta y el nombre de columna correcto)
    $countTareas = \App\TaskUser::where('user_id', $user->id ?? 0)
        ->join('tasks', 'task_user.task_id', '=', 'tasks.id')
        ->where('tasks.estado_tarea', '!=', 3)
        ->count();
    $countProyectos = \App\Project::count();
    $countClientes = \App\Customer::count();
@endphp

<div class="menu-header">
    <header class="menu-logo">
        <h1><a href="{{ route('eunomia.home') }}">Horae</a></h1>
        <img src="{{ asset('images/logo_mglab_box.png') }}" alt="Logo mg.lab" class="logo-image">
    </header>

    <nav class="menu-horizontal">
        {{-- Usuarios --}}
        @if($user && ($user->compruebaSeguridad('mostrar-usuarios') || $user->compruebaSeguridad('crear-usuario')))
        <div class="menu-group">
            <a href="#" class="group-title">Usuarios</a>
            <ul>
                @if($user->compruebaSeguridad('mostrar-usuarios'))
                <li><a href="{{ route('users.index') }}"><i class="fas fa-fw fa-users"></i> Usuarios</a></li>
                @endif
                @if($user->compruebaSeguridad('crear-usuario'))
                <li><a href="{{ route('users.create') }}"><i class="fas fa-fw fa-user-plus"></i> Añadir Usuario</a></li>
                @endif
            </ul>
        </div>
        @endif

        {{-- Clientes --}}
        @if($user && ($user->compruebaSeguridad('mostrar-clientes') || $user->compruebaSeguridad('mostrar-proyectos') || $user->compruebaSeguridad('mostrar-tareas')))
        <div class="menu-group">
            <a href="#"  class="group-title">Listados</a>
            <ul>
                @if($user->compruebaSeguridad('mostrar-clientes'))
                <li>
                    <a href="{{ route('customers.index') }}">
                        <i class="fas fa-fw fa-building"></i> Listado Clientes
                        @if($countClientes > 0)
                            <span class="badge badge-info">{{ $countClientes }}</span>
                        @endif
                    </a>
                </li>
                @endif
                @if($user->compruebaSeguridad('mostrar-proyectos'))
                <li>
                    <a href="{{ route('projects.index') }}">
                        <i class="fas fa-fw fa-project-diagram"></i> Listado Proyectos
                        @if($countProyectos > 0)
                            <span class="badge badge-success">{{ $countProyectos }}</span>
                        @endif
                    </a>
    
                    
                </li>
                @endif
                @if($user->compruebaSeguridad('mostrar-tareas'))
                <li>
                    <a href="{{ route('tasks.index') }}">
                        <i class="fas fa-fw fa-tasks"></i> Listado Tareas
                        @if($countTareas > 0)
                            <span class="badge badge-danger">{{ $countTareas }}</span>
                        @endif
                    </a>

                </li>
                @endif
            </ul>
        </div>
        @endif

        {{-- Calendario --}}
        @if($user && $user->compruebaSeguridad('mostrar-calendario'))
        <div class="menu-group">
            <a href="{{ route('calendar.index') }}"  class="group-title">Calendario</a>

        </div>
        @endif

        {{-- Vacaciones --}}
        @if($user && ($user->compruebaSeguridad('mostrar-vacaciones') || $user->compruebaSeguridad('mostrar-dias-festivos')))
        <div class="menu-group">
            <a href="{{ route('holiday_days.index') }}"  class="group-title">Vacaciones</a>
        </div>
        @endif

        {{-- Nuevo Proyecto --}}
        @if($user && $user->compruebaSeguridad('crear-proyecto'))
        <div class="menu-group mastareas">
            <a href="{{ route('projects.create') }}"  class="group-title">Nuevo Proyecto</a>
            <div class="tarea-btn-nuevaTarea">
                <a href="{{ route('projects.create') }}"><button><i class="fa fa-plus"></i></button></a>
            </div>
        </div>
        @endif

        {{-- Nueva Tarea --}}
        @if($user && $user->compruebaSeguridad('crear-tarea'))
        <div class="menu-group mastareas">
            <a href="{{ route('tasks.create') }}"  class="group-title">Nueva Tarea</a>
            <div class="tarea-btn-nuevaTarea">
                <a href="{{ route('tasks.create') }}"><button><i class="fa fa-plus"></i></button></a>
            </div>
           
        </div>
        @endif

        {{-- Fichajes: visible para cualquier usuario que no sea cliente --}}
        @if($user && ($user->compruebaSeguridad('mostrar-fichajes') || !$user->isRole('cliente')))
        <div class="menu-group">
            <a href="{{ route('fichajes.index') }}" class="group-title">Fichajes</a>
        </div>
        @endif

        {{-- Configuración --}}
        @if($user && ($user->compruebaSeguridad('mostrar-modulos') || $user->compruebaSeguridad('mostrar-permisos') || $user->compruebaSeguridad('mostrar-roles') || $user->compruebaSeguridad('mostrar-control-de-accesos')))
        <div class="menu-group">
            <a href="{{ route('modulos.index') }}"  class="group-title">Configuración</a>
            <ul>
                @if($user->compruebaSeguridad('mostrar-modulos'))
                <li><a href="{{ route('modulos.index') }}"><i class="fas fa-fw fa-cube"></i> Módulos</a></li>
                @endif
                @if($user->compruebaSeguridad('mostrar-permisos'))
                <li><a href="{{ route('permisos.index') }}"><i class="fas fa-fw fa-key"></i> Permisos</a></li>
                @endif
                @if($user->compruebaSeguridad('mostrar-roles'))
                <li><a href="{{ route('roles.index') }}"><i class="fas fa-fw fa-user-tag"></i> Roles</a></li>
                @endif
                @if($user->compruebaSeguridad('mostrar-roles'))
                <li><a href="{{ route('roles.matrix') }}"><i class="fas fa-fw fa-user-shield"></i> Asignar Permisos a Roles</a></li>
                @endif
                @if($user->compruebaSeguridad('mostrar-control-de-accesos'))
                <li><a href="{{ route('control_accesos.index') }}"><i class="fas fa-fw fa-lock"></i> Control de Acceso</a></li>
                @endif
                @if($user->compruebaSeguridad('mostrar-dias-festivos'))
                <li><a href="{{ route('party_days.index') }}"><i class="fas fa-fw fa-calendar-day"></i> Días Festivos</a></li>
                @endif
            </ul>
        </div>
        @endif
    </nav>

    {{-- Sección de perfil --}}
    <div class="menu-perfil">
        <img src="{{ $userPhoto }}" alt="User">
        <div class="seccion-perfil">
            <span class="group-title perfil-toggle">{{ e($userName) }} <i class="fas fa-chevron-down"></i></span>
            <ul class="perfil-submenu">
                @if($user)
                <li><a href="{{ route('users.edit', $user->id) }}"><i class="fas fa-fw fa-user-edit"></i> Mi Perfil</a></li>
                <li><a href="{{ route('users.password') }}"><i class="fas fa-fw fa-lock"></i> Cambiar Contraseña</a></li>
                @endif
                <li>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: white; cursor: pointer; padding: 0; font: inherit;">
                            <i class="fas fa-fw fa-sign-out-alt"></i> Cerrar Sesión
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div><!-- Fin del menú horizontal -->
