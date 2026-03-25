<div class="project-form-container">
    <div class="form-header">
        <h3 class="card-title">Datos del proyecto</h3>
    </div>
    <form action="{{ route('projects.update', $project) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="form-body">
            <div class="form-group">
                <label for="customer_id" class="form-label">Cliente</label>
                <select name="customer_id" id="customer_id" class="form-control">
                    <option value="">Selecciona un cliente</option>
                    @foreach ($customers as $id => $customerName)
                        <option value="{{ $id }}"
                            {{ old('customer_id', $project->customer_id) == $id ? 'selected' : '' }}>{{ $customerName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="user_id" class="form-label">Responsable en mg.lab</label>
                <select name="user_id" id="user_id" class="form-control">
                    <option value="">Selecciona un responsable</option>
                    @foreach ($users as $id => $userName)
                        <option value="{{ $id }}"
                            {{ old('user_id', $project->user_id) == $id ? 'selected' : '' }}>{{ $userName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group full-width">
                <label for="titulo_proyecto" class="form-label">Nombre de Proyecto</label>
                <input type="text" name="titulo_proyecto" id="titulo_proyecto" class="form-control"
                    placeholder="Nombre del proyecto" value="{{ old('titulo_proyecto', $project->titulo_proyecto) }}">
            </div>

            <div class="form-group">
                <label for="fechaentrega_proyecto" class="form-label">Fecha de entrega</label>
                <input type="text" name="fechaentrega_proyecto" id="fechaentrega_proyecto" class="form-control"
                    value="{{ old('fechaentrega_proyecto', $project->fechaentrega_proyecto) }}">
            </div>

            <div class="form-group">
                <label for="estado_proyecto" class="form-label">Estado de proyecto</label>
                <select name="estado_proyecto" id="estado_proyecto" class="form-control">
                    <option value="">Selecciona un estado</option>
                    <option value="1"
                        {{ old('estado_proyecto', $project->estado_proyecto) == 1 ? 'selected' : '' }}>En proceso
                    </option>
                    <option value="2"
                        {{ old('estado_proyecto', $project->estado_proyecto) == 2 ? 'selected' : '' }}>En espera
                    </option>
                    <option value="3"
                        {{ old('estado_proyecto', $project->estado_proyecto) == 3 ? 'selected' : '' }}>Para Facturar
                    </option>
                    <option value="4"
                        {{ old('estado_proyecto', $project->estado_proyecto) == 4 ? 'selected' : '' }}>Cerrado</option>
                </select>
            </div>

            @if (\Illuminate\Support\Str::startsWith($project->codigo_proyecto, 'SER_'))
                <div class="form-group full-width">
                    <label for="solicitado_nfs" class="form-label">Nº Orden de trabajo</label>
                    <div class="form-check">
                        <input type="checkbox" name="solicitado_nfs" id="solicitado_nfs" class="form-check-input"
                            value="1"
                            {{ $project->solicitado_nfs != '' && $project->solicitado_nfs != 'No solicitado' ? 'checked' : '' }}>
                        <label for="solicitado_nfs" class="form-check-label">Nº orden solicitado</label>
                    </div>
                    <input type="text" name="n_factura" id="n_factura" class="form-control" placeholder="Nº Factura"
                        value="{{ $project->solicitado_nfs != '' && $project->solicitado_nfs != 'No solicitado' && $project->solicitado_nfs != 'Solicitado' ? $project->solicitado_nfs : '' }}">
                </div>
            @endif

            <div class="form-group full-width">
                <label for="comentario_proyecto" class="form-label">Comentarios</label>
                <textarea name="comentario_proyecto" id="comentario_proyecto" class="form-control"
                    placeholder="Comentarios sobre el proyecto" rows="4">{{ old('comentario_proyecto', $project->comentario_proyecto) }}</textarea>
            </div>

            <div class="form-group full-width">
                <label for="web_preview" class="form-label">Web preview</label>
                <input type="file" name="web_preview" id="web_preview" class="form-control">

                <div class="preview-section mt-3">
                    <small class="form-text text-muted">Vista previa actual: </small>
                    @if ($project->customer && $project->customer->slug && $project->slug)
                        <img src="https://mglab.es/images/clientes/previsualiza/{{ $project->customer->slug }}/{{ $project->slug }}/{{ $project->web_preview ?: 'sinimagen.jpg' }}"
                            class="img-fluid mt-2" alt="{{ $project->customer->nombre_cliente ?? 'Cliente' }}">
                        <p><strong>Link:</strong></p>
                        <a target="_blank"
                            href="https://mglab.es/images/clientes/previsualiza/{{ $project->customer->slug }}/{{ $project->slug }}/{{ explode('.', $project->web_preview)[0] }}">
                            https://mglab.es/images/clientes/previsualiza/{{ $project->customer->slug }}/{{ $project->slug }}/{{ explode('.', $project->web_preview)[0] }}
                        </a>
                    @else
                        <p class="text-muted">Vista previa no disponible - faltan datos del cliente o proyecto.</p>
                    @endif
                </div>
            </div>

        </div>

        <div class="form-footer">
            <button type="submit" class="btn btn-submit">Guardar cambios</button>
            <a href="{{ route('projects.index') }}" class="btn btn-cancel">Cancelar</a>
        </div>

    </form>
</div>
