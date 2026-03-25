<form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data" id="formulario_proyectos">
    @csrf
    <div class="project-form-container">
        <div class="form-header">
            <h2 class="fuenteTituloBlanco">{{ $action ? 'Editar Proyecto -modal?' : 'Crear Nuevo Proyecto' }}</h2>
        </div>
        <div class="form-body">
            @if(!$action)
            <div class="form-group full-width">
                <label for="customer_id" class="form-label fuente-blanco">Cliente</label>
                <div class="form-field-with-button">
                    <select name="customer_id" id="customer_id" class="form-control form-field-flexible">
                        <option value="">Selecciona un cliente</option>
                        @foreach($customers as $id => $customerName)
                            <option value="{{ $id }}" {{ old('customer_id') == $id ? 'selected' : '' }}>{{ $customerName }}</option>
                        @endforeach
                    </select>
                    <button type="button" id="aniade_cliente" class="btn-accion form-field-button">Añadir</button>
                </div>
            </div>
            @else
                <div class="form-group full-width">
                    <label for="customer" class="form-label">Cliente</label>
                    <input type="text" class="form-control" id="customer" value="{{ $customer->codigo_cliente . '_' . $customer->nombre_cliente }}" disabled>
                    <input type="hidden" name="customer_id" id="customer_id" value="{{ $customer->id }}">
                </div>
            @endif

            <div class="form-group">
                <label for="user_id" class="form-label">Responsable en mg.lab</label>
                <select name="user_id" id="user_id" class="form-control">
                    <option value="">Selecciona un responsable</option>
                    @foreach($users as $id => $userName)
                        <option value="{{ $id }}" {{ old('user_id') == $id ? 'selected' : '' }}>{{ $userName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="titulo_proyecto" class="form-label">Nombre de Proyecto</label>
                <input type="text" name="titulo_proyecto" id="titulo_proyecto" class="form-control" placeholder="Nombre del proyecto" value="{{ old('titulo_proyecto') }}">
            </div>

            <div class="form-group">
                <label for="fechaentrega_proyecto" class="form-label">Fecha de entrega</label>
                <input type="text" name="fechaentrega_proyecto" id="fechaentrega_proyecto" class="form-control" placeholder="DD/MM/YYYY" value="{{ old('fechaentrega_proyecto') }}">
            </div>

            <div class="form-group">
                <label for="estado_proyecto" class="form-label">Estado de proyecto</label>
                <select name="estado_proyecto" id="estado_proyecto" class="form-control">
                    <option value="">Selecciona un estado</option>
                    <option value="1" {{ old('estado_proyecto') == 1 ? 'selected' : '' }}>En proceso</option>
                    <option value="2" {{ old('estado_proyecto') == 2 ? 'selected' : '' }}>En espera</option>
                    <option value="3" {{ old('estado_proyecto') == 3 ? 'selected' : '' }}>Para Facturar</option>
                    <option value="4" {{ old('estado_proyecto') == 4 ? 'selected' : '' }}>Cerrado</option>
                </select>
            </div>

            <div class="form-group full-width">
                <label for="comentario_proyecto" class="form-label">Comentarios</label>
                <textarea name="comentario_proyecto" id="comentario_proyecto" class="form-control" placeholder="Comentarios sobre el proyecto" rows="4">{{ old('comentario_proyecto') }}</textarea>
            </div>
            
            <input type="hidden" name="action" value="{{ $action }}">
            <input type="hidden" name="role_id" value="{{ Auth::user()->role_id }}">

            <div class="form-actions full-width">
                <button type="submit" class="btn-accion">Guardar</button>
                <a href="{{ route('projects.index') }}" class="btn-accion">Cancelar</a>
            </div>
        </div>
    </div>

</form>