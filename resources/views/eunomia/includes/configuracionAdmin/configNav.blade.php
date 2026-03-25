<div class="d-flex" style="gap: 8px;">
      @if(\Auth::user()->compruebaSeguridad('mostrar-elementos-menu-admin') == true)
        <a href="{{route('menu_admin')}}" class="btn-nueva-tarea">Menú Admin</a>
      @endif
      @if(\Auth::user()->compruebaSeguridad('crear-modulo') == true)
        <a href="{{route('modulos.create')}}" class="btn-nueva-tarea"> Nuevo Módulo</a>
      @endif
      @if(\Auth::user()->compruebaSeguridad('mostrar-permisos') == true)
        <a href="{{ route('permisos.index') }}" class="btn-nueva-tarea">Ver Permisos</a>
      @endif
      @if(\Auth::user()->compruebaSeguridad('mostrar-roles') == true)
        <a href="{{route('roles.index')}}" class="btn-nueva-tarea"> Ver Roles</a> 
      @endif
      @if(\Auth::user()->compruebaSeguridad('mostrar-permisos') == true)
        <a href="{{ route('roles.matrix') }}" class="btn-nueva-tarea">Matriz de Roles</a>
        <a href="{{ route('control_accesos.index') }}" class="btn-nueva-tarea">Control de Accesos</a>
      @endif
    </div>