@if ($project->user)
    <div class="usuario-mglab">
        <div class="header-userProject">
            <div class="logo-userProject">
                <img src="{{ asset('images/logo_mglab_box.png') }}" alt="Logo Mg_Lab">
            </div>
            <div class="usuario-userProject">
                <img src="{{ asset('images/avatar/' . ($project->user->avatar ?? 'default.png')) }}" alt="{{ $project->user->nombre_completo }}">
                <div class="usuario-userProjectDepartamento">
                    <h3>Usuario: {{ $project->user->nombre_completo }}</h3>
                    <h4>Departamento: {{ optional($project->user->departamento)->role_name ?? 'Sin departamento' }}</h4>
           </div>    
            
            </div>
            
        </div>
        <div class="footer-userProject">
            <h5>Proyectos Activos:{{ $project->user->nactive_projects() }}</h5>
            <h5>Tareas Activas: {{ $project->user->nactive_tasks() }}</h5>
            <h5>Comentarios: {{ $project->user->ncomentarios() }}</h5>
        </div>
    </div>
@else
    <div class="usuario-mglab">
        <div class="header-userProject">
            <div class="logo-userProject">
                <img src="{{ asset('images/logo_mglab_box.png') }}" alt="Logo Mg_Lab">
            </div>
            <div class="usuario-userProject">
                <img src="{{ asset('images/avatar/default.png') }}" alt="Usuario sin asignar">
                <h3>Usuario: Sin asignar</h3>
            </div>
            <h4>Departamento: Sin departamento</h4>
        </div>
        <div class="footer-userProject">
            <h5>Proyectos Activos: 0</h5>
            <h5>Tareas Activas: 0</h5>
            <h5>Comentarios: 0</h5>
        </div>
    </div>
@endif
