<div class="project-form-container">
    <div class="form-header">
        <h3 class="card-title">Datos de la Tarea</h3>
    </div>

    <form action="{{ route('tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="form-body">
            {{-- Proyecto --}}
            <div class="form-group">
                <label for="project_id" class="form-label">Proyecto</label>
                <select name="project_id" id="project_id" class="form-control">
                    <option value="">Selecciona un proyecto</option>
                    @foreach($projects as $id => $projectTitle)
                        <option value="{{ $id }}" {{ old('project_id', $task->project_id) == $id ? 'selected' : '' }}>
                            {{ $projectTitle }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Responsables (Select2 Múltiple) --}}
            <div class="form-group">
                <label for="user_id" class="form-label">Responsables en mg.lab</label>
                <select name="user_id[]" id="user_id" class="select2 form-control" multiple data-placeholder="Selecciona responsables">
                    @foreach($users as $id => $user)
                        <option value="{{ $id }}" {{ (collect(old('user_id', $myusers))->contains($id)) ? 'selected' : '' }}>
                            {{ $user }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Nombre de la Tarea --}}
            <div class="form-group full-width">
                <label for="titulo_tarea" class="form-label">Nombre de tarea</label>
                <input type="text" name="titulo_tarea" id="titulo_tarea" class="form-control" 
                       placeholder="Nombre de tarea" value="{{ old('titulo_tarea', $task->titulo_tarea) }}">
            </div>

            {{-- Fila: Inicio (Fecha y Hora) --}}
            <div class="form-group">
                <label for="fechainicio_tarea" class="form-label">Fecha de inicio</label>
                <input type="text" name="fechainicio_tarea" id="fechainicio_tarea" class="form-control" 
                       value="{{ old('fechainicio_tarea', $fechatareaoriginalinicio) }}">
            </div>
            <div class="form-group">
                <label for="horanicio_tarea" class="form-label">Hora de inicio</label>
                <input type="text" name="horanicio_tarea" id="horanicio_tarea" class="form-control" 
                       value="{{ old('horanicio_tarea', $horatareaoriginalinicio) }}">
            </div>

            {{-- Fila: Entrega (Fecha y Hora) --}}
            <div class="form-group">
                <label for="fechaentrega_tarea" class="form-label">Fecha de entrega</label>
                <input type="text" name="fechaentrega_tarea" id="fechaentrega_tarea" class="form-control" 
                       value="{{ old('fechaentrega_tarea', $fechatareaoriginalentrega) }}">
            </div>
            <div class="form-group">
                <label for="horaentrega_tarea" class="form-label">Hora de entrega</label>
                <input type="text" name="horaentrega_tarea" id="horaentrega_tarea" class="form-control" 
                       value="{{ old('horaentrega_tarea', $horatareaoriginalentrega) }}">
            </div>

            {{-- Estado de la Tarea --}}
            <div class="form-group">
                <label for="estado_tarea" class="form-label">Estado de tarea</label>
                <select name="estado_tarea" id="estado_tarea" class="form-control">
                    @foreach($task_states as $id => $state)
                        <option value="{{ $id }}" {{ old('estado_tarea', $task->estado_tarea) == $id ? 'selected' : '' }}>
                            {{ $state }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Detalles / Comentarios --}}
            <div class="form-group full-width">
                <label for="comentario_tarea" class="form-label">Detalles</label>
                <textarea name="comentario_tarea" id="comentario_tarea" class="form-control" 
                          placeholder="Detalles de la tarea" rows="4">{{ old('comentario_tarea', $task->comentario_tarea) }}</textarea>
            </div>

            <input type="hidden" name="previous" value="{{ URL::previous() }}">
        </div>

        <div class="form-footer">
            <button type="submit" class="btn-accion">Guardar cambios</button>
            <a href="{{ URL::previous() }}" class="btn-accion">Cancelar</a>
        </div>
    </form>
</div>