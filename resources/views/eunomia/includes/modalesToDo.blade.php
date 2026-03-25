<!-- Create new item Modal -->
<div class="modal fade" id="newModal" tabindex="-1" aria-labelledby="newModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('todo.store') }}" method="POST" class="row g-3">
                @csrf
                <div class="modal-header" style="background-color: #3C8DBC; color: #FFF;">
                    <h5 class="modal-title" id="newModalLabel">
                        <i class="fas fa-plus me-2"></i>Nueva tarea
                    </h5>
                    <button type="button" class="close btn-close-white" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="titulo_tarea_new" class="form-label fw-bold">Título</label>
                        <input type="text" name="titulo_tarea" class="form-control" placeholder="Título de la tarea"
                            id="titulo_tarea_new" value="{{ old('titulo_tarea') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="fechaentrega_tarea_new" class="form-label fw-bold">Fecha de entrega</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            <input type="text" name="fechaentrega_tarea" class="form-control datepicker"
                                id="fechaentrega_tarea_new" value="{{ old('fechaentrega_tarea') }}"
                                placeholder="dd/mm/yyyy" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="horaentrega_tarea_new" class="form-label fw-bold">Hora de entrega</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            <input type="text" name="horaentrega_tarea" class="form-control timepicker"
                                id="horaentrega_tarea_new" value="{{ old('horaentrega_tarea') }}"
                                placeholder="HH:MM" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="comentario_tarea_new" class="form-label fw-bold">Comentarios</label>
                        <textarea name="comentario_tarea" class="form-control" rows="3" id="comentario_tarea_new"
                            placeholder="Comentarios adicionales...">{{ old('comentario_tarea') }}</textarea>
                    </div>
                </div>

                <input type="hidden" name="role_id" value="{{ Auth::user()->role_id }}">
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        id="btn-cancelar-new">Cancelar</button>
                    <button type="submit" class="btn btn-primary"
                        style="background-color: #3C8DBC; border-color: #3C8DBC;">
                        <i class="fas fa-plus me-2"></i>Crear tarea
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Fin del modal de creación -->

<!-- Edit item Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('todo.update') }}" method="POST" class="row g-3" id="form_edit">
                @csrf
                <div class="modal-header" style="background-color: #3C8DBC; color: #FFF;">
                    <h5 class="modal-title" id="editModalLabel">
                        <i class="fas fa-edit me-2"></i>Editar tarea
                    </h5>
                    <button type="button" class="close btn-close-white" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="titulo_tarea_edit" class="form-label fw-bold">Título</label>
                        <input type="text" name="titulo_tarea" class="form-control"
                            placeholder="Título de la tarea" id="titulo_tarea_edit"
                            value="{{ old('titulo_tarea') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="fechaentrega_tarea_edit" class="form-label fw-bold">Fecha de entrega</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            <input type="text" name="fechaentrega_tarea" class="form-control datepicker"
                                id="fechaentrega_tarea_edit" value="{{ old('fechaentrega_tarea') }}"
                                placeholder="dd/mm/yyyy" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="horaentrega_tarea_edit" class="form-label fw-bold">Hora de entrega</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            <input type="text" name="horaentrega_tarea" class="form-control timepicker"
                                id="horaentrega_tarea_edit" value="{{ old('horaentrega_tarea') }}"
                                placeholder="HH:MM" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="comentario_tarea_edit" class="form-label fw-bold">Comentarios</label>
                        <textarea name="comentario_tarea" class="form-control" rows="3" id="comentario_tarea_edit"
                            placeholder="Comentarios adicionales...">{{ old('comentario_tarea') }}</textarea>
                    </div>
                </div>

                <input type="hidden" name="role_id" value="{{ Auth::user()->role_id }}">
                <input type="hidden" name="id" id="edit_task_id">
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btn-cancelar-edit">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary"
                        style="background-color: #3C8DBC; border-color: #3C8DBC;">
                        <i class="fas fa-save me-2"></i>Actualizar tarea
                    </button>
                    <button type="button" class="btn btn-danger" id="btn-delete-edit" data-toggle="modal"
                        data-target="#deleteModal">
                        <i class="fas fa-trash me-2"></i>Eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Fin del modal de edición -->

<!-- Delete item Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('todo.delete') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-trash-alt me-2"></i>Eliminar tarea
                    </h5>
                    <button type="button" class="close btn-close-white" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                        <p class="mb-0 fs-5">¿Está seguro de que desea eliminar esta tarea?</p>
                        <p class="text-muted">Esta acción no se puede deshacer.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btn-cancelar-delete">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <input type="hidden" name="delete_id" id="postvalue" value="" />
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--Fin del modal -->