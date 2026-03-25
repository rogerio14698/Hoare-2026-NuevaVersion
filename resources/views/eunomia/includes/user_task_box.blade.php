<div class="usuario-mglab">
    <div class="header-userProject">
        <div class="logo-userProject">
            <img src="{{ asset('images/logo_mglab_box.png') }}" alt="Logo Mg_Lab">
        </div>

        @if($task->users->count() > 0)
            {{-- Bloque para cuando hay uno o más responsables --}}
            <div class="usuario-userProject">
                <div class="d-flex flex-wrap">
                    @foreach($task->users as $usuario)
                        <div class="user-item-wrapper" style="margin-right: 15px; margin-bottom: 10px;">
                            <img src="{{ asset('images/avatar/' . ($usuario->avatar ?? 'default.png')) }}" 
                                 alt="{{ $usuario->nombre_completo }}">
                            <div class="usuario-userProjectDepartamento">
                                <h3>{{ $usuario->nombre_completo }}</h3>
                                <h4>{{ optional($usuario->departamento)->role_name ?? 'Sin departamento' }}</h4>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            {{-- Bloque para cuando NO hay nadie asignado --}}
            <div class="usuario-userProject">
                <img src="{{ asset('images/avatar/default.png') }}" alt="Sin asignar">
                <div class="usuario-userProjectDepartamento">
                    <h3>Usuario: Sin asignar</h3>
                    <h4>Departamento: Sin departamento</h4>
                </div>
            </div>
        @endif
    </div>

    <div class="footer-userProject">
        @if($task->users->count() == 1)
            {{-- Si solo hay uno, mostramos sus estadísticas reales --}}
            @php $singleUser = $task->users->first(); @endphp
            <h5>Proyectos Activos: {{ $singleUser->nactive_projects() }}</h5>
            <h5>Tareas Activas: {{ $singleUser->nactive_tasks() }}</h5>
            <h5>Comentarios: {{ $singleUser->ncomentarios() }}</h5>
        @else
            {{-- Si hay varios o ninguno, mostramos datos generales --}}
            <h5>Responsables asignados: {{ $task->users->count() }}</h5>
            <h5>Estado tarea: {{ $task->taskstate->state ?? 'Pendiente' }}</h5>
        @endif
    </div>
</div>